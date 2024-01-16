<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\PackingController;


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



$router->get('/bookings','PackingController@getBookings');
$router->get('/get-price','PackingController@getPriceSummary');
$router->get('/availability','PackingController@getAvailability');
$router->post('/create-booking', 'PackingController@createBooking');
$router->put('/cancel-booking/{bookingId}', 'PackingController@cancelBooking');
$router->put('/amend-booking/{bookingId}', 'PackingController@amendBooking');
