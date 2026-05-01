<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DashboardNotificationController;
use App\Http\Controllers\Admin\HotelBranchController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\AdminSearchController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\RoomMaintenanceController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\NewsletterSubscriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberAccountController;
use App\Http\Controllers\MemberBookingController;
use App\Http\Controllers\Member\MemberRestaurantController;
use App\Http\Controllers\Reception\ReceptionBookingController;
use App\Http\Controllers\Reception\ReceptionBranchAnalyticsController;
use App\Http\Controllers\Reception\ReceptionBranchController;
use App\Http\Controllers\Reception\ReceptionContactController;
use App\Http\Controllers\Reception\ReceptionCustomerController;
use App\Http\Controllers\Reception\ReceptionDashboardController;
use App\Http\Controllers\Reception\ReceptionInvoiceController;
use App\Http\Controllers\Reception\ReceptionMaintenanceController;
use App\Http\Controllers\Reception\ReceptionNotificationController;
use App\Http\Controllers\Reception\ReceptionReportController;
use App\Http\Controllers\Reception\ReceptionRoomController;
use App\Http\Controllers\RoomServiceController;
use App\Http\Controllers\Reception\ReceptionRoomServiceController;
use App\Http\Controllers\Admin\PropertiesDirectoryController;
use App\Http\Controllers\Member\MemberPropertiesController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\MemberInvoiceController;
use App\Http\Controllers\MemberNotificationController;
use App\Http\Controllers\MemberSearchController;
use App\Http\Controllers\Reception\ReceptionPropertiesController;
use App\Http\Controllers\Site\BookingPortalController;
use App\Http\Controllers\Site\SiteBookingController;
use App\Http\Controllers\Site\SiteCartController;
use App\Http\Controllers\Site\SiteContactController;
use App\Http\Controllers\Site\SiteNewsletterController;
use App\Http\Controllers\Site\SiteInvoiceController;
use App\Http\Controllers\Site\SitePageController;
use App\Http\Controllers\Site\GuestStayController;
use App\Http\Controllers\Site\GuestRestaurantAccessController;
use App\Http\Controllers\Site\SiteBranchesController;
use App\Http\Controllers\Site\SiteSearchController;
use App\Http\Controllers\Site\SiteSitemapController;
use App\Http\Controllers\Site\PublicMediaController;
use App\Http\Controllers\Admin\AdminBranchScopeController;
use App\Http\Controllers\Admin\AdminPendingPaymentsController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Member\MemberBranchFilterController;
use App\Http\Controllers\Member\MemberHotelServiceCatalogController;
use App\Http\Controllers\Member\MemberHotelServiceRequestController;
use App\Http\Controllers\Kitchen\KitchenDashboardController;
use App\Http\Controllers\Kitchen\KitchenAssignmentController;
use App\Http\Controllers\Kitchen\KitchenMenuController;
use App\Http\Controllers\Kitchen\KitchenNotificationController;
use App\Http\Controllers\Kitchen\KitchenOrderController;
use App\Http\Controllers\Kitchen\KitchenQrController;
use App\Http\Controllers\Kitchen\KitchenReportController;
use App\Http\Controllers\Kitchen\KitchenRoleController;
use App\Http\Controllers\Kitchen\KitchenSettingsController;
use App\Http\Controllers\Kitchen\KitchenStaffController;
use App\Http\Controllers\Reception\ReceptionHotelServiceController;
use App\Http\Controllers\Site\KitchenQrMenuController;
use App\Http\Controllers\Site\RoomServiceOrderPaymentController;
use App\Http\Controllers\RoomServiceBillController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\PesapalController;

Route::get('/', [SitePageController::class, 'home'])->name('site.home');

Route::get('/booking', [SiteBookingController::class, 'show'])->name('site.booking');
Route::post('/booking', [SiteBookingController::class, 'store'])->middleware('throttle:20,1')->name('site.booking.store');
Route::get('/booking/availability', [SiteBookingController::class, 'availability'])->name('site.booking.availability');
Route::get('/booking/room-calendar', [SiteBookingController::class, 'roomCalendar'])->name('site.booking.room-calendar');
Route::get('/booking/confirmation/{reference}', [SiteBookingController::class, 'confirmation'])
    ->where('reference', '[A-Za-z0-9\-]+')
    ->name('site.booking.confirmation');

Route::get('/booking/portal/{reference}', [BookingPortalController::class, 'show'])
    ->middleware('signed')
    ->where('reference', '[A-Za-z0-9\-]+')
    ->name('site.booking.portal');
Route::post('/booking/portal', [BookingPortalController::class, 'store'])
    ->middleware('throttle:15,1')
    ->name('site.booking.portal.login');

Route::get('/cart', [SiteCartController::class, 'show'])->name('site.cart');
Route::post('/cart/add', [SiteCartController::class, 'add'])->middleware('throttle:30,1')->name('site.cart.add');
Route::post('/cart/remove', [SiteCartController::class, 'remove'])->middleware('throttle:30,1')->name('site.cart.remove');

Route::post('/contact', [SiteContactController::class, 'store'])->middleware('throttle:20,1')->name('site.contact.submit');
Route::post('/newsletter', [SiteNewsletterController::class, 'store'])->middleware('throttle:15,1')->name('site.newsletter.subscribe');
Route::get('/invoice/{token}', [SiteInvoiceController::class, 'show'])
    ->where('token', '[A-Za-z0-9]+')
    ->name('site.invoice.show');
Route::get('/invoice/{token}/export', [SiteInvoiceController::class, 'exportCsv'])
    ->where('token', '[A-Za-z0-9]+')
    ->name('site.invoice.export');

Route::get('/sitemap.xml', SiteSitemapController::class)->name('site.sitemap');
Route::get('/search', SiteSearchController::class)->name('site.search');
Route::get('/our-properties', SiteBranchesController::class)->name('site.branches');
Route::get('/media/{path}', [PublicMediaController::class, 'show'])
    ->where('path', '.*')
    ->name('site.media.show');

Route::get('/pay/{reference}', [PesapalController::class, 'payNow'])
    ->where('reference', '[A-Za-z0-9\-]+')
    ->name('pay.now');

Route::get('/my-booking/{token}', [GuestStayController::class, 'show'])
    ->where('token', '[a-zA-Z0-9]+')
    ->name('site.guest-stay.show');
Route::post('/my-booking/{token}/room-service', [GuestStayController::class, 'storeRoomService'])
    ->middleware('throttle:20,1')
    ->where('token', '[a-zA-Z0-9]+')
    ->name('site.guest-stay.room-service');
Route::get('/my-booking/{token}/restaurant', GuestRestaurantAccessController::class)
    ->where('token', '[a-zA-Z0-9]+')
    ->name('site.guest-stay.restaurant');
Route::get('/room-menu/{token}', [KitchenQrMenuController::class, 'show'])
    ->where('token', '[a-z0-9]+')
    ->name('site.kitchen-menu.show');
Route::post('/room-menu/{token}', [KitchenQrMenuController::class, 'store'])
    ->middleware('throttle:20,1')
    ->where('token', '[a-z0-9]+')
    ->name('site.kitchen-menu.store');
Route::post('/room-service-orders/{reference}/payment', [RoomServiceOrderPaymentController::class, 'store'])
    ->middleware('throttle:15,1')
    ->where('reference', '[A-Za-z0-9\-]+')
    ->name('site.room-service-orders.payment');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified', 'active.account'])->group(function () {
    Route::get('/dashboard', MemberDashboardController::class)->name('dashboard');
    Route::get('/dashboard/properties', [MemberPropertiesController::class, 'index'])->name('member.properties.index');
    Route::get('/account/customer', [MemberAccountController::class, 'customer'])->name('account.customer');
    Route::get('/account/search', MemberSearchController::class)->name('member.search');

    Route::get('/room-service', [RoomServiceController::class, 'index'])->name('member.room-service.index');
    Route::post('/room-service', [RoomServiceController::class, 'store'])->middleware('throttle:20,1')->name('member.room-service.store');

    Route::post('/dashboard/branch-filter', [MemberBranchFilterController::class, 'store'])->name('member.branch-filter');
    Route::get('/dashboard/hotel-services', MemberHotelServiceCatalogController::class)->name('member.hotel-services.catalog');
    Route::post('/bookings/{booking}/hotel-service-requests', [MemberHotelServiceRequestController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('member.booking-service-requests.store');
    Route::get('/restaurant', MemberRestaurantController::class)->name('member.restaurant.launch');

    Route::get('/bookings', [MemberBookingController::class, 'index'])->name('bookings.index');
    Route::get('/invoices', MemberInvoiceController::class)->name('member.invoices.index');
    Route::get('/notifications', MemberNotificationController::class)->name('member.notifications.index');
    Route::patch('/bookings/{booking}/dates', [MemberBookingController::class, 'updateDates'])->name('bookings.update-dates');
    Route::post('/bookings/{booking}/invoice/resend', [MemberBookingController::class, 'resendInvoice'])
        ->middleware('throttle:6,1')
        ->name('bookings.invoice.resend');
    Route::get('/bookings/{booking}', [MemberBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/extend', [MemberBookingController::class, 'requestExtend'])
        ->middleware('throttle:10,1')
        ->name('bookings.extend');
});

Route::middleware(['auth', 'active.account'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'active.account', 'admin.panel'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::post('branch-scope', [AdminBranchScopeController::class, 'store'])
            ->middleware('permission:switch-branch-scope')
            ->name('branch-scope');
        Route::get('payments/pending', [AdminPendingPaymentsController::class, 'index'])
            ->middleware('permission:manage-bookings')
            ->name('payments.pending');
        Route::post('payments/pending/{booking}/confirm', [AdminPendingPaymentsController::class, 'confirm'])
            ->middleware('permission:manage-bookings')
            ->name('payments.confirm');
        Route::post('payments/pending/{booking}/cancel', [AdminPendingPaymentsController::class, 'cancel'])
            ->middleware(['permission:manage-bookings', 'permission:cancel-pending-bookings'])
            ->name('payments.cancel');
        Route::post('payments/pending/{booking}/resend-reminder', [AdminPendingPaymentsController::class, 'resendReminder'])
            ->middleware(['permission:manage-bookings', 'permission:resend-payment-reminders'])
            ->name('payments.resend-reminder');
        Route::middleware('permission:manage-payment-methods')->group(function () {
            Route::get('payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
            Route::post('payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
            Route::put('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
            Route::post('payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggle'])->name('payment-methods.toggle');
            Route::delete('payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');
        });
        Route::middleware('permission:manage-dashboard-notifications')->group(function () {
            Route::get('notifications', [DashboardNotificationController::class, 'index'])->name('notifications.index');
            Route::post('notifications/{notification}/read', [DashboardNotificationController::class, 'read'])->name('notifications.read');
            Route::post('notifications/{notification}/signout', [DashboardNotificationController::class, 'resolveSignOut'])->name('notifications.signout');
        });
        Route::middleware('permission:manage-bookings')->group(function () {
            Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
            Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
            Route::put('bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
        });
        Route::delete('bookings/{booking}', [AdminBookingController::class, 'destroy'])
            ->middleware('permission:delete-bookings')
            ->name('bookings.destroy');
        Route::post('invoices/{invoice}/resend', [AdminInvoiceController::class, 'resend'])
            ->middleware('permission:manage-invoices')
            ->name('invoices.resend');
        Route::middleware('permission:manage-customers')->group(function () {
            Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::post('customers/{customer}/toggle', [CustomerController::class, 'toggle'])->name('customers.toggle');
            Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        });
        Route::resource('maintenance', RoomMaintenanceController::class)
            ->middleware('permission:manage-maintenance')
            ->except(['show']);
        Route::get('search', AdminSearchController::class)->name('search');
        Route::get('reports/export', [ReportController::class, 'export'])
            ->middleware('permission:export-reports')
            ->name('reports.export');
        Route::get('reports/{type}', [ReportController::class, 'show'])
            ->middleware('permission:view-reports,view-dashboard-analytics')
            ->where('type', 'summary|bookings|customers|rooms|maintenance|full')
            ->name('reports.show');
        Route::get('reports', [ReportController::class, 'index'])
            ->middleware('permission:view-reports,view-dashboard-analytics')
            ->name('reports.index');
        Route::get('settings', [SystemSettingsController::class, 'edit'])
            ->middleware('permission:manage-system-settings')
            ->name('settings.edit');
        Route::put('settings', [SystemSettingsController::class, 'update'])
            ->middleware('permission:manage-system-settings')
            ->name('settings.update');
        Route::resource('roles', RoleController::class)
            ->middleware('permission:manage-roles')
            ->except(['show']);
        Route::resource('permissions', PermissionController::class)
            ->middleware('permission:manage-permissions')
            ->except(['show']);
        Route::resource('users', UserController::class)
            ->middleware('permission:manage-users,manage-staff-users')
            ->except(['show']);
        Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->middleware('permission:manage-users,manage-staff-users')
            ->name('users.toggle-active');
        Route::middleware('permission:manage-contacts')->group(function () {
            Route::get('contacts', [ContactMessageController::class, 'index'])->name('contacts.index');
            Route::post('contacts/{message}/reply', [ContactMessageController::class, 'reply'])->name('contacts.reply');
            Route::delete('contacts/{message}', [ContactMessageController::class, 'destroy'])->name('contacts.destroy');
        });
        Route::middleware('permission:manage-newsletters')->group(function () {
            Route::get('emails', [NewsletterSubscriptionController::class, 'index'])->name('emails.index');
            Route::delete('emails/{subscription}', [NewsletterSubscriptionController::class, 'destroy'])->name('emails.destroy');
        });
        Route::resource('branches', HotelBranchController::class)
            ->middleware('permission:manage-branches')
            ->except(['show']);
        Route::get('properties', [PropertiesDirectoryController::class, 'index'])
            ->middleware('permission:manage-properties-directory')
            ->name('properties.index');
        Route::middleware('permission:manage-media-library')->group(function () {
            Route::get('media-library', [MediaLibraryController::class, 'index'])->name('media-library.index');
            Route::post('media-library', [MediaLibraryController::class, 'store'])->name('media-library.store');
            Route::delete('media-library/{asset}', [MediaLibraryController::class, 'destroy'])->name('media-library.destroy');
        });
        Route::post('rooms/{room}/toggle-in-use', [RoomController::class, 'toggleInUse'])
            ->middleware('permission:manage-rooms')
            ->name('rooms.toggle-in-use');
        Route::resource('room-types', RoomTypeController::class)
            ->middleware('permission:manage-room-categories')
            ->except(['show']);
        Route::resource('rooms', RoomController::class)
            ->middleware('permission:manage-rooms')
            ->except(['show']);
        Route::resource('hotel-services', ReceptionHotelServiceController::class)
            ->middleware('permission:manage-hotel-services')
            ->except(['show']);
        Route::get('/reception/rooms/{room}/booked-dates', [ReceptionBookingController::class, 'getBookedDates']);

    });
// Hakikisha ipo ndani ya middleware 'auth' ili receptionist aweze kuiona
Route::middleware(['auth'])->group(function () {
    Route::get('/reception/rooms/{room}/booked-dates', [App\Http\Controllers\Reception\ReceptionBookingController::class, 'getBookedDates'])
         ->name('reception.rooms.booked-dates');
});

Route::middleware(['auth', 'verified', 'active.account', 'staff.panel'])
    ->prefix('kitchen')
    ->name('kitchen.')
    ->middleware('permission:access-kitchen-panel')
    ->group(function () {
        Route::get('/', KitchenDashboardController::class)->name('dashboard');
        Route::get('orders', [KitchenOrderController::class, 'index'])
            ->middleware('permission:manage-kitchen-orders')
            ->name('orders.index');
        Route::post('orders/{order}', [KitchenOrderController::class, 'update'])
            ->middleware('permission:manage-kitchen-orders')
            ->name('orders.update');
        Route::get('assignments', [KitchenAssignmentController::class, 'index'])
            ->middleware('permission:assign-kitchen-orders')
            ->name('assignments.index');
        Route::post('assignments/orders/{order}', [KitchenAssignmentController::class, 'assign'])
            ->middleware('permission:assign-kitchen-orders')
            ->name('assignments.store');
        Route::get('staff', [KitchenStaffController::class, 'index'])
            ->middleware('permission:manage-kitchen-staff')
            ->name('staff.index');
        Route::post('staff', [KitchenStaffController::class, 'store'])
            ->middleware('permission:manage-kitchen-staff')
            ->name('staff.store');
        Route::put('staff/{user}', [KitchenStaffController::class, 'update'])
            ->middleware('permission:manage-kitchen-staff')
            ->name('staff.update');
        Route::delete('staff/{user}', [KitchenStaffController::class, 'destroy'])
            ->middleware('permission:manage-kitchen-staff')
            ->name('staff.destroy');
        Route::get('roles', [KitchenRoleController::class, 'index'])
            ->middleware('permission:manage-kitchen-roles')
            ->name('roles.index');
        Route::post('roles', [KitchenRoleController::class, 'store'])
            ->middleware('permission:manage-kitchen-roles')
            ->name('roles.store');
        Route::put('roles/matrix', [KitchenRoleController::class, 'updateMatrix'])
            ->middleware('permission:manage-kitchen-roles')
            ->name('roles.matrix.update');
        Route::put('roles/{role}', [KitchenRoleController::class, 'update'])
            ->middleware('permission:manage-kitchen-roles')
            ->name('roles.update');
        Route::get('menu', [KitchenMenuController::class, 'index'])
            ->middleware('permission:manage-kitchen-menu')
            ->name('menu.index');
        Route::post('menu', [KitchenMenuController::class, 'store'])
            ->middleware('permission:manage-kitchen-menu')
            ->name('menu.store');
        Route::put('menu/{menuItem}', [KitchenMenuController::class, 'update'])
            ->middleware('permission:manage-kitchen-menu')
            ->name('menu.update');
        Route::delete('menu/{menuItem}', [KitchenMenuController::class, 'destroy'])
            ->middleware('permission:manage-kitchen-menu')
            ->name('menu.destroy');
        Route::get('settings', [KitchenSettingsController::class, 'edit'])
            ->middleware('permission:access-kitchen-panel')
            ->name('settings.edit');
        Route::put('settings', [KitchenSettingsController::class, 'update'])
            ->middleware('permission:access-kitchen-panel')
            ->name('settings.update');
        Route::get('qr-codes', [KitchenQrController::class, 'index'])
            ->middleware('permission:generate-kitchen-qr')
            ->name('qr.index');
        Route::post('qr-codes/{room}', [KitchenQrController::class, 'store'])
            ->middleware('permission:generate-kitchen-qr')
            ->name('qr.store');
        Route::get('reports', [KitchenReportController::class, 'index'])
            ->middleware('permission:view-kitchen-reports')
            ->name('reports.index');
        Route::post('orders/{order}/confirm-paid', [KitchenOrderController::class, 'confirmPaid'])
            ->middleware('permission:manage-kitchen-orders')
            ->name('orders.confirm-paid');
        Route::post('notifications/{notification}/read', [KitchenNotificationController::class, 'read'])
            ->name('notifications.read');
        Route::post('orders/{order}/generate-bill', [RoomServiceBillController::class, 'generate'])
            ->middleware('permission:manage-kitchen-orders')
            ->name('orders.bill.generate');
        Route::get('orders/{order}/bill', [RoomServiceBillController::class, 'show'])
            ->middleware('permission:manage-kitchen-orders')
            ->name('orders.bill.show');
    });

Route::middleware(['auth', 'verified', 'active.account', 'staff.panel'])
    ->prefix('reception')
    ->name('reception.')
    ->group(function () {
        Route::get('/', ReceptionDashboardController::class)->name('dashboard');
        Route::post('branch', [ReceptionBranchController::class, 'store'])->name('branch');

        Route::get('properties', [ReceptionPropertiesController::class, 'index'])
            ->middleware('permission:manage-bookings')
            ->name('properties.index');
        Route::get('analytics/branches', ReceptionBranchAnalyticsController::class)->name('analytics.branches');
        Route::get('reports', [ReceptionReportController::class, 'index'])
            ->middleware('permission:view-reception-reports')
            ->name('reports.index');
        Route::get('contacts', [ReceptionContactController::class, 'index'])
            ->middleware('permission:manage-bookings')
            ->name('contacts.index');
        Route::post('contacts/{message}/reply', [ReceptionContactController::class, 'reply'])
            ->middleware('permission:manage-bookings')
            ->name('contacts.reply');
        Route::delete('contacts/{message}', [ReceptionContactController::class, 'destroy'])
            ->middleware('permission:manage-bookings')
            ->name('contacts.destroy');

        Route::middleware('permission:manage-bookings')->group(function () {
            Route::get('bookings/create', [ReceptionBookingController::class, 'create'])->name('bookings.create');
            Route::post('bookings', [ReceptionBookingController::class, 'store'])->name('bookings.store');
            Route::get('bookings', [ReceptionBookingController::class, 'index'])->name('bookings.index');
            Route::post('bookings/{booking}/confirm-cash', [ReceptionBookingController::class, 'confirmCash'])->name('bookings.confirm-cash');
            Route::get('bookings/{booking}', [ReceptionBookingController::class, 'show'])->name('bookings.show');
            Route::put('bookings/{booking}', [ReceptionBookingController::class, 'update'])->name('bookings.update');
            Route::delete('bookings/{booking}', [ReceptionBookingController::class, 'destroy'])->name('bookings.destroy');
        });

        Route::middleware('permission:manage-customers')->group(function () {
            Route::get('customers', [ReceptionCustomerController::class, 'index'])->name('customers.index');
            Route::get('customers/{customer}', [ReceptionCustomerController::class, 'show'])->name('customers.show');
            Route::post('customers/{customer}/toggle', [ReceptionCustomerController::class, 'toggle'])->name('customers.toggle');
            Route::delete('customers/{customer}', [ReceptionCustomerController::class, 'destroy'])->name('customers.destroy');
        });

        Route::middleware('permission:manage-bookings')->group(function () {
            Route::get('notifications', [ReceptionNotificationController::class, 'index'])->name('notifications.index');
            Route::post('notifications/{notification}/read', [ReceptionNotificationController::class, 'read'])->name('notifications.read');
            Route::post('notifications/{notification}/signout', [ReceptionNotificationController::class, 'resolveSignOut'])->name('notifications.signout');
        });

        Route::post('invoices/{invoice}/resend', [ReceptionInvoiceController::class, 'resend'])
            ->middleware('permission:manage-invoices')
            ->name('invoices.resend');

        Route::middleware('permission:manage-bookings')->group(function () {
            Route::post('rooms/{room}/toggle-in-use', [ReceptionRoomController::class, 'toggleInUse'])->name('rooms.toggle-in-use');
            Route::resource('rooms', ReceptionRoomController::class)->except(['show']);
        });

        Route::resource('maintenance', ReceptionMaintenanceController::class)
            ->middleware('permission:manage-maintenance')
            ->except(['show']);

        Route::get('room-service', [ReceptionRoomServiceController::class, 'index'])
            ->middleware('permission:manage-room-service-reception')
            ->name('room-service.index');
        Route::post('room-service/{roomServiceOrder}', [ReceptionRoomServiceController::class, 'update'])
            ->middleware('permission:manage-room-service-reception')
            ->name('room-service.update');
        Route::post('room-service/{roomServiceOrder}/confirm-paid', [ReceptionRoomServiceController::class, 'confirmPaid'])
            ->middleware('permission:manage-room-service-reception')
            ->name('room-service.confirm-paid');
        Route::get('room-service/{order}/bill', [RoomServiceBillController::class, 'show'])
            ->middleware('permission:manage-room-service-reception')
            ->name('room-service.bill.show');

        Route::resource('hotel-services', ReceptionHotelServiceController::class)
            ->middleware('permission:manage-room-service-reception')
            ->except(['show']);
    });



// Routes za Pesapal
Route::get('/pesapal/callback', [PesapalController::class, 'callback'])->name('site.pesapal.callback');
Route::any('/pesapal/ipn', [PesapalController::class, 'ipn'])->name('api.pesapal.ipn');

Route::get('/{slug}', [SitePageController::class, 'show'])
    ->name('site.page')
    ->where('slug', '[A-Za-z0-9-]+');
