<?php

namespace ShahBurhan\LaravelPayPal;

use GuzzleHttp\Exception\ClientException;
use ShahBurhan\LaravelPayPal\Model\Token;
use ShahBurhan\LaravelPayPal\Response\SubscriptionResponse;

class PayPalSubscription
{
    /**
     * Stores GuzzleClient Instance
     * @var object
     */
    protected $client = null;
    /**
     * The PayPal Endpoint to create a subscription
     * @var string
     */
    protected $store = '/v1/billing/subscriptions';
    /**
     * The PayPal Endpoint to update a subscription
     * @var string
     */
    protected $update = '/v1/billing/subscriptions/{id}';
    /**
     * The PayPal Endpoint to list all subscription
     * @var string
     */
    protected $transactions = '/v1/billing/subscriptions/{id}/transactions';
    /**
     * The PayPal Endpoint to fetch subscription details
     * @var string
     */
    protected $show = '/v1/billing/subscriptions/{id}';
    /**
     * The PayPal Endpoint to activate a subscription
     * @var string
     */
    protected $activate = '/v1/billing/subscriptions/{id}/activate';
    /**
     * The PayPal Endpoint to deactivate a subscription
     * @var string
     */
    protected $cancel = '/v1/billing/subscriptions/{id}/cancel';

    protected $capture = '/v1/billing/subscriptions/{id}/capture';
    
    protected $suspend = '/v1/billing/subscriptions/{id}/suspend';
    /**
     * Generated Access Token
     * @var string | null
     */
    protected $token = null;

    protected $id;
    protected $plan;
    protected $merchant;
    protected $quantity = 1;
    protected $shipping_amount;
    protected $subscriber;
    protected $application_context;
    protected $payment_source;
    protected $payment_mode = "paypal";
    protected $return_url;
    protected $cancel_url;
    protected $billing_address_1;
    protected $billing_address_2;
    protected $billing_area_1;
    protected $billing_area_2;
    protected $billing_postal_code;
    protected $billing_country_code;
    protected $shipping_address_1;
    protected $shipping_address_2;
    protected $shipping_area_1;
    protected $shipping_area_2;
    protected $shipping_postal_code;
    protected $shipping_country_code;
    protected $subscriber_given_name;
    protected $subscriber_surname;
    protected $card_name;
    protected $card_number;
    protected $card_expiry;
    protected $card_code;
    protected $start_time;
    protected $email_address;

    public function __construct()
    {
        $this->client = (new Client)();
        $this->token  = (new AccessToken)($this->client);
        $this->setPaymentMode();
        $this->setURLs();
    }

    /**
     * Method to create a new product
     * @return Response
     */
    public function store()
    {
        $body = $this->body();

        try {
            $response = $this->client->request("POST", $this->store, ["headers" => $this->headers(), "json" => $body]);
        } catch (ClientException $e) {
            return $e->getResponse()->getBody();
        }
        return new SubscriptionResponse(json_decode($response->getBody(), true));
    }

    /**
     * Method to fetch a subscription
     * @return Response
     */
    public function show()
    {
        $this->show = str_replace('{id}', $this->id, $this->show);
        $response   = $this->client->request("GET", $this->show, ["headers" => $this->headers()]);
        return json_decode($response->getBody(), true);
    }
    /**
     * Method to activate a subscription
     * @return Response
     */
    public function activate()
    {
        $this->activate = str_replace('{id}', $this->id, $this->activate);
        $response   = $this->client->request("GET", $this->activate, ["headers" => $this->headers()]);
        return $response->getBody();
    }
    /**
     * Method to cancel a subscription
     * @return Response
     */
    public function cancel()
    {
        $this->cancel = str_replace('{id}', $this->id, $this->cancel);
        $response   = $this->client->request("GET", $this->cancel, ["headers" => $this->headers()]);
        return $response->getBody();
    }

    /**
     * List all transactions for a subscription
     * @return Response
     */
    public function transactions()
    {
        $this->transactions = str_replace('{id}', $this->id, $this->transactions);

        try {
            $response   = $this->client->request("GET", $this->transactions, ["headers" => $this->headers()]);
        } catch (ClientException $e) {
            return $e->getResponse()->getBody();
        }
        return $response->getBody();
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
            "brand_name"          => $this->merchant,
            "locale"              => "en-US",
            "shipping_preference" => "NO_SHIPPING",
            "payment_method"      => [
                "payer_selected"  => "PAYPAL",
                "payee_preferred" => "IMMEDIATE_PAYMENT_REQUIRED",
            ],
            "return_url"          => $this->return_url,
            "cancel_url"          => $this->cancel_url,
        ];
    }
    protected function setCardAsPaymentSource()
    {
        $this->payment_source = [
            "card" => [
                "number"          => $this->card_number,
                "expiry"          => $this->card_expiry, //YYYY-MM
                "security_code"   => $this->card_code,
                "name"            => $this->card_name,
                "billing_address" => [
                    "address_line_1" => $this->billing_address_1,
                    "address_line_2" => $this->billing_address_2,
                    "admin_area_1"   => $this->billing_area_1,
                    "admin_area_2"   => $this->billing_area_2,
                    "postal_code"    => $this->billing_postal_code,
                    "country_code"   => $this->billing_country_code,
                ],
            ],
        ];
    }
    protected function setSubscriber()
    {
        $this->subscriber = [
            "name"          => [
                "given_name" => $this->subscriber_given_name,
                "surname"    => $this->subscriber_surname,
            ],
            "email_address" => $this->email_address,
        ];
    }
    protected function appendShipping()
    {
        $this->subscriber["shipping_address"] = [
            "name"    => [
                "full_name" => $this->subscriber_given_name . " " . $this->subscriber_surname,
            ],
            "address" => [
                "address_line_1" => $this->shipping_address_1,
                "address_line_2" => $this->shipping_address_2,
                "admin_area_2"   => $this->shipping_area_1,
                "admin_area_1"   => $this->shipping_area_2,
                "postal_code"    => $this->shipping_postal_code,
                "country_code"   => $this->shipping_country_code,
            ],
        ];
    }
    protected function body()
    {
        $body = [];

        $this->setSubscriber();

        if ($this->payment_mode == "card") {
            $this->setCardAsPaymentSource();
            $this->subscriber['payment_source'] = $this->payment_source;
        } else {
            $this->setPayPalAsPaymentSource();
            $body['application_context'] = $this->application_context;
        }

        $body['plan_id'] = $this->plan;

        $body['quantity'] = $this->quantity;

        if ($this->shipping_amount) {
            $body['shipping_amount'] = $this->shipping_amount;
            $this->appendShipping();
        }
        $body['subscriber'] = $this->subscriber;

        if ($this->start_time != null) {
            $body['start_time'] = $this->start_time;
        }

        return $body;
    }
    protected function setPaymentMode()
    {
        $this->payment_mode = strtolower(config('laravel_paypal.payment_mode') ?? "paypal");
    }
    protected function setURLs()
    {
        $this->return_url = config('laravel_paypal.return_url');
        $this->cancel_url = config('laravel_paypal.cancel_url');
    }
    public function setReturnUrl($url)
    {
        $this->return_url = $url;
    }
    public function setCancelUrl($url)
    {
        $this->cancel_url = $url;
    }
    public function setCardNumber($value)
    {
        $this->card_number = $value;
    }
    public function setCardExpiry($value)
    {
        $this->card_expiry = $value;
    }
    public function setCardCode($value)
    {
        $this->card_code = $value;
    }
    public function setCardName($value)
    {
        $this->card_name = $value;
    }
    public function setBillingAddress1($value)
    {
        $this->billing_address_1 = $value;
    }
    public function setBillingAddress2($value)
    {
        $this->billing_address_2 = $value;
    }
    public function setBillingCity($value)
    {
        $this->billing_area_1 = $value;
    }
    public function setBillingState($value)
    {
        $this->billing_area_2 = $value;
    }
    public function setBillingPostalCode($value)
    {
        $this->billing_postal_code = $value;
    }
    public function setBillingCountryCode($value)
    {
        $this->billing_country_code = $value;
    }
    public function setShippingAddress1($value)
    {
        $this->shipping_address_1 = $value;
    }
    public function setShippingAddress2($value)
    {
        $this->shipping_address_2 = $value;
    }
    public function setShippingCity($value)
    {
        $this->shipping_area_1 = $value;
    }
    public function setShippingState($value)
    {
        $this->shipping_area_2 = $value;
    }
    public function setShippingPostalCode($value)
    {
        $this->shipping_postal_code = $value;
    }
    public function setShippingCountryCode($value)
    {
        $this->shipping_country_code = $value;
    }
    public function setSubscriberFirstName($value)
    {
        $this->subscriber_given_name = $value;
    }
    public function setSubscriberLastName($value)
    {
        $this->subscriber_surname = $value;
    }
    public function setEmail($value)
    {
        $this->email_address = $value;
    }
    public function setMerchant($value)
    {
        $this->merchant = $value;
    }
    public function setPlan($value)
    {
        $this->plan = $value;
    }
    public function setStartTime($value = null)
    {
        $this->start_time = $value;
    }
    public function setId($value)
    {
        $this->id = $value;
    }
}
