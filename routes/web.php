<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    $res['success'] = true;
    $res['result'] = "Hello there welcome to web api using lumen!";
    return response($res);
});



$router->post('/api/login', 'UserController@login');
$router->get('/api/DataCollector', ['middleware' => 'cors', 'uses' =>  'UserController@data_collector']);
$router->get('/api/DataCollector/?search={name}', ['middleware' => 'cors', 'uses' =>  'UserController@data_search']);
$router->get('/api/DataNasabah', ['middleware' => 'cors', 'uses' =>  'UserController@data_nasabah']);
$router->post('/api/UpdateStatus', ['middleware' => 'cors', 'uses' =>  'NasabahController@update_status']);
?>