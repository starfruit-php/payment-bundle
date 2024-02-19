<?php

namespace Starfruit\PaymentBundle\Service;

class BaseService
{
    protected static function getCurrentLocale()
    {
        return \Pimcore::getContainer()->get(\Pimcore\Localization\LocaleServiceInterface::class)->getLocale();
    }

    protected static function getIP()
    {
        try {
            $url = "ipv4.icanhazip.com";

            $client = new \GuzzleHttp\Client();
            $call = $client->request("GET", $url, []);

            $ip_address = $call->getBody()->getContents();
            $ip_address = str_replace("\n", "", $ip_address);

            return $ip_address;
        } catch (\Throwable $e) {
        }
        
        return $_SERVER['REMOTE_ADDR'];
    }
}
