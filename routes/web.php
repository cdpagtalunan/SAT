<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SATController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\DropdownController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('verifySession')->group(function(){
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/sat', function () {
        return view('SAT');
    })->name('sat');

    Route::get('/dropdown_maintenance', function () {
        return view('dropdown_maintenance');
    })->name('dropdown_maintenance');

    Route::controller(DropdownController::class)->group(function(){
        Route::get('/get_dropdown_list', 'getDropdownList')->name('get_dropdown_list');
        Route::get('/dt_get_dropdown_items', 'dtGetDropdownItems')->name('dt_get_dropdown_items');
        Route::post('/save_dropdown_item', 'saveDropdownItem')->name('save_dropdown_item');
        Route::post('/delete_dropdown_item', 'deleteDropdownItem')->name('delete_dropdown_item');
    });

    Route::controller(SATController::class)->group(function(){
        Route::get('/get_dropdown_data', 'getDropdownData')->name('get_dropdown_data');
        Route::post('/save_sat', 'saveSAT')->name('save_sat');
        Route::get('/dt_get_sat', 'dtGetSat')->name('dt_get_sat');
        Route::get('/get_sat_by_id', 'getSatById')->name('get_sat_by_id');
        Route::post('/proceed_obs', 'proceedObs')->name('proceed_obs');
        Route::get('/dt_get_process_for_observation', 'dtGetProcessForObservation')->name('dt_get_process_for_observation');
        Route::post('/save_process_obs', 'saveProcessObs')->name('save_process_obs');
        Route::post('/done_obs', 'doneObs')->name('done_obs');
        Route::get('/dt_get_process_for_line_balance', 'dtGetProcessForLineBalance')->name('dt_get_process_for_line_balance');
    });
});
