# Restaurant Integration Setup

These files are the package you should copy into the **restaurant system** so it can accept hotel guest access securely.

## Files already prepared in this hotel system

1. `docs/integrations/restaurant/HotelGuestSsoVerifier.php`
2. `docs/integrations/restaurant/HotelSsoController.php`

## Where to place them in the restaurant system

If the restaurant system is Laravel, copy them like this:

1. Copy `HotelGuestSsoVerifier.php` to:
   `app/Support/Integrations/HotelGuestSsoVerifier.php`

2. Copy `HotelSsoController.php` to:
   `app/Http/Controllers/HotelSsoController.php`

## Add the shared secret in the restaurant system

Put this in the restaurant system `.env`:

```env
HOTEL_GUEST_SSO_SHARED_SECRET=put-the-same-secret-you-entered-in-hotel-admin
```

Then add this to `config/services.php` in the restaurant system:

```php
'hotel_guest_sso' => [
    'shared_secret' => env('HOTEL_GUEST_SSO_SHARED_SECRET', ''),
],
```

## Add the SSO route in the restaurant system

In `routes/web.php` of the restaurant system:

```php
use App\Http\Controllers\HotelSsoController;

Route::get('/restaurant/sso/hotel', [HotelSsoController::class, 'hotelGuestEntry'])
    ->name('restaurant.hotel-sso');
```

This path should match what you enter in the hotel admin panel as:

`System Settings -> Integrations -> Restaurant SSO / launch path`

If you use the route above, the launch path is:

```text
restaurant/sso/hotel
```

## What the restaurant system should do after verification

After the token is verified successfully:

1. Find or create the restaurant-side guest/customer using the email.
2. Save room and branch context in session.
3. Log the guest into the restaurant system or create a temporary order session.
4. Redirect them to the restaurant ordering dashboard/page.

## Payload fields the hotel system sends

The restaurant system will receive verified payload data such as:

- `booking_reference`
- `booking_id`
- `guest_name`
- `guest_email`
- `guest_phone`
- `room_id`
- `room_name`
- `room_number`
- `branch_id`
- `branch_name`
- `check_in`
- `check_out`
- `iat`
- `exp`
- `jti`

## Security model

This integration is secured in these layers:

1. **At rest**:
   hotel-side integration secrets are stored encrypted in the hotel database.

2. **In transit**:
   hotel and restaurant communication should use HTTPS only.

3. **Signed handoff**:
   the guest browser never receives raw API secrets. It only carries a short-lived signed token.

4. **Replay protection**:
   the sample controller stores used `jti` values in cache so one token cannot be reused repeatedly.

5. **Short lifetime**:
   the token expires in a few minutes based on the value set in the hotel admin integration settings.

## What to configure in the hotel system admin

Go to:

`Admin -> System Settings -> Integrations`

Fill these:

1. `Enable restaurant integration`
2. `Restaurant system base URL`
3. `Restaurant SSO / launch path`
4. `Shared secret for signed guest access`
5. Optional `Server API key`
6. Optional `Server API secret`
7. Token lifetime and timeout

## Important note

`Ctrl+U`, browser devtools, or frontend source inspection cannot be blocked 100 percent by any website. The real protection for this integration is:

- server-side secret storage
- HTTPS
- signed short-lived tokens
- server-side verification on the restaurant system

## After copying the files

Run on the restaurant system:

```bash
php artisan optimize:clear
```

If Laravel caches config/routes/views in production, then also run:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
