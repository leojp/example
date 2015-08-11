<?php

use Source\Src\Location;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
* Loads the autoload file.
*/
require_once __DIR__.'/../vendor/autoload.php';

/**
* Loads the cofig file with Instagram and Gmap data.
*/
require_once __DIR__.'/../config/config.php';

/**
* Running Silex
*/
$app = new Application();

$app->get('/media/', function () use ($app) {
    
    $response = array(
        'reason_phrase' => 'Please put an Instagram Media ID.',
        'satus_code' => 404,
    );

    $headers = array('Content-Type' => 'application/json; charset=utf-8');

    return $app->json($response, 404, $headers);
});


$app->get('/media/{mediaID}', function ($mediaID, Request $request) use ($app) {

    $filters =$request->get('filters');
    
    $location = new Location($mediaID, $filters);

    $response = $location->getResponse();

    $headers = array('Content-Type' => 'application/json; charset=utf-8');

    $code = (isset($response['satus_code']))  ?  $response['satus_code'] : 200;

    return $app->json($response, $code, $headers);

});

$app->run();
