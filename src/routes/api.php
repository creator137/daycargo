<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TariffController;
use App\Http\Controllers\Api\CalculatorController;
use App\Http\Controllers\Api\AddressSearchController;
use App\Http\Controllers\Api\BusAuthController;
use App\Http\Controllers\Api\DriverVacancyController;

Route::get('tariffs', [TariffController::class, 'index']);
Route::get('/health', fn() => response()->json(['status' => 'ok']));
Route::post('/calculate', [CalculatorController::class, 'calculate']);
Route::get('/address-search', [AddressSearchController::class, 'search']);
Route::post('/driver-vacancy', [DriverVacancyController::class, 'store']);
Route::post('/bus/register', [BusAuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/bus/profile', [BusAuthController::class, 'profile']);
