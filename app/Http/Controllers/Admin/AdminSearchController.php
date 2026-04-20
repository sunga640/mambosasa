<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $bookings = collect();
        $customers = collect();
        $rooms = collect();

        if (strlen($q) >= 2) {
            $like = '%'.$q.'%';
            $bookings = Booking::query()
                ->with(['room', 'user'])
                ->where(function ($b) use ($like): void {
                    $b->where('public_reference', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                })
                ->latest()
                ->limit(15)
                ->get();

            $customers = Customer::query()
                ->where(function ($b) use ($like): void {
                    $b->where('email', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like);
                })
                ->latest()
                ->limit(15)
                ->get();

            $rooms = Room::query()
                ->with('branch')
                ->where(function ($b) use ($like): void {
                    $b->where('name', 'like', $like)
                        ->orWhere('room_number', 'like', $like)
                        ->orWhere('slug', 'like', $like);
                })
                ->orderBy('name')
                ->limit(15)
                ->get();
        }

        return view('admin.search.results', [
            'q' => $q,
            'bookings' => $bookings,
            'customers' => $customers,
            'rooms' => $rooms,
        ]);
    }
}
