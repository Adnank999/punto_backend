<?php

use App\Http\Controllers\BusController;
use App\Http\Controllers\BusStatusController;
use App\Http\Controllers\BusStopController;
use App\Http\Controllers\CalculationController;
use App\Http\Controllers\RouteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::get('/buses', [BusController::class, 'index']);
Route::post('/buses', [BusController::class, 'store']);
Route::put('/buses/{bus}', [BusController::class, 'update']);
Route::delete('/buses/{bus}', [BusController::class, 'destroy']);


Route::get('/bus-stops', [BusStopController::class, 'index']);
Route::post('/busStops/create', [BusStopController::class, 'store']);
Route::put('/bus-stops/{busStop}', [BusStopController::class, 'update']);
Route::delete('/bus-stops/{busStop}', [BusStopController::class, 'destroy']);


Route::get('/routes', [RouteController::class, 'index']);
Route::post('/routes', [RouteController::class, 'store']);
Route::put('/routes/{route}', [RouteController::class, 'update']);
Route::delete('/routes/{route}', [RouteController::class, 'destroy']);





Route::post('/bus-status', [BusStatusController::class, 'update']);


Route::post('/calculate-distance', [CalculationController::class, 'calculateDistance']);
Route::post('/calculate-travel-time', [CalculationController::class, 'calculateTravelTime']);
Route::post('/bus/get-nearest-busstop', [CalculationController::class, 'getNearestBusStop']);
Route::post('/bus/determine-direction', [CalculationController::class, 'determineDirection']);
Route::post('/bus/estimated-arrival', [CalculationController::class, 'calculateRemainingTime']);




Route::post('/check-bus-departure', [CalculationController::class, 'checkIfBusDeparted']);



/* most important calculation Testing */

Route::post('bus-stops/{busStopId}', [CalculationController::class, 'getBusStopDetails']);

Route::post('/bus/estimated-arrival2', [CalculationController::class, 'getBusDetails']);