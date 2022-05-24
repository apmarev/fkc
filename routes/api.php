<?php

use App\Http\Controllers\AmoCrmController;
use App\Http\Controllers\AccessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::post('/access/amo/new', [AmoCrmController::class, 'amoNewAccess']);

Route::get('/reports/test', [ReportController::class, 'salesAnalysis']);
Route::get('/reports/dealsByManager', [ReportController::class, 'dealsByManager']);
Route::get('/reports/salesByManager', [ReportController::class, 'salesByManager']);
Route::get('/reports/transactionSources', [ReportController::class, 'transactionSources']);
Route::get('/reports/completedTasks', [ReportController::class, 'completedTasks']);
Route::get('/reports/createdNotesForManagers', [ReportController::class, 'createdNotesForManagers']);

Route::get('/test', [ReportController::class, 'test']);


