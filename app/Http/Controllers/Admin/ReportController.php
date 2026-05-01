<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\HotelBranch;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Models\User;
use App\Support\StaffScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const TYPES = ['summary', 'bookings', 'customers', 'rooms', 'maintenance', 'full'];

    public function index(Request $request): View
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to = $request->date('to') ?? now();
        $data = $this->overviewDashboard($from, $to);

        return view('admin.reports.index', $data);
    }

    /**
     * @return array<string, mixed>
     */
    private function overviewDashboard(Carbon $from, Carbon $to): array
    {
        $revenueToday = (float) Booking::where('status', BookingStatus::Confirmed)
            ->whereDate('created_at', now())
            ->sum('total_amount');

        $revenueThisMonth = (float) Booking::where('status', BookingStatus::Confirmed)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $revenueLastMonth = (float) Booking::where('status', BookingStatus::Confirmed)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');

        $revenueYear = (float) Booking::where('status', BookingStatus::Confirmed)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $percentageChange = 0;
        if ($revenueLastMonth > 0) {
            $percentageChange = (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
        }

        $revenueTrend = Booking::where('status', BookingStatus::Confirmed)
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $paymentByMethod = Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->join('booking_methods', 'bookings.booking_method_id', '=', 'booking_methods.id')
            ->selectRaw('booking_methods.name as method_name, SUM(bookings.total_amount) as total')
            ->groupBy('booking_methods.id', 'booking_methods.name')
            ->get();

        $cashRevenue = (float) Booking::where('status', BookingStatus::Confirmed)
            ->whereHas('method', fn ($m) => $m->where('slug', 'cash'))
            ->sum('total_amount');

        $nonCashRevenue = (float) Booking::where('status', BookingStatus::Confirmed)
            ->whereDoesntHave('method', fn ($m) => $m->where('slug', 'cash'))
            ->sum('total_amount');

        $maintenanceExpenses = (float) RoomMaintenance::sum('expenses');

        $totalRooms = Room::count();
        $bookedRoomsCount = Booking::where('status', BookingStatus::Confirmed)
            ->whereDate('check_in', '<=', now())
            ->whereDate('check_out', '>', now())
            ->distinct('room_id')
            ->count('room_id');

        $availableRoomsCount = max(0, $totalRooms - $bookedRoomsCount);

        $maintenanceStats = RoomMaintenance::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();

        $roomDistribution = Room::join('hotel_branches', 'rooms.hotel_branch_id', '=', 'hotel_branches.id')
            ->selectRaw('hotel_branches.name as branch_name, count(*) as total')
            ->groupBy('branch_name')
            ->get();

        return compact(
            'revenueToday',
            'revenueThisMonth',
            'revenueYear',
            'revenueLastMonth',
            'percentageChange',
            'from',
            'to',
            'revenueTrend',
            'paymentByMethod',
            'cashRevenue',
            'nonCashRevenue',
            'maintenanceExpenses',
            'bookedRoomsCount',
            'availableRoomsCount',
            'maintenanceStats',
            'roomDistribution'
        );
    }

public function show(Request $request, string $type): View
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();
        if ($from->gt($to)) {
            [$from, $to] = [$to->copy(), $from->copy()];
        }

        $statusFilter = $request->query('status');
        if ($statusFilter !== null && $statusFilter !== '' && ! BookingStatus::tryFrom((string) $statusFilter)) {
            $statusFilter = null;
        }

        $exportQuery = array_filter([
            'type' => $type === 'full' ? 'full' : $type,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'status' => $statusFilter,
        ], fn ($v) => $v !== null && $v !== '');

        $exportUrl = route('admin.reports.export', $exportQuery);

        if ($type === 'full') {
            return view('admin.reports.show', $this->fullDashboard($from, $to, $exportUrl));
        }

        if ($type === 'summary') {
            return view('admin.reports.show', $this->summaryDashboard($from, $to, $exportUrl));
        }

        if ($type === 'bookings') {
            return view('admin.reports.show', $this->bookingsDashboard($from, $to, $statusFilter, $exportUrl));
        }

        if ($type === 'customers') {
            return view('admin.reports.show', $this->customersDashboard($from, $to, $exportUrl));
        }

        if ($type === 'rooms') {
            return view('admin.reports.show', $this->roomsDashboard($from, $to, $exportUrl));
        }

        return view('admin.reports.show', $this->maintenanceDashboard($from, $to, $exportUrl));
    }

    public function export(Request $request): StreamedResponse|Response
    {
        $type = $request->query('type', 'summary');
        abort_unless(in_array($type, self::TYPES, true), 404);

        $from = $request->date('from');
        $to = $request->date('to');
        $format = strtolower((string) $request->query('format', 'csv'));
        $statusFilter = $request->query('status');
        if ($statusFilter !== null && $statusFilter !== '' && ! BookingStatus::tryFrom((string) $statusFilter)) {
            $statusFilter = null;
        }

        if ($format === 'pdf') {
            if ($request->query('dashboard') === 'overview') {
                $pdfFrom = $from ?? now()->startOfMonth();
                $pdfTo = $to ?? now();

                return response()->view('admin.reports.pdf-overview', $this->overviewDashboard($pdfFrom, $pdfTo));
            }

            $pdfFrom = $from ?? now()->subDays(30);
            $pdfTo = $to ?? now();
            if ($pdfFrom->gt($pdfTo)) {
                [$pdfFrom, $pdfTo] = [$pdfTo->copy(), $pdfFrom->copy()];
            }

            $reportData = match ($type) {
                'full' => $this->fullDashboard($pdfFrom, $pdfTo, ''),
                'summary' => $this->summaryDashboard($pdfFrom, $pdfTo, ''),
                'bookings' => $this->bookingsDashboard($pdfFrom, $pdfTo, $statusFilter, ''),
                'customers' => $this->customersDashboard($pdfFrom, $pdfTo, ''),
                'rooms' => $this->roomsDashboard($pdfFrom, $pdfTo, ''),
                default => $this->maintenanceDashboard($pdfFrom, $pdfTo, ''),
            };

            return response()->view('admin.reports.pdf', $reportData);
        }

        $filename = 'hotel-report-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($type, $from, $to, $statusFilter): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            if ($type === 'bookings' || $type === 'full') {
                fputcsv($out, ['=== BOOKINGS ===']);
                fputcsv($out, ['Reference', 'Status', 'Email', 'Room', 'Total', 'Created']);
                $q = Booking::query()->with('room')->orderByDesc('id');
                $this->applyBookingExportFilters($q, $from, $to, $statusFilter);
                $q->chunk(500, function ($rows) use ($out): void {
                    foreach ($rows as $b) {
                        fputcsv($out, [
                            $b->public_reference,
                            $b->status->value,
                            $b->email,
                            $b->room?->name,
                            (string) $b->total_amount,
                            $b->created_at?->toDateTimeString(),
                        ]);
                    }
                });
                fputcsv($out, []);
            }

            if ($type === 'customers' || $type === 'full') {
                fputcsv($out, ['=== CUSTOMERS ===']);
                fputcsv($out, ['Name', 'Email', 'Phone', 'Active', 'Last booking']);
                $q = Customer::query()->orderByDesc('id');
                $this->applyDateRange($q, 'created_at', $from, $to);
                $q->chunk(500, function ($rows) use ($out): void {
                    foreach ($rows as $c) {
                        fputcsv($out, [
                            $c->first_name.' '.$c->last_name,
                            $c->email,
                            $c->phone,
                            $c->is_active ? '1' : '0',
                            $c->last_booking_at?->toDateTimeString(),
                        ]);
                    }
                });
                fputcsv($out, []);
            }

            if ($type === 'rooms' || $type === 'full') {
                fputcsv($out, ['=== ROOMS ===']);
                fputcsv($out, ['ID', 'Name', 'Room #', 'Branch', 'Floor', 'Status', 'Price']);
                Room::query()->with('branch')->orderBy('id')->chunk(500, function ($rows) use ($out): void {
                    foreach ($rows as $r) {
                        fputcsv($out, [
                            $r->id,
                            $r->name,
                            $r->room_number,
                            $r->branch?->name,
                            $r->floor_number,
                            $r->status->value,
                            (string) $r->price,
                        ]);
                    }
                });
                fputcsv($out, []);
            }

            if ($type === 'maintenance' || $type === 'full') {
                fputcsv($out, ['=== MAINTENANCE ===']);
                fputcsv($out, ['Room', 'Branch', 'Kind', 'Status', 'Expenses', 'Due', 'Started']);
                $q = RoomMaintenance::query()->with(['room', 'branch'])->orderByDesc('id');
                $this->applyDateRange($q, 'created_at', $from, $to);
                $q->chunk(500, function ($rows) use ($out): void {
                    foreach ($rows as $m) {
                        fputcsv($out, [
                            $m->room?->name,
                            $m->branch?->name,
                            $m->kind->value,
                            $m->status->value,
                            (string) $m->expenses,
                            $m->due_at?->toDateTimeString(),
                            $m->started_at?->toDateTimeString(),
                        ]);
                    }
                });
                fputcsv($out, []);
            }

            if ($type === 'summary') {
                fputcsv($out, ['Metric', 'Value']);
                fputcsv($out, ['Bookings total', (string) Booking::query()->count()]);
                fputcsv($out, ['Bookings confirmed', (string) Booking::query()->where('status', BookingStatus::Confirmed)->count()]);
                fputcsv($out, ['Customers', (string) Customer::query()->count()]);
                fputcsv($out, ['Rooms', (string) Room::query()->count()]);
                fputcsv($out, ['Branches', (string) HotelBranch::query()->count()]);
                fputcsv($out, ['Users', (string) User::query()->count()]);
                $contactMessages = ContactMessage::query();
                app(StaffScope::class)->filterContactMessagesByBranch($contactMessages);
                fputcsv($out, ['Contact messages', (string) $contactMessages->count()]);
                fputcsv($out, ['Maintenance records', (string) RoomMaintenance::query()->count()]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function applyBookingExportFilters(Builder $q, ?Carbon $from, ?Carbon $to, ?string $status): void
    {
        $this->applyDateRange($q, 'created_at', $from, $to);
        if ($status && ($st = BookingStatus::tryFrom($status))) {
            $q->where('status', $st);
        }
    }

    private function applyDateRange(Builder $q, string $column, ?Carbon $from, ?Carbon $to): void
    {
        if ($from) {
            $q->whereDate($column, '>=', $from);
        }
        if ($to) {
            $q->whereDate($column, '<=', $to);
        }
    }

    private function dateExpr(string $column): string
    {
        return DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m-%d', {$column})"
            : "DATE({$column})";
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function dailyCounts(Builder $baseQuery, string $dateColumn, Carbon $from, Carbon $to): array
    {
        $expr = $this->dateExpr($dateColumn);
        $rows = (clone $baseQuery)
            ->whereBetween($dateColumn, [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw("{$expr} as d, COUNT(*) as c")
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $map = $rows->pluck('c', 'd');
        $labels = [];
        $values = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $labels[] = $d->format('M j');
            $values[] = (int) ($map[$key] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array<string, mixed>
     */
    private function summaryDashboard(Carbon $from, Carbon $to, string $exportUrl): array
    {
        $bookingBase = Booking::query();
        $chart = $this->dailyCounts($bookingBase, 'created_at', $from, $to);

        return [
            'reportType' => 'summary',
            'title' => __('Summary'),
            'from' => $from,
            'to' => $to,
            'exportUrl' => $exportUrl,
            'kpis' => [
                ['label' => __('Bookings'), 'value' => (string) Booking::query()->count()],
                ['label' => __('Confirmed'), 'value' => (string) Booking::query()->where('status', BookingStatus::Confirmed)->count()],
                ['label' => __('Customers'), 'value' => (string) Customer::query()->count()],
                ['label' => __('Rooms'), 'value' => (string) Room::query()->count()],
                ['label' => __('Branches'), 'value' => (string) HotelBranch::query()->count()],
                ['label' => __('Maintenance'), 'value' => (string) RoomMaintenance::query()->count()],
            ],
            'chartLabels' => $chart['labels'],
            'chartData' => $chart['values'],
            'chartLabel' => __('New bookings per day'),
            'tableHeaders' => [],
            'tableRows' => collect(),
            'statusFilter' => null,
            'bookingStatuses' => BookingStatus::cases(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function bookingsDashboard(Carbon $from, Carbon $to, ?string $statusFilter, string $exportUrl): array
    {
        $statusEnum = $statusFilter ? BookingStatus::tryFrom((string) $statusFilter) : null;

        $base = Booking::query();
        $this->applyDateRange($base, 'created_at', $from, $to);
        if ($statusEnum) {
            $base->where('status', $statusEnum);
        }

        $forChart = Booking::query();
        if ($statusEnum) {
            $forChart->where('status', $statusEnum);
        }
        $chart = $this->dailyCounts($forChart, 'created_at', $from, $to);

        $totalAmount = (clone $base)->sum('total_amount');
        $preview = (clone $base)->with('room')->latest()->limit(12)->get();

        $tableRows = $preview->map(fn (Booking $b) => [
            $b->public_reference,
            $b->status->value,
            $b->email,
            $b->room?->name ?? '—',
            (string) $b->total_amount,
            $b->created_at?->format('Y-m-d H:i') ?? '—',
        ]);

        return [
            'reportType' => 'bookings',
            'title' => __('Bookings report'),
            'from' => $from,
            'to' => $to,
            'exportUrl' => $exportUrl,
            'kpis' => [
                ['label' => __('In range'), 'value' => (string) (clone $base)->count()],
                ['label' => __('Revenue (total)'), 'value' => number_format((float) $totalAmount, 2)],
                ['label' => __('Pending payment'), 'value' => (string) Booking::query()->where('status', BookingStatus::PendingPayment)->count()],
            ],
            'chartLabels' => $chart['labels'],
            'chartData' => $chart['values'],
            'chartLabel' => __('Bookings per day'),
            'tableHeaders' => [__('Ref'), __('Status'), __('Email'), __('Room'), __('Total'), __('Created')],
            'tableRows' => $tableRows,
            'statusFilter' => $statusFilter,
            'bookingStatuses' => BookingStatus::cases(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function customersDashboard(Carbon $from, Carbon $to, string $exportUrl): array
    {
        $base = Customer::query();
        $this->applyDateRange($base, 'created_at', $from, $to);

        $chart = $this->dailyCounts(Customer::query(), 'created_at', $from, $to);

        $preview = (clone $base)->latest()->limit(12)->get();
        $tableRows = $preview->map(fn (Customer $c) => [
            $c->first_name.' '.$c->last_name,
            $c->email,
            $c->phone ?? '—',
            $c->is_active ? __('Yes') : __('No'),
            $c->created_at?->format('Y-m-d') ?? '—',
        ]);

        return [
            'reportType' => 'customers',
            'title' => __('Customers report'),
            'from' => $from,
            'to' => $to,
            'exportUrl' => $exportUrl,
            'kpis' => [
                ['label' => __('In range'), 'value' => (string) (clone $base)->count()],
                ['label' => __('Active'), 'value' => (string) Customer::query()->where('is_active', true)->count()],
                ['label' => __('All time'), 'value' => (string) Customer::query()->count()],
            ],
            'chartLabels' => $chart['labels'],
            'chartData' => $chart['values'],
            'chartLabel' => __('New customers per day'),
            'tableHeaders' => [__('Name'), __('Email'), __('Phone'), __('Active'), __('Created')],
            'tableRows' => $tableRows,
            'statusFilter' => null,
            'bookingStatuses' => BookingStatus::cases(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function roomsDashboard(Carbon $from, Carbon $to, string $exportUrl): array
    {
        $branchNames = HotelBranch::query()->pluck('name', 'id');

        $byBranchRows = DB::table('rooms')
            ->select('hotel_branch_id', DB::raw('COUNT(*) as c'))
            ->groupBy('hotel_branch_id')
            ->get();

        $labels = [];
        $values = [];
        foreach ($byBranchRows as $row) {
            $labels[] = $row->hotel_branch_id
                ? (string) ($branchNames[$row->hotel_branch_id] ?? __('Branch #:id', ['id' => $row->hotel_branch_id]))
                : __('Unassigned');
            $values[] = (int) $row->c;
        }

        $byStatusRows = DB::table('rooms')
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->get();

        $byStatus = collect($byStatusRows)->mapWithKeys(fn ($row) => [$row->status => (int) $row->c]);

        $preview = Room::query()->with('branch')->orderBy('name')->limit(12)->get();
        $tableRows = $preview->map(fn (Room $r) => [
            $r->name,
            $r->room_number ?? '—',
            $r->branch?->name ?? '—',
            $r->status->value,
            number_format((float) $r->price, 2),
        ]);

        return [
            'reportType' => 'rooms',
            'title' => __('Rooms report'),
            'from' => $from,
            'to' => $to,
            'exportUrl' => $exportUrl,
            'kpis' => [
                ['label' => __('Total rooms'), 'value' => (string) Room::query()->count()],
                ['label' => __('Avg price'), 'value' => number_format((float) Room::query()->avg('price'), 2)],
            ],
            'chartLabels' => $labels,
            'chartData' => $values,
            'chartLabel' => __('Rooms by branch'),
            'secondaryChart' => [
                'labels' => $byStatus->keys()->values()->all(),
                'values' => $byStatus->values()->all(),
                'label' => __('By status'),
            ],
            'tableHeaders' => [__('Room'), __('#'), __('Branch'), __('Status'), __('Price')],
            'tableRows' => $tableRows,
            'statusFilter' => null,
            'bookingStatuses' => BookingStatus::cases(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function maintenanceDashboard(Carbon $from, Carbon $to, string $exportUrl): array
    {
        $base = RoomMaintenance::query();
        $this->applyDateRange($base, 'created_at', $from, $to);

        $chart = $this->dailyCounts(RoomMaintenance::query(), 'created_at', $from, $to);

        $expenses = (clone $base)->sum('expenses');

        $statusQuery = DB::table('room_maintenances')
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status');
        if ($from) {
            $statusQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $statusQuery->whereDate('created_at', '<=', $to);
        }
        $byStatus = collect($statusQuery->get())->mapWithKeys(fn ($row) => [$row->status => (int) $row->c]);

        $preview = (clone $base)->with(['room', 'branch'])->latest()->limit(12)->get();
        $tableRows = $preview->map(fn (RoomMaintenance $m) => [
            $m->room?->name ?? '—',
            $m->status->value,
            (string) $m->expenses,
            $m->due_at?->format('Y-m-d') ?? '—',
        ]);

        return [
            'reportType' => 'maintenance',
            'title' => __('Maintenance report'),
            'from' => $from,
            'to' => $to,
            'exportUrl' => $exportUrl,
            'kpis' => [
                ['label' => __('In range'), 'value' => (string) (clone $base)->count()],
                ['label' => __('Expenses sum'), 'value' => number_format((float) $expenses, 2)],
            ],
            'chartLabels' => $chart['labels'],
            'chartData' => $chart['values'],
            'chartLabel' => __('Records opened per day'),
            'secondaryChart' => [
                'labels' => $byStatus->keys()->values()->all(),
                'values' => $byStatus->values()->all(),
                'label' => __('By status'),
            ],
            'tableHeaders' => [__('Room'), __('Status'), __('Expenses'), __('Due')],
            'tableRows' => $tableRows,
            'statusFilter' => null,
            'bookingStatuses' => BookingStatus::cases(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function fullDashboard(Carbon $from, Carbon $to, string $exportUrl): array
    {
        $bookingInRange = Booking::query();
        $this->applyDateRange($bookingInRange, 'created_at', $from, $to);

        $maintInRange = RoomMaintenance::query();
        $this->applyDateRange($maintInRange, 'created_at', $from, $to);

        return [
            'reportType' => 'full',
            'title' => __('Full export hub'),
            'from' => $from,
            'to' => $to,
            'exportUrl' => $exportUrl,
            'kpis' => [
                ['label' => __('Bookings (range)'), 'value' => (string) (clone $bookingInRange)->count()],
                ['label' => __('Customers'), 'value' => (string) Customer::query()->count()],
                ['label' => __('Rooms'), 'value' => (string) Room::query()->count()],
                ['label' => __('Maintenance (range)'), 'value' => (string) $maintInRange->count()],
            ],
            'chartLabels' => [],
            'chartData' => [],
            'chartLabel' => '',
            'tableHeaders' => [],
            'tableRows' => collect(),
            'statusFilter' => null,
            'bookingStatuses' => BookingStatus::cases(),
        ];
    }
}
