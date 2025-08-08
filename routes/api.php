<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\ServicesController as AdminServicesController;

use App\Http\Controllers\Api\Admin\RoomsController as AdminRoomsController;

use App\Http\Controllers\Api\Admin\PaymentSettingsController;
use App\Http\Controllers\Api\Admin\SmtpSettingsController;


use App\Http\Controllers\Api\Admin\FinanceController;
use App\Http\Controllers\Api\Admin\EmployeeController;
use App\Http\Controllers\Api\Admin\CampaignController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\GalleryController;



use App\Http\Controllers\Api\Customer\ServicesController;
use App\Http\Controllers\Api\Customer\RoomsController;
use App\Http\Controllers\Api\Customer\PaymentController;


use App\Http\Controllers\Api\Customer\BookingController;

use App\Http\Controllers\Api\Admin\BookingsController as BackendBookingsController;


use App\Http\Controllers\Api\Admin\BusinessHoursController;

use App\Http\Controllers\Api\Customer\AuthController;


use App\Http\Controllers\Api\Auth\PasswordResetController;

Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);



Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'getUser']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::get('/services', [ServicesController::class, 'getAllServices']);
Route::get('/popular-services', [ServicesController::class, 'getPopularServices']);
Route::get('services/{service}', [ServicesController::class, 'show']); // Fetch 



Route::get('/rooms', [RoomsController::class, 'getAllRooms']);
Route::get('/popular-rooms', [RoomsController::class, 'getPopularRooms']);
Route::get('rooms/{room}', [RoomsController::class, 'show']); // Fetch 

Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/paypal/verify', [PaymentController::class, 'verifyPaypalPayment']);





Route::get('/unavailable-dates', [BookingController::class, 'getUnavailableDates']);
Route::post('/book', [BookingController::class, 'createBooking']);

Route::put('/bookings/{id}/payment', [BookingController::class, 'updatePaymentStatus']);

Route::get('/payment-settings', [PaymentSettingsController::class, 'index']);
Route::get('/smtp-settings', [SmtpSettingsController::class, 'index']);


Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {


    Route::get('reports/summary', [ReportController::class, 'summary']);
    Route::get('reports/popular-rooms', [ReportController::class, 'popularRooms']);
    Route::get('reports/top-clients', [ReportController::class, 'topClients']);



Route::get('/payment-settings/{id}', [PaymentSettingsController::class, 'show']);
Route::put('/payment-settings/{id}', [PaymentSettingsController::class, 'update']);


Route::get('/smtp-settings/{id}', [SmtpSettingsController::class, 'show']);
Route::put('/smtp-settings/{id}', [SmtpSettingsController::class, 'update']);



Route::get('gallery', [GalleryController::class, 'index']);
Route::post('gallery', [GalleryController::class, 'store']);
Route::delete('gallery/{id}', [GalleryController::class, 'destroy']);


    Route::apiResource('business-hours', BusinessHoursController::class);

        Route::apiResource('services', AdminServicesController::class);
        
Route::apiResource('rooms', AdminRoomsController::class);

        

Route::apiResource('employees', EmployeeController::class);

Route::apiResource('campaigns', CampaignController::class);

Route::apiResource('products', ProductController::class);



         Route::put('/services/{serviceId}/toggle-featured', [AdminServicesController::class, 'toggleFeatured']);

Route::apiResource('finances', FinanceController::class);



Route::apiResource('bookings', BackendBookingsController::class);


});