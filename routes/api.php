<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\AdminTicketController;
use Illuminate\Http\Request;

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login_admin', [AuthController::class, 'login_admin'])->name('login_admin');

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/tickets', [TicketController::class, 'index']);

    Route::post('/tickets', [TicketController::class, 'store']);

    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);

    Route::prefix('admin')->group(function () {

        Route::get('/tickets/admin1', [AdminTicketController::class, 'pendingAdmin1']);
        Route::get('/tickets/admin2', [AdminTicketController::class, 'pendingAdmin2']);

        Route::post('/tickets/{ticket}/approve-admin-1', [AdminTicketController::class,'approveAdmin1']);
        Route::post('/tickets/{ticket}/reject-admin-1', [AdminTicketController::class,'rejectAdmin1']);

        Route::post('/tickets/{ticket}/approve-admin-2', [AdminTicketController::class,'approveAdmin2']);
        Route::post('/tickets/{ticket}/reject-admin-2', [AdminTicketController::class,'rejectAdmin2']);

        Route::post('/tickets/bulk-approve-admin-1', [AdminTicketController::class, 'bulkApproveAdmin1']);
        Route::post('/tickets/bulk-approve-admin-2', [AdminTicketController::class, 'bulkApproveAdmin2']);
        Route::post('/tickets/bulk-reject-admin-1', [AdminTicketController::class, 'bulkRejectAdmin1']);
        Route::post('/tickets/bulk-reject-admin-2', [AdminTicketController::class, 'bulkRejectAdmin2']);
    });

    Route::post('/fake-external-service', function ()
    {
        $random = random_int(1, 100);
        if ($random <= 30)
        {
            return response()->json([
                'message' => 'External service failed'
            ], 500);
        }

        return response()->json([
            'message' => 'Ticket processed successfully'
        ]);
    });
});
Route::get('/test-error', function () {
    throw new Exception("Test error");
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
