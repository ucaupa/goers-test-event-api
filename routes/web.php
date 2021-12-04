<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->group(['prefix' => 'v1'], function () use ($router) {
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('login', 'AuthController@login');
        $router->post('register', 'AuthController@register');
        $router->post('logout', 'AuthController@logout');
        $router->post('refresh', 'AuthController@refresh');
        $router->post('me', 'AuthController@me');
        $router->patch('change-password', 'AuthController@changePassword');
    });

    $router->group(['prefix' => 'organization', 'middleware' => 'auth:api'], function () use ($router) {
        $router->get('/', 'OrganizationController@getById');
        $router->post('/', 'OrganizationController@store');
    });

    $router->group(['prefix' => 'event', 'middleware' => ['auth:api', 'has_organization', 'roles:admin-organization']], function () use ($router) {
        $router->get('/', ['uses' => 'EventController@get']);
        $router->get('/{id}', ['uses' => 'EventController@getById']);
        $router->post('/', ['uses' => 'EventController@store']);
        $router->patch('/{id}/publish', ['uses' => 'EventController@publish']);
        $router->patch('/{id}', ['uses' => 'EventController@update']);
        $router->delete('/{id}', ['uses' => 'EventController@destroy']);
    });

    $router->group(['prefix' => 'event/{eventId}/ticket', 'middleware' => ['auth:api', 'has_organization', 'roles:admin-organization']], function () use ($router) {
        $router->get('/', ['uses' => 'EventTicketController@get']);
        $router->get('/{id}', ['uses' => 'EventTicketController@getById']);
        $router->post('/', ['uses' => 'EventTicketController@store']);
        $router->patch('/{id}', ['uses' => 'EventTicketController@update']);
        $router->delete('/{id}', ['uses' => 'EventTicketController@destroy']);
    });

    $router->get('/assets/image/event/{file:[a-zA-Z0-9-_]+}[{extension:\.[a-z]+}]', ['uses' => 'EventController@file']);
});

$router->group(['prefix' => 'v2', 'namespace' => 'V2'], function () use ($router) {
    $router->group(['prefix' => 'event'], function () use ($router) {
        $router->get('/', ['uses' => 'PublicEventController@get']);
        $router->get('/{slugId}', ['uses' => 'PublicEventController@getById']);
    });

    $router->group(['prefix' => 'transaction'], function () use ($router) {
        $router->get('/{id}', ['uses' => 'TransactionController@get']);
        $router->post('/checkout', ['uses' => 'TransactionController@checkout']);
        $router->post('/pay', ['uses' => 'TransactionController@pay']);
        $router->post('/callback', ['uses' => 'TransactionController@callback']);
    });
});
