<?php

namespace App\Services;

use App\Models\BookingMethod;
use App\Models\RoomServiceOrder;

class RoomServiceOrderPaymentService
{
    public function supportsAutomaticGateway(BookingMethod $method): bool
    {
        if ($method->method_type !== 'online') {
            return false;
        }

        return $method->slug === 'pesapal'
            && filled($method->gateway_public_key)
            && filled($method->gateway_secret_key);
    }

    public function onlineMethods()
    {
        return BookingMethod::query()
            ->where('is_active', true)
            ->where('method_type', 'online')
            ->where(fn ($query) => $query
                ->where('visibility', BookingMethod::VIS_PUBLIC)
                ->where('show_on_booking_page', true))
            ->where(function ($query) {
                $query->where('slug', 'pesapal')
                    ->orWhereNotNull('instructions');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function availableMethods()
    {
        return $this->onlineMethods();
    }

    public function cashMethod(): ?BookingMethod
    {
        return BookingMethod::query()
            ->where('slug', 'cash')
            ->where('is_active', true)
            ->first();
    }

    public function markCashPending(RoomServiceOrder $order, BookingMethod $method): RoomServiceOrder
    {
        $order->forceFill([
            'booking_method_id' => $method->id,
            'payment_status' => 'cash_pending',
            'payment_reference' => null,
            'paid_at' => null,
        ])->save();

        return $order->fresh(['bookingMethod']);
    }

    public function markBillLater(RoomServiceOrder $order, BookingMethod $method): RoomServiceOrder
    {
        $order->forceFill([
            'booking_method_id' => $method->id,
            'payment_status' => 'bill_later',
            'payment_reference' => null,
            'paid_at' => null,
        ])->save();

        return $order->fresh(['bookingMethod']);
    }

    public function markPaid(RoomServiceOrder $order, BookingMethod $method, ?string $paymentReference = null): RoomServiceOrder
    {
        $order->forceFill([
            'booking_method_id' => $method->id,
            'payment_status' => 'paid',
            'payment_reference' => $paymentReference,
            'paid_at' => $order->paid_at ?? now(),
        ])->save();

        return $order->fresh(['bookingMethod']);
    }

    public function markProcessing(RoomServiceOrder $order, BookingMethod $method, ?string $paymentReference = null): RoomServiceOrder
    {
        $order->forceFill([
            'booking_method_id' => $method->id,
            'payment_status' => 'processing',
            'payment_reference' => $paymentReference,
        ])->save();

        return $order->fresh(['bookingMethod']);
    }
}
