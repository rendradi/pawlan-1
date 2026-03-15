<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// import controller BookController
use App\Http\Controllers\Api\BookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// books
Route::apiResource('/books', BookController::class);

