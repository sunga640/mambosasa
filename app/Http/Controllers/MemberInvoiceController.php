<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MemberInvoiceController extends Controller
{
    public function __invoke(Request $request): View
    {
        $u = $request->user();
        $invoices = Invoice::query()
            ->whereHas('booking', function ($q) use ($u): void {
                $q->where('user_id', $u->id)->orWhere('email', $u->email);
            })
            ->with('booking.room')
            ->latest('issued_at')
            ->paginate(15);

        return view('member.invoices.index', [
            'invoices' => $invoices,
        ]);
    }
}
