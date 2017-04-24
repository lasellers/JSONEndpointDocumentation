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

$app->group(['prefix' => 'api'], function () use ($app) {

    $app->get('/', 'APIController@index');
    $app->get('posts', 'APIController@posts');
    $app->get('post', 'APIController@post');

});

$app->group(['prefix' => 'apis', 'middleware' => 'auth:api'], function () use ($app) {

    $app->get('/', 'APIController@index');
    $app->get('posts', 'APIController@posts');
    $app->get('post', 'APIController@post');

});


$app->get('/', ['as' => 'profile', function ($id) {
    //
}]);

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('/', 'APIController@index');


/*
$app->get('/user', function (Request $request) {
    return $request->user();
});
*/
/*
 *
$app->get('/', function () use ($app) {
    return $app->version();
});



$app->get('/api-test', function ()    {
    echo "apitest";
});
*/

/*
$app->middleware('auth:api')->get('/user', function (Request $request) {
return $request->user();
});

$app->group(['prefix' => 'api', 'middleware' => 'auth:api'], function () {
//  $app->post('/short', 'UrlMapperController@store');
$app->get('/user', function (Request $request) {
return $request->user();
});
});
*/

$app->get('/test', function () {

    return response()->json([
        'value' => 'Hello World',
        'data' => [1, 2, 3]
    ]);
});