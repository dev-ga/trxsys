<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ip', function (Request $request) {
    $ip = $request->ip();
    dd($ip);

    // return view('welcome');
});