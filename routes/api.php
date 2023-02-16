<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\NasabahController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/', function () {
    $res['success'] = true;
    $res['result'] = config('app.name');
    return response($res);
});

//Route::get('test', [TestController::class, 'login']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/DataCollector', [UserController::class, 'data_collector']);
Route::get('/DataCollector/?search={name}', [UserController::class, 'data_search']);
Route::get('/DataNasabah', [UserController::class, 'data_nasabah']);
Route::post('/UpdateStatus', [NasabahController::class, 'update_status']);
Route::post('/FileUpload', [FileUploadController::class, 'FileUpload']);
