<?php

namespace App\Service;


class ApiUrl
{

    private static $url = 'http://189.206.185.236/api/';
    //private static $url = 'http://192.168.15.17/api/';

    static function urlApi()
    {
        return self::$url;
    }
}
