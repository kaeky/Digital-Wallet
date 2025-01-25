<?php

use App\Http\Controllers\SoapController;
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

Route::get('/soap', [SoapController::class, 'wsdl'])->name('soap.wsdl');

// Protected POST for SOAP calls (the Auth0Middleware decides if "createClient" is public)
Route::post('/soap', [SoapController::class, 'handle'])
    ->middleware('auth0')
    ->name('soap.handle');
