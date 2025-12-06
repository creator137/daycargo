<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TariffController;
use App\Http\Controllers\Api\CalculatorController;
use App\Http\Controllers\Api\AddressSearchController;

Route::get('tariffs', [TariffController::class, 'index']);
Route::get('/health', fn() => response()->json(['status' => 'ok']));
Route::post('/calculate', [CalculatorController::class, 'calculate']);
Route::get('/address-search', [AddressSearchController::class, 'search']);
