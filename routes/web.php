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
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {

    $router->post('client-info',  ['uses' => 'UssdController@clientInfo']);
    $router->post('payment',  ['uses' => 'UssdController@payment']);


    $router->group(['middleware' => 'auth'], function () use ($router) {

        //Dashboard
        $router->get('data',  ['uses' => 'DashboardController@index']);

    });


    //Authentification
    $router->group(['namespace' => '\App\Http\Controllers\Api'], function () use ($router) {
        $router->post('login','AuthApiController@login');
        $router->post('check_key','AuthApiController@check_key');
    });
    $router->group(['namespace' => '\App\Http\Controllers\Api', 'middleware' => 'auth'], function () use ($router) {
        $router->post('logout','AuthApiController@logout');
    });

    //Organisation
    $router->get('show-organization/{id}', ['uses' => 'OrganizationController@show']);
    $router->post('update-user-organization', ['uses' => 'OrganizationController@updateUserOrg']);
    $router->get('all-user-organization', ['uses' => 'OrganizationController@allOrganizationsUser']);

});

