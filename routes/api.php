<?php

use App\Http\Controllers\AmoCrmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::post('/access/amo/new', [AmoCrmController::class, 'amoNewAccess']);

Route::get('/reports/salesAnalysis', [ReportController::class, 'salesAnalysis']);
Route::get('/reports/dealsByManager', [ReportController::class, 'dealsByManager']);
Route::get('/reports/salesByManager', [ReportController::class, 'salesByManager']);