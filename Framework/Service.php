<?php

/**
 * Service - base service
 */

namespace Framework;

abstract class Service
{
    public function __construct()
    {
        //authentication is mandatory for all requests
        $apiUsername = preg_replace("/[^a-zA-Z0-9]+/", "", Utilities::escapeString($_SERVER['PHP_AUTH_USER']));
        $apiPassword = preg_replace("/[^a-zA-Z0-9]+/", "", Utilities::escapeString($_SERVER['PHP_AUTH_PW']));

        if ($apiUsername == '' || $apiPassword == '') {
            $httpStatusCode    = '401';
            $httpStatusMessage = 'Unauthorized';
        } else {
            // validate  the apiUsername and apiPassword
            $httpStatusCode    = '200';
            $httpStatusMessage = 'OK';
        }

        //return back the response
        if ($httpStatusCode == '') {
            $response = new Response();
            $response->setHttpStatusCode($httpStatusCode, $httpStatusMessage);
            $response->setErrorMessage('INVALID_LOGIN', 'Invalid API Credentials');
            $response->dispatchJson();
        }
    }

    public function process(Response $response)
    {
        $response->dispatchJson();
    }
}
