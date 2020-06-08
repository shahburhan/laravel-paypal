<?php

namespace ShahBurhan\LaravelPayPal;

class Client
{
    public function __invoke()
    {
        $url = config('laravel_paypal.sandbox') ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';

        $client = new \GuzzleHttp\Client(['base_uri' => $url]);

        return $client;
    }
}
