<?php

namespace ShahBurhan\LaravelPayPal;

use GuzzleHttp\Exception\ClientException;
use ShahBurhan\LaravelPayPal\Model\Token;

class AccessToken
{
	public function __invoke($client){
		
		$token = Token::where('expiry', '>', time())->first();

		if (!$token) {

            $auth = [
                config('laravel_paypal.client_id'),
                config('laravel_paypal.secret'),
            ];

            $form = [
                'grant_type' => 'client_credentials',
            ];
            try {
                $response = $client->request('POST', '/v1/oauth2/token', ['auth' => $auth, 'form_params' => $form]);
            } catch (ClientException $e) {
                dd($e->getCode(), $e->getResponse()->getBody());
            }
            $body = json_decode($response->getBody(), true);

            $token = Token::create(
                [
                    "expiry" => time() + $body["expires_in"],
                    "app_id" => $body['app_id'],
                    'token'  => $body['access_token'],
                ]
            );
        }

        return $token->token;
	}
}
