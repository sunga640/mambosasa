<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingMethod;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;                    // MPYA: Kwa ajili ya kutengeneza account
use App\Services\BookingLifecycleService;
use App\Support\StaffScope;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;   // MPYA: Kwa ajili ya Hash::make
use Illuminate\Support\Facades\Mail;   // MPYA: Kwa ajili ya Mail::to
use Illuminate\Support\Str;            // MPYA: Kwa ajili ya Str::random
use App\Mail\GuestLoginCredentialsMail; // MPYA: Kwa ajili ya Email

class AdminPendingPaymentsController extends Controller
{
    public function index(Request $request, StaffScope $scope): View
    {
        $search = trim((string) $request->query('q', ''));
        $methodId = (int) $request->query('method_id', 0);
        $query = Booking::query()
            ->with(['room.branch', 'method', 'user'])
            ->where('status', BookingStatus::PendingPayment)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('public_reference', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%');
                });
            })
            ->when($methodId > 0, fn ($query) => $query->where('booking_method_id', $methodId))
            ->latest();
        $scope->filterBookingsByBranch($query);

        $bookings = $query->paginate(15)->withQueryString();

        return view('admin.payments.pending', [
            'bookings' => $bookings,
            'search' => $search,
            'methodId' => $methodId,
            'methods' => BookingMethod::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function confirm(Request $request, Booking $booking, BookingLifecycleService $lifecycle): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $booking->loadMissing('room');
        $ids = app(StaffScope::class)->branchIds();
        if ($ids !== null) {
            $bid = (int) $booking->room->hotel_branch_id;
            if ($ids === [] || ! in_array($bid, $ids, true)) {
                abort(404);
            }
        }

        abort_unless($booking->status === BookingStatus::PendingPayment, 400);

        // 1. TENGENEZA PASSWORD YA RANDOM
        $plainPassword = Str::random(10);

        // 2. TENGENEZA USER ACCOUNT (Kama haipo)
        // Tunatafuta kama user ana email hii tayari
        $user = User::where('email', $booking->email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $booking->first_name . ' ' . $booking->last_name,
                'email' => $booking->email,
                'password' => Hash::make($plainPassword),
            ]);
        }

        // 3. UPDATE BOOKING
        $booking->update([
            'status' => BookingStatus::Confirmed,
            'confirmed_at' => now(),
            'payment_deadline_at' => null,
            'user_id' => $user->id, // Unganisha booking na user account
        ]);

        Customer::syncFromBooking($booking->fresh());
        Invoice::createForBooking($booking->fresh());
        $lifecycle->handlePaymentConfirmed($booking->fresh());

        // 4. TUMA EMAIL
        // Hapa tunatuma password aliyotengenezewa mteja kwenye email yake
        try {
            Mail::to($booking->email)->send(new GuestLoginCredentialsMail($booking, $plainPassword));
        } catch (\Exception $e) {
            // Hi ni option: Kama email ikifeli kutumwa, system isicrash
            // Unaweza kulog error hapa ikibidi
        }

        return back()->with('status', __('Payment confirmed. Login credentials sent to guest email.'));
    }

    public function cancel(Request $request, Booking $booking, StaffScope $scope): RedirectResponse
    {
        $booking->loadMissing('room');
        $ids = $scope->branchIds();
        if ($ids !== null) {
            $bid = (int) $booking->room->hotel_branch_id;
            if ($ids === [] || ! in_array($bid, $ids, true)) {
                abort(404);
            }
        }

        abort_unless($booking->status === BookingStatus::PendingPayment, 400);

        $booking->update([
            'status' => BookingStatus::Cancelled,
            'payment_deadline_at' => null,
        ]);

        return back()->with('status', __('Booking cancelled.'));
    }

    public function resendReminder(Booking $booking, StaffScope $scope, BookingLifecycleService $lifecycle): RedirectResponse
    {
        $booking->loadMissing('room');
        $ids = $scope->branchIds();
        if ($ids !== null) {
            $bid = (int) $booking->room->hotel_branch_id;
            if ($ids === [] || ! in_array($bid, $ids, true)) {
                abort(404);
            }
        }

        abort_unless($booking->status === BookingStatus::PendingPayment, 400);

        $lifecycle->resendPendingPaymentNotifications($booking->fresh());

        return back()->with('status', __('Payment reminder sent again (email and SMS if configured).'));
    }
}
