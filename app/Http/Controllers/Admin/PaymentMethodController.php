<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingMethod;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    private const PROVIDER_PRESETS = [
        'cash' => [
            'label' => 'Pay on arrival / cash',
            'name' => 'Pay on arrive',
            'code' => 'cash',
            'method_type' => 'offline',
            'visibility' => 'public',
            'show_on_booking_page' => true,
        ],
        'pesapal' => [
            'label' => 'Pesapal',
            'name' => 'Pesapal',
            'code' => 'pesapal',
            'method_type' => 'online',
            'visibility' => 'public',
            'show_on_booking_page' => true,
        ],
        'mpesa' => [
            'label' => 'M-Pesa',
            'name' => 'Lipa kwa simu (M-Pesa)',
            'code' => 'mpesa',
            'method_type' => 'online',
            'visibility' => 'public',
            'show_on_booking_page' => true,
        ],
        'tigopesa' => [
            'label' => 'Tigo Pesa',
            'name' => 'Lipa kwa simu (Tigo Pesa)',
            'code' => 'tigopesa',
            'method_type' => 'online',
            'visibility' => 'public',
            'show_on_booking_page' => true,
        ],
    ];

    public function index(): View
    {
        return view('admin.payment-methods.index', [
            'methods' => BookingMethod::query()->orderBy('sort_order')->orderBy('name')->get(),
            'providerPresets' => self::PROVIDER_PRESETS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePayload($request);
        $data['slug'] = \Illuminate\Support\Str::slug($data['code'] ?: $data['name'], '_');

        BookingMethod::query()->create($data);

        return back()->with('status', __('Payment method created.'));
    }

    public function update(Request $request, BookingMethod $paymentMethod): RedirectResponse
    {
        $data = $this->validatePayload($request, $paymentMethod->id);
        $paymentMethod->update($data);

        return back()->with('status', __('Payment method updated.'));
    }

    public function destroy(BookingMethod $paymentMethod): RedirectResponse
    {
        try {
            $paymentMethod->delete();
        } catch (QueryException $exception) {
            return back()->withErrors([
                'payment_method_delete' => __('This payment method is still used by bookings or orders, so it cannot be deleted yet.'),
            ]);
        }

        return back()->with('status', __('Payment method deleted.'));
    }

    public function toggle(BookingMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->update(['is_active' => ! $paymentMethod->is_active]);

        return back()->with('status', __('Payment method status updated.'));
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:120'],
            'method_type' => ['required', 'in:offline,online'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'visibility' => ['required', 'in:public,internal'],
            'show_on_booking_page' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'account_number' => ['nullable', 'string', 'max:120'],
            'account_holder' => ['nullable', 'string', 'max:150'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'gateway_public_key' => ['nullable', 'string', 'max:255'],
            'gateway_secret_key' => ['nullable', 'string', 'max:255'],
            'gateway_base_url' => ['nullable', 'url', 'max:255'],
            'gateway_ipn_id' => ['nullable', 'string', 'max:255'],
        ];

        $data = $request->validate($rules);
        $data['show_on_booking_page'] = $request->boolean('show_on_booking_page');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $slugBase = \Illuminate\Support\Str::slug((string) ($data['code'] ?: $data['name']), '_');
        $slug = $slugBase !== '' ? $slugBase : 'method_'.time();
        $exists = BookingMethod::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
        if ($exists) {
            $slug .= '_'.substr(md5((string) microtime(true)), 0, 4);
        }
        $data['slug'] = $slug;

        return $data;
    }
}
