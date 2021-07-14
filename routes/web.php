<?php
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary, Content-Length, X-BCA-Key, X-BCA-Timestamp, X-BCA-Signature');
header('Access-Control-Allow-Origin: http://localhost:3000');

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


// API route group
$router->group(['prefix' => 'api'], function () use ($router) {
    // Matches "/api/register
   $router->post('register', 'AuthController@register');

     // Matches "/api/login
    $router->post('login', 'AuthController@login');
});


// API route group
$router->group(['prefix' => 'api'], function () use ($router) {
    // Matches "/api/register
   $router->post('register', 'AuthController@register');

     // Matches "/api/login
    $router->post('login', 'AuthController@login');
});

$router->post('/api/oauth/token', 'AuthController@token');
$router->post('/va/bills', 'BillsController@index');
$router->post('/va/payments', 'PaymentsController@index');

$router->get('/signature-bill', 'AuthController@signatureBill');
$router->get('/signature-payment', 'AuthController@signaturePayment');

$router->get('/signature-bca', 'AuthController@signBCA');