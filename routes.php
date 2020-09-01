<?php

use Illuminate\Support\Facades\Route;
use Septech\Snowflake\Http\Controllers\AllocatedWorkerController;

Route::post('workers', [AllocatedWorkerController::class, 'allocate']);
Route::delete('workers', [AllocatedWorkerController::class, 'release']);