<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReceptionCustomerController extends Controller
{
    use InteractsWithStaffScope;

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $customers = Customer::query()
            ->when($q !== '', function ($c) use ($q) {
                $c->where(function ($c) use ($q) {
                    $c->where('email', 'like', '%'.$q.'%')
                        ->orWhere('phone', 'like', '%'.$q.'%')
                        ->orWhere('first_name', 'like', '%'.$q.'%')
                        ->orWhere('last_name', 'like', '%'.$q.'%');
                });
            })
            ->whereHas('bookings', function ($b) {
                $this->scope()->filterBookingsByBranch($b);
            })
            ->latest('last_booking_at')
            ->latest('id')
            ->paginate(7)
            ->withQueryString();

        return view('reception.customers.index', [
            'customers' => $customers,
            'q' => $q,
        ]);
    }

    public function show(Customer $customer): View
    {
        $bookings = $customer->bookings()->with(['room.branch', 'method', 'invoice']);
        $this->scope()->filterBookingsByBranch($bookings);
        $bookings = $bookings->latest()->get();

        abort_if($bookings->isEmpty(), 404);

        return view('reception.customers.show', [
            'customer' => $customer,
            'bookings' => $bookings,
        ]);
    }

    private function ensureCustomerInScope(Customer $customer): void
    {
        $q = $customer->bookings();
        $this->scope()->filterBookingsByBranch($q);
        abort_if($q->count() === 0, 404);
    }

    public function toggle(Customer $customer): RedirectResponse
    {
        $this->ensureCustomerInScope($customer);
        $customer->update(['is_active' => ! $customer->is_active]);

        return redirect()->route('reception.customers.index')->with('status', __('Customer updated.'));
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->ensureCustomerInScope($customer);
        $customer->delete();

        return redirect()->route('reception.customers.index')->with('status', __('Customer removed.'));
    }
}
