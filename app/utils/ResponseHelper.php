<?php
class ResponseHelper
{

    public static function jsonResponse($response, $data)
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}