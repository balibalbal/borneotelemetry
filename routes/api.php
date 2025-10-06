<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiLoginController;
use App\Http\Controllers\API\ApiDashboardController;
use App\Http\Controllers\API\ApiDriverController;
use App\Http\Controllers\API\ApiDeviceController;
use App\Http\Controllers\API\ApiTraccarController;
use App\Http\Controllers\API\ApiUserController;
use App\Http\Controllers\API\ApiTrackingController;
use App\Http\Controllers\API\ApiReportController;
use App\Http\Controllers\API\ApiVehicleController;
use App\Http\Controllers\API\ApiInformationController;
use App\Http\Controllers\API\ApiUploadController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [ApiLoginController::class,'login']);
Route::post('/logout', [ApiLoginController::class, 'logout'])->middleware('auth:sanctum');

Route::get('dashboard', [ApiDashboardController::class,'index'])->middleware('auth:sanctum');
Route::get('device', [ApiDeviceController::class,'index'])->middleware('auth:sanctum');
Route::get('traccar', [ApiTraccarController::class,'index'])->middleware('auth:sanctum');
Route::get('information', [ApiInformationController::class,'index'])->middleware('auth:sanctum');
Route::get('user', [ApiUserController::class,'index'])->middleware('auth:sanctum');
Route::get('report/last_position', [ApiReportController::class,'last_position'])->middleware('auth:sanctum');
Route::get('report/historical', [ApiReportController::class,'historical'])->middleware('auth:sanctum');
Route::get('report/parking', [ApiReportController::class,'parking'])->middleware('auth:sanctum');
Route::get('report/distance', [ApiReportController::class,'distance'])->middleware('auth:sanctum');
Route::get('report/jarak', [ApiReportController::class,'jarak'])->middleware('auth:sanctum');
Route::get('report/speed', [ApiReportController::class,'speed'])->middleware('auth:sanctum');
Route::get('tracking', [ApiTrackingController::class,'index'])->middleware('auth:sanctum');
Route::get('vehicle', [ApiVehicleController::class,'index'])->middleware('auth:sanctum');
Route::get('driver', [ApiDriverController::class,'index'])->middleware('auth:sanctum');
Route::get('driver/{id}', [ApiDriverController::class,'show'])->middleware('auth:sanctum');
Route::get('vehicle/{id}', [ApiVehicleController::class,'show'])->middleware('auth:sanctum');

Route::post('/upload', [ApiUploadController::class, 'upload'])->middleware('auth:sanctum');