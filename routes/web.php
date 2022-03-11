<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::post('/payment', [PaymentController::class, 'redirectPayments']);
Route::post('/BCA/payment', [MidtransController::class, 'createBCAPayment']);
Route::post('/gopay/payment', [MidtransController::class, 'createGoPayPayment']);
