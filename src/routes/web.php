<?php

use Dealense\NearToRoute\Http\Controllers\RouteController;
use Illuminate\Support\Facades\Route;

    // routes, locations, radius, type - or read doc on github
    Route::get('/neartoroute', [RouteController::class, 'neartoroute'])->name('NearToRoute');