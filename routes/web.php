<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Web\WebController;
use App\Http\Controllers\ReportController;

Route::get('/', [ReportController::class, 'getAllReports']);
Route::get('/reports/{type}', [WebController::class, 'getFunction']);
