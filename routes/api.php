<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CurrenciesController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\FundsController;

Route::get('countries', [CountriesController::class, 'index']);
Route::group(
    ['namespace' => 'Auth'], 
    function () {
        Route::get('logout', [LoginController::class, 'logout']);
        Route::post('register', [RegisterController::class, 'register']);
        Route::post('login', [LoginController::class, 'login']);
    }
);

Route::group(
    [
        'namespace' => 'Auth', 
        'middleware', 'auth:api'
    ], 
    function () {
        Route::get('send-two-factor-token', [LoginController::class, 'sendtwoFactorToken']);
        Route::post('post-two-factor-token', [LoginController::class, 'postTwoFactorToken']);
    }
);

Route::group(
    ['middleware' => 'auth:api'],
    function () {
        Route::get('currencies', [CurrenciesController::class, 'index']);
        Route::get('wallets/{wallet}/transactions', [WalletsController::class, 'getTransactions']);
        Route::apiResource('wallets', WalletsController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('wallet.address', AddressController::class)->only(['index','store']);
        Route::apiResource('wallet.fund', FundsController::class)->only(['index', 'store']);
    }
);
