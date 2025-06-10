<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\NotificationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Handle OPTIONS requests globally for CORS
Route::get('/top-performing-employees', [CouponController::class, 'getTopPerformingEmployees']);

Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-CSRF-TOKEN');
})->where('any', '.*');

// Analytics routes with simple CORS
Route::get('/analytics', [AnalyticsController::class, 'getAnalytics']);
Route::get('/analytics/export', [AnalyticsController::class, 'exportAnalytics']);

// Your existing routes...
Route::get('/employee-count', function () {
    $count = Employee::count();
    return response()->json(['totalEmployees' => $count])
        ->header('Access-Control-Allow-Origin', '*');
});

Route::get('employees', [EmployeeController::class, 'index']);

Route::middleware('api')->group(function () {
    // Test route
    Route::get('test', function () {
        return response()->json([
            'message' => 'API is working!',
            'timestamp' => now(),
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');
    });

    // Employee routes
    Route::post('employees', [EmployeeController::class, 'store']);
    Route::get('employees/{id}', [EmployeeController::class, 'show']);
    Route::put('employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('employees/{id}', [EmployeeController::class, 'destroy']);
    
    // Legacy employee routes for compatibility
    Route::get('employee', [EmployeeController::class, 'index']);
    Route::post('employee', [EmployeeController::class, 'store']);
    Route::post('employee/{id}', [EmployeeController::class, 'update']);
    Route::put('employee/{id}', [EmployeeController::class, 'update']);
    
    // Coupon routes
    Route::prefix('coupons')->group(function () {
        Route::post('generate', [CouponController::class, 'generateCoupons']);
        Route::post('generate-all', [CouponController::class, 'generateCouponsForAll']);
        Route::get('scan/{barcode}', [CouponController::class, 'scanCoupon']);
        Route::post('{id}/claim', [CouponController::class, 'claimCoupon']);
        Route::get('/', [CouponController::class, 'getCoupons']);
        Route::get('statistics', [CouponController::class, 'getStatistics']);
    });
});

Route::get('/coupons/stats', [CouponController::class, 'getEmployeeCouponCount']);
Route::get('/coupons/claimed-stats', [CouponController::class, 'getClaimedCouponsCount']);


//new api
Route::get('/analytics', [AnalyticsController::class, 'getAnalytics']);
Route::get('/top-performing-employees', [CouponController::class, 'getTopPerformingEmployees']);

// New Dynamic Routes
Route::prefix('analytics')->group(function () {
    Route::get('/live', [AnalyticsController::class, 'getLiveAnalytics']);
    Route::get('/departments', [AnalyticsController::class, 'getDepartmentAnalytics']);
    Route::get('/departments-dynamic', [AnalyticsController::class, 'getDynamicDepartmentAnalytics']);
    Route::get('/usage-alerts', [AnalyticsController::class, 'getUsageAlerts']);
});

// Notification Routes
Route::prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/stream', [NotificationController::class, 'stream']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
});

// Coupon Routes
Route::prefix('coupons')->group(function () {
    Route::get('/expiring-soon', [CouponController::class, 'getExpiringSoon']);
});

Route::get('/notifications', [NotificationController::class, 'index']);
Route::get('/notifications/stream', [NotificationController::class, 'stream']);
Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
Route::post('/notifications/generate', [NotificationController::class, 'generate']);