<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationCodeController;
use App\Services\CustomResponse;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['json', 'throttle:60,1']], function () {
	Route::get('/', function () {
		return CustomResponse::successResponse('Welcome to Accounter');
	});

	//register
	Route::post('/register', [AuthController::class, 'register'])->name('register');
	//login
	Route::post('/login', [AuthController::class, 'login'])->name('login');
	//resend verification code
	Route::post('/resend-verify-code', [VerificationCodeController::class, 'resendVerificationCode']);
	//verify email
	Route::post('/verify-email', [VerificationCodeController::class, 'verifyEmail']);
	//request reset password code
	Route::post('/request-reset-password', [VerificationCodeController::class, 'requestPasswordResetCode']);
	//reset password
	Route::post('/reset-password', [VerificationCodeController::class, 'resetPassword']);

	//protected routes
	Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
		Route::prefix('profile')->group(function () {
			//view your profile
			Route::get('/', [AuthController::class, 'profile'])->name('profile');
			//update
			Route::post('/', [AuthController::class, 'update'])->name('profile.update');
			//logout
			Route::post('/logout', [AuthController::class, 'logout'])->name('profile.logout');
		});

		Route::prefix('account')->group( function (){
			//get account
			Route::get('/{account}', [AccountController::class, 'show'])->name('account.get');
			//create account
			Route::post('/', [AccountController::class, 'store'])->name('account.create');
			//update account
			Route::patch('/{account}', [AccountController::class, 'update'])->name('account.update');
			//delete account
			Route::delete('/{account}', [AccountController::class, 'destroy'])->name('account.delete');
		});

		Route::get('accounts', [AccountController::class, 'getAll'])->name('accounts.get');
	});

});