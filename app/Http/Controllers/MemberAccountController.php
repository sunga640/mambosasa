<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MemberAccountController extends Controller
{
    public function customer(Request $request): View
    {
        $user = $request->user();

        $customer = Customer::query()->where('email', $user->email)->first();

        $bookings = Booking::query()
            ->where(function ($q) use ($user) {
                $q->where('email', $user->email)->orWhere('user_id', $user->id);
            })
            ->with(['room.branch', 'method', 'invoice'])
            ->latest()
            ->paginate(20);

        return view('member.account.customer', [
            'customer' => $customer,
            'bookings' => $bookings,
        ]);
    }
}
