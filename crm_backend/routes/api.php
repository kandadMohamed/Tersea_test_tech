<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Routes For User Auth
Route::post('create-account',[\App\Http\Controllers\UserController::class, 'createAccount']);
Route::post('get-account',[\App\Http\Controllers\UserController::class, 'login']);
// Route::post('get-account',[\App\Http\Controllers\UserController::class, 'getAccount']);
Route::post('get-invite',[\App\Http\Controllers\UserController::class, 'getEmployeeInvite']);
Route::post('get-employees-accounts',[\App\Http\Controllers\UserController::class, 'getEmployeesAccounts']);
Route::post('refuse-invite',[\App\Http\Controllers\UserController::class, 'refuseInvite']);

Route::post('valide-account',[\App\Http\Controllers\UserController::class, 'valideAccount']);

// Route::post('refuse-invite',[\App\Http\Controllers\UserController::class, 'refuseInvite']);

// Routes For Company
Route::post('add-company',[\App\Http\Controllers\CompanyController::class, 'addCompany']);
Route::post('edit-company',[\App\Http\Controllers\CompanyController::class, 'editCompany']);
Route::post('delete-company',[\App\Http\Controllers\CompanyController::class, 'deleteCompany']);
Route::post('get-companies',[\App\Http\Controllers\CompanyController::class, 'getAllCompanies']);


Route::post('get-history',[\App\Http\Controllers\HistoryController::class, 'getAllHisory']);




