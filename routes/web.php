<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MapDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\TraccarController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportAdminController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AssignRoleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AlarmController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\GeofenceSessionController;
use App\Http\Controllers\TransmissionController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\HsoMonitoringController;
use App\Http\Controllers\Modem\InformationController;
use App\Http\Controllers\Modem\TraccarModemController;
use App\Http\Controllers\Modem\TraccarMobileController;
use App\Http\Controllers\Modem\TransmissionModemController;
use App\Http\Controllers\Modem\AlarmController as ModemAlarmController;
use App\Http\Controllers\Modem\DeviceController as ModemDeviceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [DashboardController::class, 'index'])->middleware('auth');

// route untuk modem
Route::post('/modem/information', [InformationController::class, 'store']);
Route::post('/modem/traccar', [TraccarModemController::class, 'store']);
Route::post('/mobile/traccar', [TraccarMobileController::class, 'store']);
Route::post('/modem/alarm', [ModemAlarmController::class, 'store']);
Route::post('/modem/transmission', [TransmissionModemController::class, 'store']);
Route::get('/modem/check-imei/{imei}', [ModemDeviceController::class, 'checkImei']);
// batas route untuk modem

Route::get('/traccar/{status}', [DashboardController::class, 'listTraccar'])->name('traccar.listTraccarStatus')->middleware('auth');
Route::get('/dump/{status}', [DashboardController::class, 'listDump'])->name('traccar.listDumpStatus')->middleware('auth');
Route::get('traccar', [DashboardController::class, 'listTraccar'])->name('traccar.listTraccar')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::resource('drivers', DriverController::class);
    Route::resource('vehicles', VehicleController::class);   
    Route::resource('groups', GroupController::class); 
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('assign_roles', AssignRoleController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('report_admin', ReportAdminController::class);
    Route::resource('devices', DeviceController::class);
    Route::resource('traccars', TraccarController::class);
    Route::resource('geofence', GeofenceController::class);
    Route::resource('settings', SettingController::class);
    Route::resource('users', UserController::class);
    Route::resource('notification', NotificationController::class);
    Route::resource('histories', HistoryController::class);
    Route::resource('events', EventController::class);
    Route::resource('transmissions', TransmissionController::class);
    Route::resource('alarms', AlarmController::class);
    Route::post('/settings/save', [SettingController::class, 'save']);
    Route::get('/vehicle/export', [VehicleController::class, 'exportVehicle'])->name('vehicles.export');
    Route::get('/group-vehicle/{customerId}', [VehicleController::class, 'getGroupsByCustomer']); 
    Route::get('/vehiclebygroup/{groupId}', [ReportController::class, 'getVehicleByGroup']); 
    Route::get('/get-nearby-vehicles', [TraccarController::class, 'getNearestVehicles']);
    Route::get('/get-traccar-data', [TraccarController::class, 'getData']);
    Route::get('/realtime', [TraccarController::class, 'webSocket']);
    Route::get('/get-traccar-hso', [HsoMonitoringController::class, 'getDataHso']);    
    Route::get('/report-last-position', [ReportController::class, 'tampilkanListPosisiAkhir']);
    Route::get('/report-historical', [ReportController::class, 'tampilkanListHistorical']);
    Route::get('/report-kecepatan', [ReportController::class, 'tampilkanListKecepatan']);
    Route::get('/datatable-parking', [ReportController::class, 'tampilkanListParkir']);
    Route::get('/report/historical', [ReportController::class, 'historicalReport'])->name('report.historicalReport');
    Route::get('/report/kecepatan', [ReportController::class, 'laporanKecepatan'])->name('reports.kecepatan');
    Route::get('/report/jarak', [ReportController::class, 'laporanJarak'])->name('reports.jarak');
    Route::get('/report/parkir', [ReportController::class, 'laporanParkir'])->name('reports.parkir');
    Route::get('/report/download-historical', [ReportController::class, 'exportLaporanHistorical'])->name('report.downloadHistorical');
    Route::get('/report/download-speed', [ReportController::class, 'exportLaporanKecepatan'])->name('report.downloadKecepatan');
    Route::get('/report/download-distance', [ReportController::class, 'exportLaporanJarak'])->name('report.downloadJarak');
    Route::get('/report/download-parking', [ReportController::class, 'exportParkirToExcel'])->name('report.downloadParkir');
    Route::get('/report/distance', [ReportController::class, 'listDistance']);

    // report dump truck
    Route::get('/report/dump_truck', [ReportController::class, 'laporanDump']);
    Route::get('/report/preview-dump-report', [ReportController::class, 'previewDumpReport'])->name('report.previewDumpReport');
    Route::get('/report/download-dump-report', [ReportController::class, 'downloadDumpReport'])->name('report.downloadDumpReport');

    Route::get('/grafik/speed', [GrafikController::class, 'grafikSpeed']);
    Route::get('/grafik/distance', [GrafikController::class, 'grafikDistance']);

    // report untuk admin
    Route::get('/admin/report-historical', [ReportAdminController::class, 'listHistorical']);
    Route::get('/admin/report-last-position', [ReportAdminController::class, 'listLastPosition']);
    Route::get('/admin/report-distance', [ReportAdminController::class, 'listDistance']);
    Route::get('/admin/datatable-parking', [ReportAdminController::class, 'listParkingDatatable']);
    Route::get('/admin/report/historical', [ReportAdminController::class, 'historicalReport'])->name('report.historicalReportAdmin');
    Route::get('/admin/report/kecepatan', [ReportAdminController::class, 'laporanKecepatan'])->name('reports.kecepatanAdmin');
    Route::get('/admin/report/jarak', [ReportAdminController::class, 'laporanJarak'])->name('reports.jarakAdmin');
    Route::get('/admin/report/parkir', [ReportAdminController::class, 'laporanParkir'])->name('reports.parkirAdmin');
    Route::get('/admin/report/download-historical', [ReportAdminController::class, 'exportLaporanHistorical'])->name('report.downloadHistoricalAdmin');
    Route::get('/admin/report/download-speed', [ReportAdminController::class, 'exportLaporanKecepatan'])->name('report.downloadKecepatanAdmin');
    Route::get('/admin/report/download-distance', [ReportAdminController::class, 'exportLaporanJarak'])->name('report.downloadJarakAdmin');
    Route::get('/admin/report/download-parking', [ReportAdminController::class, 'exportParkirToExcel'])->name('report.downloadParkirAdmin');
    
    // Grafik
    // Route::get('/grafik/historical', [GrafikController::class, 'historicalReport'])->name('grafik.historical');
    Route::get('/grafik/kecepatan', [GrafikController::class, 'grafikKecepatan'])->name('grafik.kecepatan');
    Route::get('/grafik/jarak', [GrafikController::class, 'grafikJarak'])->name('grafik.jarak');
    // Route::get('/grafik/parkir', [GrafikController::class, 'laporanParkir'])->name('grafik.parkir');
    Route::get('/grafik/heatmap', [GrafikController::class, 'heatmapByVehicleForm']);
    Route::get('/grafik/heatmap-per-vehicle', [GrafikController::class, 'heatmapByVehicle']);
    Route::get('/grafik/heatmap-speed-status', [GrafikController::class, 'heatmapBySpeedOrStatus']);
    Route::get('/grafik/clustering-location', [GrafikController::class, 'clusteringLocation']);
    Route::get('/grafik/distribusi', [GrafikController::class, 'speedDistributionForm']);
    Route::get('/grafik/distribusi-kecepatan', [GrafikController::class, 'speedDistribution']);
    Route::get('/grafik/distance-by-day', [GrafikController::class, 'totalDistancePerDay']);
    Route::get('/grafik/distance', [GrafikController::class, 'totalDistancePerDayForm']);
    Route::get('/grafik/speed-map', [GrafikController::class, 'getSpeedMap']);
    Route::get('/grafik/speed', [GrafikController::class, 'getSpeedMapForm']);

    // testing
    Route::get('/api/speeding', [MapDataController::class, 'speeding']);
    Route::get('/api/stops', [MapDataController::class, 'stops']);
    Route::get('/api/jams', [MapDataController::class, 'jams']);
    Route::get('/map', [MapDataController::class, 'index']);
   
    Route::post('devices-gps', [DeviceController::class, 'updateData'])->name('devices.updateData');
    //Route::post('vehicles', [VehicleController::class, 'updateData'])->name('vehicles.updateData');
    Route::post('devices-login', [DeviceController::class, 'updateDataLogin'])->name('devices.updateDataLogin');
    Route::get('/pesan/unduh', [NotificationController::class, 'unduh'])->name('notification.export');
    Route::get('/driver/export', [DriverController::class, 'export'])->name('drivers.export');
    Route::get('/customer/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::get('/report/export', [ReportController::class, 'exportLastPosition'])->name('report.exportLastPosition');
    Route::get('/admin/report/export', [ReportAdminController::class, 'exportLastPosition'])->name('report.exportLastPositionAdmin');
    Route::get('/get-vehicles/{customer_id}', [ReportAdminController::class, 'getVehicles']);
    Route::post('/geofenced/radius-simpan', [GeofenceController::class, 'simpan'])->name('geofence.simpan');
    Route::get('/geofenced/radius-baru', [GeofenceController::class, 'baru'])->name('geofence.baru');
    Route::get('/list-data-information', [EventController::class, 'listDataInformation']);
    Route::get('/list-data-transmission', [TransmissionController::class, 'listDataTransmission']);
    Route::get('/get-data-map', [HistoryController::class, 'getMapData']);

    // route untuk cek sistem php daemon jalan atau tidak di server tujuan
    Route::get('/show-comserver-processes', [SystemController::class, 'showSistemProcesses']);
    Route::post('/check-sistem-processes', [SystemController::class, 'checkSistemProcesses']);
    Route::post('/run-remote-sistem', [SystemController::class, 'runRemoteSistem']);
});


// Login
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);
//Route::get('/register', [RegisterController::class, 'index']);
//Route::post('/register', [RegisterController::class, 'store']);
// batas akhir untuk route login

// download apk mototrack
Route::get('/download', [DownloadController::class, 'index'])->name('download');

// Lupa password
Route::get('/forgot-password', [LoginController::class, 'forgotPassword'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'kirimLinkUntukReset'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [LoginController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'resetSandi'])->middleware('guest')->name('password.update');
// akhir route untuk lupa password