<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [config('app.name') => '1.0.0'];
});
