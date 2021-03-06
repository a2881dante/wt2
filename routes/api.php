<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('stolen-cars', 'Api\StolenCarController')
    ->except(['show']);

Route::get('stolen-cars/export/', 'Api\StolenCarController@export');

Route::get('makes/{partname}', 'Api\MakeController@getWithModels');