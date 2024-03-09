<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Frontend\NotificationController;
use App\Http\Controllers\Frontend\PageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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



// Admin auth 
Route::get('admin/login', [AdminLoginController::class, 'showLoginForm']);
Route::post('admin/login', [AdminLoginController::class, 'login'])->name('admin.login');
Route::post('admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// User auth 
Auth::routes();
Route::post('login', [LoginController::class, 'login'])->name('login')->middleware('account_verified');
Route::get('/account/{hashed_code}', [RegisterController::class, 'status'])->name('account.status');
Route::get('/resend-email', [RegisterController::class, 'resend'])->name('resendEmail');
Route::get('/account/verification/{key}', [RegisterController::class, 'verification'])->name('account.verification');
Route::get('/otp', [RegisterController::class, 'otp'])->name('otp');
Route::post('/post_register', [RegisterController::class, 'post_register'])->name('post_register');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('home');

    Route::get('/profile', [PageController::class, 'profile'])->name('profile');
    Route::get('/update-password', [PageController::class, 'updatePassword'])->name('update-password');
    Route::post('/update-password', [PageController::class, 'updatePasswordStore'])->name('update-password.store');

    Route::get('/wallet', [PageController::class, 'wallet'])->name('wallet');

    Route::get('/transfer', [PageController::class, 'transfer'])->name('transfer');
    Route::get('/transfer/confirm', [PageController::class, 'transferConfirm'])->name('transfer.confirm');
    Route::post('/transfer/complete', [PageController::class, 'transferComplete'])->name('transfer.complete');

    Route::get('transaction', [PageController::class, 'transaction']);
    Route::get('transaction/{trx_id}', [PageController::class, 'transactionDetail']);

    Route::get('to-account-verify', [PageController::class, 'toAccountVerify'])->name('toAccountVerify');
    Route::get('transfer/confirm/password-check', [PageController::class, 'passwordCheck'])->name('passwordCheck');
    Route::get('transfer-data-encryption', [PageController::class, 'dataEncryption']);

    Route::get('receive-qr', [PageController::class, 'receiveQR']);
    Route::get('scan-and-pay', [PageController::class, 'scanAndPay']);
    Route::get('scan-and-pay-form', [PageController::class, 'scanAndPayForm']);
    Route::get('scan-and-pay-confirm', [PageController::class, 'scanAndPayConfirm']);
    Route::post('scan-and-pay-complete', [PageController::class, 'scanAndPayComplete']);
    
    Route::get('notification', [NotificationController::class, 'index']);
    Route::get('notification/{id}', [NotificationController::class, 'show']);
});

Route::get('/qr', function() {
    return view('qr');
});