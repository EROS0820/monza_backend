<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| apis Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/login', 'AuthController@login');
    $router->post('/forgot', 'AuthController@forgot');
    $router->post('/register', 'AuthController@register');
    $router->post('/verify', 'VerifyController@verify');
    $router->post('/reset_password', 'VerifyController@reset_password');
});

$router->group(['prefix' => 'user'], function () use ($router) {
    $router->get('/validate_token', 'ProfileController@validateToken');
});

$router->group(['prefix' => 'assortment', 'middleware' => ['auth']], function () use ($router) {
    $router->get('/info', 'AssortmentController@getInfo');
    $router->post('/filter_list', 'AssortmentController@getFilterList');
    $router->get('/', 'AssortmentController@get');
    $router->post('/', 'AssortmentController@create');
    $router->put('/',  'AssortmentController@update');
    $router->delete('/', 'AssortmentController@delete');
});

$router->group(['prefix' => 'assortment_group', 'middleware' => ['auth']], function () use ($router) {
    $router->get('/info', 'AssortmentGroupController@getInfo');
    $router->post('/filter_list', 'AssortmentGroupController@getFilterList');
    $router->get('/', 'AssortmentGroupController@get');
    $router->post('/', 'AssortmentGroupController@create');
    $router->put('/',  'AssortmentGroupController@update');
    $router->delete('/', 'AssortmentGroupController@delete');
});

$router->group(['prefix' => 'warehouse', 'middleware' => ['auth']], function () use ($router) {
    $router->post('/filter_list', 'WarehouseController@getFilterList');
    $router->get('/', 'WarehouseController@get');
    $router->post('/', 'WarehouseController@create');
    $router->put('/',  'WarehouseController@update');
    $router->delete('/', 'WarehouseController@delete');
});

$router->group(['prefix' => 'warehousegroup', 'middleware' => ['auth']], function () use ($router) {
    $router->get('/info', 'WarehouseGroupController@getInfo');
    $router->post('/filter_list', 'WarehouseGroupController@getFilterList');
    $router->get('/', 'WarehouseGroupController@get');
    $router->post('/', 'WarehouseGroupController@create');
    $router->put('/',  'WarehouseGroupController@update');
    $router->delete('/', 'WarehouseGroupController@delete');
});

$router->group(['prefix' => 'measurement_unit', 'middleware' => ['auth']], function () use ($router) {
    $router->post('/filter_list', 'MeasurementUnitController@getFilterList');
    $router->get('/', 'MeasurementUnitController@get');
    $router->post('/', 'MeasurementUnitController@create');
    $router->put('/',  'MeasurementUnitController@update');
    $router->delete('/', 'MeasurementUnitController@delete');
});

$router->group(['prefix' => 'contractor', 'middleware' => ['auth']], function () use ($router) {
    $router->post('/filter_list', 'ContractorController@getFilterList');
    $router->get('/', 'ContractorController@get');
    $router->post('/', 'ContractorController@create');
    $router->put('/',  'ContractorController@update');
    $router->delete('/', 'ContractorController@delete');
});

$router->group(['prefix' => 'warehouse_operation', 'middleware' => ['auth']], function () use ($router) {
    $router->get('/info', 'WarehouseOperationController@getInfo');
    $router->post('/filter_list', 'WarehouseOperationController@getFilterList');
    $router->get('/', 'WarehouseOperationController@get');
    $router->post('/', 'WarehouseOperationController@create');
    $router->put('/',  'WarehouseOperationController@update');
    $router->delete('/', 'WarehouseOperationController@delete');
});
