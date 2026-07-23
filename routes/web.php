<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\VehicleController as VehiclesController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\SpjController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\InvoiceNotificationController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\SmtpSettingsController;
use App\Http\Controllers\NotificationSoundSettingsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserBookingController;
use App\Http\Controllers\BookingDataController;
use App\Http\Controllers\TermsController;

Route::redirect('/', '/booking');

// Public booking form
Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dev/login-superadmin', function () {
    if (!app()->isLocal()) {
        abort(403);
    }
    $user = \App\Models\User::where('email', 'herurizkyfajar@gmail.com')->first();
    if (!$user) {
        return redirect()->route('login')->with('error', 'Super Admin belum tersedia.');
    }
    \Illuminate\Support\Facades\Auth::login($user);
    return redirect()->route('dashboard')->with('success', 'Login sebagai Super Admin berhasil.');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::get('/dashboard/calendar', [DashboardController::class, 'calendar'])->name('dashboard.calendar')->middleware('auth');
Route::post('/dashboard/test-email', [DashboardController::class, 'sendTestEmail'])
    ->name('dashboard.test-email')
    ->middleware(['auth', 'role:super_admin']);
Route::get('/settings/smtp', [SmtpSettingsController::class, 'index'])
    ->name('settings.smtp')
    ->middleware(['auth', 'role:super_admin']);
Route::put('/settings/smtp', [SmtpSettingsController::class, 'update'])
    ->name('settings.smtp.update')
    ->middleware(['auth', 'role:super_admin']);
Route::get('/settings/notification-sound', [NotificationSoundSettingsController::class, 'index'])
    ->name('settings.notification-sound')
    ->middleware(['auth', 'role:super_admin']);
Route::put('/settings/notification-sound', [NotificationSoundSettingsController::class, 'update'])
    ->name('settings.notification-sound.update')
    ->middleware(['auth', 'role:super_admin']);
Route::get('/settings/invoice', [SettingsController::class, 'edit'])
    ->name('settings.invoice.edit')
    ->middleware(['auth', 'role:super_admin']);
Route::put('/settings/invoice', [SettingsController::class, 'update'])
    ->name('settings.invoice.update')
    ->middleware(['auth', 'role:super_admin']);
Route::get('/settings/terms', [TermsController::class, 'edit'])
    ->name('settings.terms.edit')
    ->middleware(['auth', 'role:super_admin']);
Route::put('/settings/terms', [TermsController::class, 'update'])
    ->name('settings.terms.update')
    ->middleware(['auth', 'role:super_admin']);
Route::get('/email-logs', [EmailLogController::class, 'index'])
    ->name('email-logs.index')
    ->middleware(['auth', 'role:super_admin']);
Route::get('/notifications/invoices/latest', [InvoiceNotificationController::class, 'latest'])
    ->name('notifications.invoices.latest')
    ->middleware(['auth', 'role:super_admin']);
Route::post('/notifications/push/subscribe', [PushSubscriptionController::class, 'store'])
    ->name('notifications.push.subscribe')
    ->middleware(['auth', 'role:super_admin']);
Route::post('/notifications/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])
    ->name('notifications.push.unsubscribe')
    ->middleware(['auth', 'role:super_admin']);

// Management (example restrict later with middleware checking role)
Route::get('/invoice/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show')->middleware(['auth','role:super_admin']);
Route::get('/invoice/share/{invoice}', [InvoiceController::class, 'show'])->name('invoice.share')->middleware('signed');
Route::post('/invoice/send/{invoice}', [InvoiceController::class, 'sendWhatsapp'])->name('invoice.send')->middleware(['auth','role:super_admin']);
Route::get('/spj/{booking}', [SpjController::class, 'show'])->name('spj.show')->middleware(['auth','role:super_admin']);
// Vehicles CRUD removed

// Drivers CRUD removed
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::resource('mitras', MitraController::class);
    Route::resource('bookings', BookingsController::class)->except(['show']);
    Route::patch('/bookings/{booking}/phase', [BookingsController::class, 'updatePhase'])->name('bookings.phase.update');
    Route::get('/bookings-trash', [BookingsController::class, 'trash'])->name('bookings.trash');
    Route::post('/bookings/{id}/restore', [BookingsController::class, 'restore'])->name('bookings.restore');
    Route::delete('/bookings/{id}/force-delete', [BookingsController::class, 'forceDelete'])->name('bookings.force-delete');
    Route::resource('vehicles', VehiclesController::class)->except(['show']);
    Route::resource('services', ServiceController::class)->except(['show']);
    Route::resource('groups', GroupController::class)->except(['show']);
    Route::get('/booking-data', [BookingDataController::class, 'index'])->name('booking-data.index');
});

Route::middleware(['auth', 'role:super_admin,user'])->group(function () {
    Route::resource('accounts', AccountController::class);
    Route::resource('itineraries', ItineraryController::class)->except(['show']);
    Route::get('itineraries/{itinerary}/pdf', [ItineraryController::class, 'pdf'])->name('itineraries.pdf');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/my-bookings', [UserBookingController::class, 'index'])->name('user.bookings.history');
    Route::delete('/my-bookings/{booking}', [UserBookingController::class, 'destroy'])->name('user.bookings.destroy');
});

// Debug route removed after verification
