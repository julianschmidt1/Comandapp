<?php

use Firebase\JWT\JWT;

class JWTAuthenticator
{
    private static $password = 'ComandappJS';
    private static $encryptionType = ['HS256'];

    public static function GenerateToken($data)
    {
        $now = time();
        $payload = array(
            'iat' => $now,
            'exp' => $now + (60000),
            'aud' => self::Aud(),
            'data' => $data,
            'app' => "Comandapp"
        );
        return JWT::encode($payload, self::$password);
    }

    public static function CheckToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            $decodedData = JWT::decode(
                $token,
                self::$password,
                self::$encryptionType
            );
        } catch (Exception $e) {
            throw $e;
        }
        if ($decodedData->aud !== self::Aud()) {
            throw new Exception("No es el usuario valido");
        }
    }


    public static function GetPayload($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$password,
            self::$encryptionType
        );
    }

    public static function GetData($token)
    {
        return JWT::decode(
            $token,
            self::$password,
            self::$encryptionType
        )->data;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }
}