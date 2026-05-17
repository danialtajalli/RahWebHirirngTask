<?php

use App\Http\Controllers\Api\AdminTicketController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::view('/app/{any?}', 'user_app')->where('any', '.*');
Route::view('/admin/{any?}', 'admin_app')->where('any', '.*');
