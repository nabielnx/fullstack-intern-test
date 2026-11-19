<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::post('/todos', [TodoController::class, 'store']);    