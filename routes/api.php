<?php

use App\Http\Controllers\Api\V1\ApiTaskController;
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

Route::group(['prefix' => '/v1/tasks'], function () {
    Route::get('/', [ApiTaskController::class, 'index'])->name('api.v1.tasks.index');
    Route::post('/', [ApiTaskController::class, 'store'])->name('api.v1.tasks.store');

    Route::group(['middleware' => ['api.check.auth']],  function () {
        Route::put('/{taskId}', [ApiTaskController::class, 'update'])->name('api.v1.tasks.update'); //TODO rename
        Route::delete('/{taskId}', [ApiTaskController::class, 'destroy'])->name('api.v1.tasks.destroy');
        Route::patch('/{taskId}', [ApiTaskController::class, 'updateStatusToDone'])->name('api.v1.tasks.updateStatustToDone');
    });
});

