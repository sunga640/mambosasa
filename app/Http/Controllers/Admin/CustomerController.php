<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $customers = Customer::query()
            ->when($q !== '', function ($b) use ($q): void {
                $b->where(function ($b) use ($q): void {
                    $b->where('email', 'like', '%'.$q.'%')
                        ->orWhere('phone', 'like', '%'.$q.'%')
                        ->orWhere('first_name', 'like', '%'.$q.'%')
                        ->orWhere('last_name', 'like', '%'.$q.'%');
                });
            })
            ->latest('last_booking_at')
            ->latest('id')
            ->paginate(7)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'q' => $q,
        ]);
    }

    public function toggle(Customer $customer): RedirectResponse
    {
        $customer->update(['is_active' => ! $customer->is_active]);

        return redirect()->route('admin.customers.index')->with('status', __('Customer updated.'));
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('status', __('Customer removed.'));
    }
}
