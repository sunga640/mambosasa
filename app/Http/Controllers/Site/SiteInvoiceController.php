<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SiteInvoiceController extends Controller
{
    public function show(string $token): View
    {
        $invoice = Invoice::query()->where('token', $token)->with(['booking.room.branch', 'booking.method'])->firstOrFail();

        return view('site.invoice.show', [
            'invoice' => $invoice,
            'booking' => $invoice->booking,
        ]);
    }

    public function exportCsv(string $token): StreamedResponse
    {
        $invoice = Invoice::query()->where('token', $token)->with('booking.room')->firstOrFail();

        $filename = 'invoice-'.$invoice->number.'.csv';

        return response()->streamDownload(function () use ($invoice): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Invoice', $invoice->number]);
            fputcsv($out, ['Issued', $invoice->issued_at?->toDateTimeString()]);
            fputcsv($out, ['Booking', $invoice->booking?->public_reference]);
            fputcsv($out, ['Description', 'Qty', 'Unit', 'Total']);
            foreach ($invoice->line_items ?? [] as $row) {
                fputcsv($out, [
                    $row['description'] ?? '',
                    $row['quantity'] ?? 1,
                    $row['unit_price'] ?? '',
                    $row['total'] ?? '',
                ]);
            }
            fputcsv($out, ['Grand total', '', '', (string) $invoice->total_amount]);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
