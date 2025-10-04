<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SATController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ApproverController;
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

    Route::get('/sat_approval', function () {
        return view('SAT_approval');
    })->name('sat_approval');

    Route::get('/dropdown_maintenance', function () {
        return view('dropdown_maintenance');
    })->name('dropdown_maintenance');

     Route::get('/approver_list', function () {
        return view('approver_list');
    })->name('approver_list');

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
        Route::post('/save_line_balance', 'saveLineBalance')->name('save_line_balance');
        Route::post('/proceed_for_approval', 'proceedForApproval')->name('proceed_for_approval');
    });

    Route::controller(CommonController::class)->group(function(){
        Route::get('/get_operator_list', 'getOperatorList')->name('get_operator_list');
    });

    Route::controller(ApproverController::class)->group(function(){
        Route::get('/dt_get_approver_list', 'dtGetApproverList')->name('dt_get_approver_list');
        Route::get('/get_user_approver', 'getUserApprover')->name('get_user_approver');
        Route::post('/save_approver', 'saveApprover')->name('save_approver');
        Route::post('/delete_approver', 'deleteApprover')->name('delete_approver');
        
        Route::get('/dt_sat_approval', 'dtSatApproval')->name('dt_sat_approval');
        Route::get('/get_sat_details', 'getSatDetails')->name('get_sat_details');
        Route::post('/approve_sat', 'approveSat')->name('approve_sat');
    });

    Route::controller(ExportController::class)->group(function(){
        Route::get('/export_sat', 'exportSat')->name('export_sat');
    });
});
