<?php

use Illuminate\Support\Facades\Route;
use MLL\GraphiQL\Facades\GraphiQL;


Route::get('/', function () {
    return view('welcome');
});