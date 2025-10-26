<?php

namespace Tobuli\Helpers\Payments\Gateways\Paydunya;

use Illuminate\Support\Facades\Http;

class Utilities
{
    // prevent instantiation of this class
    private function __construct() {}

    protected static function getHeaders(): array
    {
        return [
            'PAYDUNYA-PUBLIC-KEY'   => Setup::getPublicKey(),
            'PAYDUNYA-PRIVATE-KEY'  => Setup::getPrivateKey(),
            'PAYDUNYA-MASTER-KEY'   => Setup::getMasterKey(),
            'PAYDUNYA-TOKEN'        => Setup::getToken(),
            'PAYDUNYA-MODE'         => Setup::getMode(),
            'User-Agent'            => Paydunya::VERSION_NAME
        ];
    }

    public static function httpJsonRequest($url, $data = array())
    {
        return Http::timeout(10)
            ->withHeaders(self::getHeaders())
            ->acceptJson()
            ->post($url, $data)
            ->json();
    }

    public static function httpPostRequest($url, $data = array())
    {
        return Http::timeout(10)
            ->withHeaders(self::getHeaders())
            ->asForm()
            ->post($url, $data)
            ->json();
    }

    public static function httpGetRequest($url)
    {
        return Http::timeout(10)
            ->withHeaders(self::getHeaders())
            ->get($url)
            ->json();
    }
}
