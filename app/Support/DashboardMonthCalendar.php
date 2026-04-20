<?php

namespace App\Support;

use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

final class DashboardMonthCalendar
{
    /**
     * @return array{title: string, weeks: list<list<array<string, mixed>|null>>}
     */
    public static function forStaffBookings(Builder $bookingBase, ?Carbon $month = null): array
    {
        $month = ($month ?? Carbon::now())->copy()->startOfMonth();
        $start = $month->copy()->startOfMonth()->startOfDay();
        $end = $month->copy()->endOfMonth()->endOfDay();

        $newCounts = (clone $bookingBase)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $confirmedCounts = (clone $bookingBase)
            ->where('status', BookingStatus::Confirmed)
            ->whereNotNull('confirmed_at')
            ->whereBetween('confirmed_at', [$start, $end])
            ->selectRaw('DATE(confirmed_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $grid = self::buildGrid($month, function (string $ds) use ($newCounts, $confirmedCounts): array {
            return [
                'mode' => 'staff',
                'new_bookings' => (int) ($newCounts[$ds] ?? 0),
                'confirmed' => (int) ($confirmedCounts[$ds] ?? 0),
            ];
        });
        $grid['mode'] = 'staff';

        return $grid;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\Booking>  $bookings
     * @return array{title: string, weeks: list<list<array<string, mixed>|null>>}
     */
    public static function forMemberStays(
        $bookings,
        ?Carbon $month = null,
        ?string $checkoutWeekday = null,
        ?string $checkoutWeekend = null,
    ): array {
        $month = ($month ?? Carbon::now())->copy()->startOfMonth();
        $wd = $checkoutWeekday && preg_match('/^\d{2}:\d{2}$/', $checkoutWeekday) === 1 ? $checkoutWeekday : '04:00';
        $we = $checkoutWeekend && preg_match('/^\d{2}:\d{2}$/', $checkoutWeekend) === 1 ? $checkoutWeekend : '04:30';

        $grid = self::buildGrid($month, function (string $ds) use ($bookings, $wd, $we): array {
            $day = Carbon::parse($ds)->startOfDay();
            $stay = false;
            $isCheckout = false;
            $checkoutClock = null;
            $refs = [];
            foreach ($bookings as $b) {
                if (! $b->check_in || ! $b->check_out) {
                    continue;
                }
                $ci = $b->check_in->copy()->startOfDay();
                $co = $b->check_out->copy()->startOfDay();
                if ($day->eq($co)) {
                    $isCheckout = true;
                    $clock = ((int) $co->dayOfWeekIso >= 6) ? $we : $wd;
                    $checkoutClock = $clock;
                    $refs[] = $b->public_reference;
                } elseif ($day->gte($ci) && $day->lt($co)) {
                    $stay = true;
                    $refs[] = $b->public_reference;
                }
            }

            return [
                'mode' => 'member',
                'stay' => $stay,
                'is_checkout' => $isCheckout,
                'checkout_time' => $checkoutClock,
                'refs' => array_values(array_unique($refs)),
            ];
        });
        $grid['mode'] = 'member';

        return $grid;
    }

    /**
     * @param  callable(string):array  $metaForDate  Y-m-d => cell meta (without day, date keys)
     * @return array{title: string, weeks: list<list<array<string, mixed>|null>>}
     */
    private static function buildGrid(Carbon $month, callable $metaForDate): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $title = $start->translatedFormat('F Y');

        $leading = $start->dayOfWeekIso - 1;
        $cells = [];
        for ($i = 0; $i < $leading; $i++) {
            $cells[] = null;
        }

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $ds = $d->toDateString();
            $cells[] = array_merge([
                'date' => $ds,
                'day' => (int) $d->format('j'),
                'is_today' => $d->isToday(),
            ], $metaForDate($ds));
        }

        while (count($cells) % 7 !== 0) {
            $cells[] = null;
        }

        $weeks = array_chunk($cells, 7);

        return ['title' => $title, 'weeks' => $weeks];
    }
}
