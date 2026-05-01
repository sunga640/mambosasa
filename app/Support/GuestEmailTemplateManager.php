<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\SystemSetting;

class GuestEmailTemplateManager
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function definitions(): array
    {
        return [
            'pending_payment' => [
                'label' => __('Booking wait payment'),
                'description' => __('Sent immediately after a guest books and still needs to complete payment.'),
                'placeholders' => $this->commonPlaceholders([
                    '{{payment_deadline}}',
                    '{{payment_method}}',
                    '{{payment_url}}',
                ]),
                'defaults' => [
                    'enabled' => true,
                    'details_enabled' => true,
                    'accent_color' => '#1f7ae0',
                    'subject' => __('Complete your payment - {{booking_reference}}'),
                    'title' => __('Booking reserved, payment pending'),
                    'intro' => __('Hi {{first_name}},'),
                    'body' => __("We have reserved your stay at {{hotel_name}} under booking {{booking_reference}}.\n\nPlease complete payment before {{payment_deadline}} so your room remains secured.\n\nIf you already paid, you can ignore this message and our team will confirm your stay shortly."),
                    'highlight' => __('Amount due: {{total_amount}}'),
                    'primary_button_label' => __('Complete payment'),
                    'secondary_button_label' => __('View invoice'),
                    'footer_note' => __('Need help? Reply to this email or contact {{hotel_email}} / {{hotel_phone}}.'),
                ],
            ],
            'invoice_ready' => [
                'label' => __('Booking invoice'),
                'description' => __('Sent with the guest invoice link after booking or when the invoice is resent.'),
                'placeholders' => $this->commonPlaceholders([
                    '{{invoice_number}}',
                    '{{invoice_url}}',
                ]),
                'defaults' => [
                    'enabled' => true,
                    'details_enabled' => true,
                    'accent_color' => '#b54a1f',
                    'subject' => __('Invoice {{invoice_number}} - {{booking_reference}}'),
                    'title' => __('Your invoice is ready'),
                    'intro' => __('Hello {{guest_name}},'),
                    'body' => __("Thank you for choosing {{hotel_name}}.\n\nYour invoice {{invoice_number}} for booking {{booking_reference}} is ready to review online.\n\nYou can open it below, print it, or keep it for your records."),
                    'highlight' => __('Invoice total: {{total_amount}}'),
                    'primary_button_label' => __('View invoice'),
                    'secondary_button_label' => __('Open booking'),
                    'footer_note' => __('We are happy to host you at {{branch_name}}.'),
                ],
            ],
            'payment_confirmed' => [
                'label' => __('Booking confirmation'),
                'description' => __('Sent after payment is confirmed and the booking becomes active.'),
                'placeholders' => $this->commonPlaceholders([
                    '{{login_url}}',
                ]),
                'defaults' => [
                    'enabled' => true,
                    'details_enabled' => true,
                    'accent_color' => '#14804a',
                    'subject' => __('Payment confirmed - {{booking_reference}}'),
                    'title' => __('Your booking is now confirmed'),
                    'intro' => __('Hi {{first_name}},'),
                    'body' => __("We have confirmed payment for booking {{booking_reference}}.\n\nYour stay at {{hotel_name}} is now active, and you can use the guest portal to follow your booking, room-service access, and stay details."),
                    'highlight' => __('Stay dates: {{check_in}} to {{check_out}}'),
                    'primary_button_label' => __('Open guest portal'),
                    'secondary_button_label' => __('Login page'),
                    'footer_note' => __('We look forward to hosting you at {{branch_name}}.'),
                ],
            ],
            'guest_credentials' => [
                'label' => __('Guest portal credentials'),
                'description' => __('Sent after payment confirmation when the system creates login details for the guest.'),
                'placeholders' => $this->commonPlaceholders([
                    '{{login_url}}',
                    '{{guest_email}}',
                    '{{password}}',
                ]),
                'defaults' => [
                    'enabled' => true,
                    'details_enabled' => true,
                    'accent_color' => '#6b46c1',
                    'subject' => __('Your booking confirmation and login details'),
                    'title' => __('Guest portal access'),
                    'intro' => __('Hello {{first_name}},'),
                    'body' => __("We have prepared your guest access so you can manage your stay online.\n\nUse the credentials below to sign in, review booking details, request room service, and follow invoices during your stay."),
                    'highlight' => __("Username: {{guest_email}}\nPassword: {{password}}"),
                    'primary_button_label' => __('Open login page'),
                    'secondary_button_label' => __('Open guest portal'),
                    'footer_note' => __('For security, please change the password after your first login if needed.'),
                ],
            ],
            'guest_signout' => [
                'label' => __('Checkout notice'),
                'description' => __('Sent when the stay is completed and the guest is checked out.'),
                'placeholders' => $this->commonPlaceholders([]),
                'defaults' => [
                    'enabled' => true,
                    'details_enabled' => true,
                    'accent_color' => '#5d6778',
                    'subject' => __('Checkout notice - {{booking_reference}}'),
                    'title' => __('Thank you for staying with us'),
                    'intro' => __('Hi {{first_name}},'),
                    'body' => __("Your booking {{booking_reference}} has now been checked out successfully.\n\nThank you for choosing {{hotel_name}}. We hope your stay was comfortable and memorable."),
                    'highlight' => __('Room: {{room_name}}'),
                    'primary_button_label' => __('View booking'),
                    'secondary_button_label' => __(''),
                    'footer_note' => __('We would love to welcome you again at {{branch_name}}.'),
                ],
            ],
        ];
    }

    public function isEnabled(string $key, ?SystemSetting $setting = null): bool
    {
        return (bool) ($this->all($setting)[$key]['enabled'] ?? false);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function all(?SystemSetting $setting = null): array
    {
        $setting ??= SystemSetting::current();
        $stored = is_array($setting->email_templates) ? $setting->email_templates : [];
        $resolved = [];

        foreach ($this->definitions() as $key => $definition) {
            $defaults = $definition['defaults'];
            $saved = is_array($stored[$key] ?? null) ? $stored[$key] : [];

            $resolved[$key] = array_merge($defaults, $saved, [
                'enabled' => (bool) ($saved['enabled'] ?? $defaults['enabled']),
                'details_enabled' => (bool) ($saved['details_enabled'] ?? $defaults['details_enabled']),
                'accent_color' => $this->sanitizeColor($saved['accent_color'] ?? $defaults['accent_color']),
            ]);
        }

        return $resolved;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, array<string, mixed>>
     */
    public function normalizeForSave(array $raw): array
    {
        $normalized = [];

        foreach ($this->definitions() as $key => $definition) {
            $defaults = $definition['defaults'];
            $row = is_array($raw[$key] ?? null) ? $raw[$key] : [];

            $normalized[$key] = [
                'enabled' => (bool) ($row['enabled'] ?? $defaults['enabled']),
                'details_enabled' => (bool) ($row['details_enabled'] ?? $defaults['details_enabled']),
                'accent_color' => $this->sanitizeColor($row['accent_color'] ?? $defaults['accent_color']),
                'subject' => $this->cleanString($row['subject'] ?? $defaults['subject'], 255) ?: $defaults['subject'],
                'title' => $this->cleanString($row['title'] ?? $defaults['title'], 255),
                'intro' => $this->cleanString($row['intro'] ?? $defaults['intro'], 1000),
                'body' => $this->cleanString($row['body'] ?? $defaults['body'], 5000),
                'highlight' => $this->cleanString($row['highlight'] ?? $defaults['highlight'], 1000),
                'primary_button_label' => $this->cleanString($row['primary_button_label'] ?? $defaults['primary_button_label'], 120),
                'secondary_button_label' => $this->cleanString($row['secondary_button_label'] ?? $defaults['secondary_button_label'], 120),
                'footer_note' => $this->cleanString($row['footer_note'] ?? $defaults['footer_note'], 1500),
            ];
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    public function render(string $key, Booking $booking, ?Invoice $invoice = null, array $extra = []): array
    {
        $booking->loadMissing(['room.branch', 'method']);

        $all = $this->all();
        $template = $all[$key] ?? null;
        if (! is_array($template)) {
            throw new \InvalidArgumentException("Unknown guest email template [{$key}]");
        }

        $context = $this->context($booking, $invoice, $extra);

        $primaryUrl = $this->resolvePrimaryUrl($key, $context);
        $secondaryUrl = $this->resolveSecondaryUrl($key, $context);

        return [
            'key' => $key,
            'enabled' => (bool) $template['enabled'],
            'accent_color' => $template['accent_color'],
            'subject' => $this->replace((string) $template['subject'], $context),
            'title' => $this->replace((string) $template['title'], $context),
            'intro' => $this->replace((string) $template['intro'], $context),
            'body' => $this->replace((string) $template['body'], $context),
            'highlight' => $this->replace((string) $template['highlight'], $context),
            'primary_button_label' => $this->replace((string) $template['primary_button_label'], $context),
            'primary_button_url' => $primaryUrl,
            'secondary_button_label' => $this->replace((string) $template['secondary_button_label'], $context),
            'secondary_button_url' => $secondaryUrl,
            'footer_note' => $this->replace((string) $template['footer_note'], $context),
            'details_enabled' => (bool) $template['details_enabled'],
            'details' => $this->detailsFor($key, $context),
            'hotel_name' => $context['hotel_name'],
        ];
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, string>
     */
    private function context(Booking $booking, ?Invoice $invoice, array $extra = []): array
    {
        $settings = SystemSetting::current();
        $guestName = trim($booking->first_name.' '.$booking->last_name);
        $checkIn = $booking->check_in?->format('Y-m-d') ?? '';
        $checkOut = $booking->check_out?->format('Y-m-d') ?? '';
        $paymentDeadline = $booking->payment_deadline_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '';
        $roomName = trim((string) ($booking->room?->name ?? ''));
        $roomNumber = trim((string) ($booking->room?->room_number ?? ''));
        $branchName = trim((string) ($booking->room?->branch?->name ?? ''));

        $context = [
            'guest_name' => $guestName !== '' ? $guestName : trim((string) $booking->first_name),
            'first_name' => trim((string) $booking->first_name),
            'last_name' => trim((string) $booking->last_name),
            'guest_email' => trim((string) $booking->email),
            'booking_reference' => trim((string) $booking->public_reference),
            'hotel_name' => $settings->hotelDisplayName(),
            'hotel_email' => trim((string) ($settings->mail_from_address ?: $settings->email ?: '')),
            'hotel_phone' => trim((string) ($settings->phone ?? '')),
            'branch_name' => $branchName !== '' ? $branchName : $settings->hotelDisplayName(),
            'room_name' => $roomName !== '' ? $roomName : __('Room'),
            'room_number' => $roomNumber !== '' ? $roomNumber : ($roomName !== '' ? $roomName : __('Room')),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'nights' => (string) ($booking->nights ?? ''),
            'adults' => (string) ($booking->adults ?? ''),
            'children' => (string) ($booking->children ?? ''),
            'rooms_count' => (string) ($booking->rooms_count ?? ''),
            'total_amount' => number_format((float) $booking->total_amount, 0),
            'payment_method' => trim((string) ($booking->method?->name ?? __('Selected method'))),
            'payment_deadline' => $paymentDeadline !== '' ? $paymentDeadline : __('the deadline'),
            'portal_url' => $booking->guestPortalUrl(),
            'invoice_url' => $invoice?->publicUrl() ?? '',
            'invoice_number' => trim((string) ($invoice?->number ?? '')),
            'payment_url' => trim((string) ($extra['payment_url'] ?? '')),
            'login_url' => url('/login'),
            'password' => trim((string) ($extra['password'] ?? '')),
        ];

        foreach ($extra as $key => $value) {
            if (is_scalar($value) && ! isset($context[$key])) {
                $context[$key] = trim((string) $value);
            }
        }

        return $context;
    }

    /**
     * @param  array<string, string>  $context
     * @return list<array{label:string,value:string}>
     */
    private function detailsFor(string $key, array $context): array
    {
        $rows = [
            ['label' => __('Booking reference'), 'value' => $context['booking_reference']],
            ['label' => __('Guest'), 'value' => $context['guest_name']],
            ['label' => __('Branch'), 'value' => $context['branch_name']],
            ['label' => __('Room'), 'value' => $context['room_number']],
            ['label' => __('Check in'), 'value' => $context['check_in']],
            ['label' => __('Check out'), 'value' => $context['check_out']],
            ['label' => __('Amount'), 'value' => $context['total_amount']],
        ];

        if ($key === 'invoice_ready') {
            $rows[] = ['label' => __('Invoice number'), 'value' => $context['invoice_number']];
        }

        if ($key === 'pending_payment') {
            $rows[] = ['label' => __('Payment deadline'), 'value' => $context['payment_deadline']];
            $rows[] = ['label' => __('Payment method'), 'value' => $context['payment_method']];
        }

        if ($key === 'guest_credentials') {
            $rows[] = ['label' => __('Username'), 'value' => $context['guest_email']];
            $rows[] = ['label' => __('Password'), 'value' => $context['password']];
        }

        return array_values(array_filter($rows, static function (array $row): bool {
            return trim($row['value']) !== '';
        }));
    }

    /**
     * @param  array<string, string>  $context
     */
    private function replace(string $value, array $context): string
    {
        if ($value === '') {
            return '';
        }

        $pairs = [];
        foreach ($context as $key => $replacement) {
            $pairs['{{'.$key.'}}'] = $replacement;
        }

        return strtr($value, $pairs);
    }

    /**
     * @param  array<string, string>  $context
     */
    private function resolvePrimaryUrl(string $key, array $context): ?string
    {
        return match ($key) {
            'pending_payment' => $context['payment_url'] ?: $context['portal_url'],
            'invoice_ready' => $context['invoice_url'] ?: $context['portal_url'],
            'payment_confirmed' => $context['portal_url'],
            'guest_credentials' => $context['login_url'],
            'guest_signout' => $context['portal_url'],
            default => $context['portal_url'],
        } ?: null;
    }

    /**
     * @param  array<string, string>  $context
     */
    private function resolveSecondaryUrl(string $key, array $context): ?string
    {
        return match ($key) {
            'pending_payment' => $context['invoice_url'] ?: $context['portal_url'],
            'invoice_ready' => $context['portal_url'],
            'payment_confirmed' => $context['login_url'],
            'guest_credentials' => $context['portal_url'],
            default => null,
        } ?: null;
    }

    /**
     * @param  list<string>  $extra
     * @return list<string>
     */
    private function commonPlaceholders(array $extra): array
    {
        return array_values(array_unique(array_merge([
            '{{guest_name}}',
            '{{first_name}}',
            '{{last_name}}',
            '{{guest_email}}',
            '{{booking_reference}}',
            '{{hotel_name}}',
            '{{hotel_email}}',
            '{{hotel_phone}}',
            '{{branch_name}}',
            '{{room_name}}',
            '{{room_number}}',
            '{{check_in}}',
            '{{check_out}}',
            '{{nights}}',
            '{{rooms_count}}',
            '{{adults}}',
            '{{children}}',
            '{{total_amount}}',
            '{{portal_url}}',
        ], $extra)));
    }

    private function sanitizeColor(mixed $value): string
    {
        $value = trim((string) $value);

        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1 ? $value : '#1f7ae0';
    }

    private function cleanString(mixed $value, int $limit): string
    {
        $value = trim((string) $value);

        return mb_substr($value, 0, $limit);
    }
}
