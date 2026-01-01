<?php

use Illuminate\Support\Facades\Route;
use App\Models\Customer;

Route::get('/', function () {
    return view('welcome');
});