<?php

namespace ShahBurhan\LaravelPayPal;

use GuzzleHttp\Exception\ClientException;
use ShahBurhan\LaravelPayPal\Model\Token;

class PayPalSubscription
{
    /**
     * Stores GuzzleClient Instance
     * @var object
     */
    protected $client = null;
    /**
     * The PayPal Endpoint to create a plan
     * @var string
     */
    protected $store = '/v1/billing/subscriptions';
    /**
     * The PayPal Endpoint to update a plan
     * @var string
     */
    protected $update = '/v1/billing/subscriptions/{id}';
    /**
     * The PayPal Endpoint to list all plans
     * @var string
     */
    protected $transactions = '/v1/billing/subscriptions/{id}/transactions';
    /**
     * The PayPal Endpoint to fetch plan details
     * @var string
     */
    protected $show = '/v1/billing/subscriptions/{id}';
    /**
     * The PayPal Endpoint to activate a plan
     * @var string
     */
    protected $activate = '/v1/billing/subscriptions/{id}/activate';
    /**
     * The PayPal Endpoint to deactivate a plan
     * @var string
     */
    protected $cancel = '/v1/billing/subscriptions/{id}/cancel';
    /**
     * The PayPal Endpoint to update a plan's pricing
     * @var string
     */
    protected $capture = '/v1/billing/subscriptions/{id}/capture';
    protected $suspend = '/v1/billing/subscriptions/{id}/suspend';
    /**
     * Generated Access Token
     * @var string | null
     */
    protected $token = null;

    protected $plan                = null;
    protected $quantity            = 1;
    protected $shipping_amount     = null;
    protected $subscriber          = null;
    protected $application_context = null;
    protected $payment_source      = null;
    protected $payment_mode        = "paypal";

    public function __construct()
    {
        $this->client = (new Client)();
        $this->token  = (new AccessToken)($this->client);
        $this->setPaymentMode();
    }

    /**
     * Method to create a new product
     * @return Response
     */
    public function store()
    {

        $body = [];

        try {
            $response = $this->client->request("POST", $this->store, ["headers" => $this->headers(), "json" => $body]);
        } catch (ClientException $e) {
            return $e->getMessage();
        }

        return $response->getBody();
    }

    /**
     * Method to fetch a product
     * @return Response
     */
    public function show()
    {
        $this->show = str_replace('{id}', $this->id, $this->show);
        $response   = $this->client->request("GET", $this->show, ["headers" => $this->headers()]);
        return $response->getBody();
    }

    /**
     * List all transactions for a subscription
     * @return Response
     */
    public function transactions()
    {
        $response = json_decode($this->client->request('GET', $this->list, ["headers" => $this->headers()])->getBody(), true);

        return $response;
    }

    /**
     * Method to update a product
     * @return Response
     */
    public function update()
    {

    }

    protected function headers(array $header = [])
    {
        $headers = ['Authorization' => "Bearer " . $this->token];
        if (count($header)) {
            $headers = array_merge($header, $headers);
        }
        return $headers;
    }

    protected function setPayPalAsPaymentSource()
    {
        $this->application_context = [
            "brand_name"          => "",
            "locale"              => "en-US",
            "shipping_preference" => "NO_SHIPPING",
            "payment_method"      => [
                "payer_selected"  => "PAYPAL",
                "payee_preferred" => "IMMEDIATE_PAYMENT_REQUIRED",
            ],
            "return_url"          => "",
            "cancel_url"          => "",
        ];
    }
    protected function setCardAsPaymentSource()
    {
        $this->payment_source = [
            "card" => [
                "number"          => "",
                "expiry"          => "", //YYYY-MM
                "security_code"   => "",
                "name"            => "",
                "billing_address" => [
                    "address_line_1" => "",
                    "address_line_2" => "",
                    "admin_area_1"   => "",
                    "admin_area_2"   => "",
                    "postal_code"    => "",
                    "country_code"   => "",
                ],
            ],
        ];
    }
    protected function setSubscriber()
    {
        $this->subscriber = [
            "name"             => [
                "given_name" => '',
                "surname"    => '',
            ],
            "email_address"    => "",
            "shipping_address" => [
                "name"    => [
                    "full_name" => "",
                ],
                "address" => [
                    "address_line_1" => "",
                    "address_line_2" => "",
                    "admin_area_2"   => "",
                    "admin_area_1"   => "",
                    "postal_code"    => "",
                    "country_code"   => "",

                ],
            ],
        ];
    }
    protected function body()
    {
        $body = [];

        if ($this->payment_mode == "card") {
            $this->setCardAsPaymentSource();
            $body['payment_source'] = $this->payment_source;
        } else {
            $this->setCardAsPaymentSource();
            $body['application_context'] = $this->application_context;
        }

        $body['plan_id'] = $this->plan;

        $body['quantity'] = $this->quantity;

        if ($this->shipping_amount != null) {
            $body['shipping_amount'] = $this->shipping_amount;
        }

        $this->setSubscriber();
        $body['subscriber'] = $this->subscriber;

    }
    protected function setPaymentMode()
    {
        $this->payment_mode = strtolower(config('laravel_paypal.payment_mode') ?? "paypal");
    }
}
