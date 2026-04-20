<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberSearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $bookings = collect();
        if (strlen($q) >= 2) {
            $like = '%'.$q.'%';
            $user = $request->user();
            $bookings = \App\Models\Booking::query()
                ->where(function ($b) use ($user) {
                    $b->where('user_id', $user->id)->orWhere('email', $user->email);
                })
                ->with('room')
                ->where(function ($b) use ($like): void {
                    $b->where('public_reference', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like);
                })
                ->latest()
                ->limit(20)
                ->get();
        }

        return view('member.search', [
            'q' => $q,
            'bookings' => $bookings,
        ]);
    }
}
