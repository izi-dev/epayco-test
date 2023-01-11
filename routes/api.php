<?php

use App\Http\Controllers\ConfirmedPayController;
use App\Http\Controllers\PayWalletController;
use App\Http\Controllers\RechargeWalletController;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\UserAccountController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register-user', RegisterUserController::class)->name('register-user');
Route::post('/recharge-wallet', RechargeWalletController::class)->name('recharge-wallet');
Route::post('/pay-wallet', PayWalletController::class)->name('pay-wallet');
Route::post('/confirmed-pay', ConfirmedPayController::class)->name('confirmed-pay');
Route::get('/user/account', UserAccountController::class)->name('user.account');

