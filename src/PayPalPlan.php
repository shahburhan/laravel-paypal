<?php

namespace ShahBurhan\LaravelPayPal;

use GuzzleHttp\Exception\ClientException;
use ShahBurhan\LaravelPayPal\Model\Token;

class PayPalPlan
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
    protected $store = '/v1/billing/plans';
    /**
     * The PayPal Endpoint to update a plan
     * @var string
     */
    protected $update = '/v1/billing/plans/{id}';
    /**
     * The PayPal Endpoint to list all plans
     * @var string
     */
    protected $list = '/v1/billing/plans';
    /**
     * The PayPal Endpoint to fetch plan details
     * @var string
     */
    protected $show = '/v1/billing/plans/{id}';
    /**
     * The PayPal Endpoint to activate a plan
     * @var string
     */
    protected $activate = '/v1/billing/plans/{id}/activate';
    /**
     * The PayPal Endpoint to deactivate a plan
     * @var string
     */
    protected $deactivate = '/v1/billing/plans/{id}/deactivate';
    /**
     * The PayPal Endpoint to update a plan's pricing
     * @var string
     */
    protected $updatePricing = '/v1/billing/plans/{id}/update-pricing-schemes';
    /**
     * Generated Access Token
     * @var string | null
     */
    protected $token = null;
    /**
     * Name associated with a plan
     * @var string
     */
    protected $name = '';
    /**
     * Product associated with a plan
     * @var string
     */
    protected $product_id = '';
    /**
     * Description associated with a plan
     * @var string
     */
    protected $description = '';
    /**
     * Status of a plan
     * ACTIVE | CREATED | INACTIVE
     * @var enum
     */
    protected $status = 'ACTIVE';
    /**
     * Plan Interval
     * @var string
     */
    protected $interval = "MONTH";
    /**
     * Plan Pricing
     * @var string
     */
    protected $price = 0;
    /**
     * Billing Cycle / Occurence of a plan
     * ACTIVE | CREATED | INACTIVE
     * @var array
     */
    protected $billing_cycles = [];
    /**
     * Payment Preference for a plan
     * @var object
     */
    protected $payment_preferences = null;

    /**
     * Taxes associated with a plan
     * @var object
     */
    protected $taxes = [];
    /**
     * Whether this plan supports quantity
     * @var boolean
     */
    protected $quantity_supported = false;

    public function __construct()
    {
        $this->client = (new Client)();
        $this->token  = (new AccessToken)($this->client);
    }

    /**
     * Method to create a new product
     * @return Response
     */
    public function store()
    {
        $this->setBillingCycles();
        $this->setPaymentPreference();
        $this->setTaxes();

        $body = [
            "product_id"          => "XRP-PROD-001",
            "name"                => $this->name,
            "description"         => $this->description,
            "status"              => $this->status,
            "billing_cycles"      => [$this->billing_cycles],
            "payment_preferences" => $this->payment_preferences,
            "taxes"               => $this->taxes,
        ];
        try {
            $response = $this->client->request("POST", $this->store, ["headers" => $this->headers(), "json" => $body]);
        } catch (ClientException $e) {
            return $e->getResponse()->getBody();
        }

        return json_decode($response->getBody(), true);
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
     * Method to list all products
     * @return Response
     */
    public function all()
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

    /**
     * Method to delete a product
     * @return Response
     */
    public function delete()
    {

    }
    public function setId($value)
    {
        $this->id = $value;
    }
    public function setName($value)
    {
        $this->name = $value;
    }
    public function setDescription($value)
    {
        $this->description = $value;
    }
    public function setPrice($value)
    {
        $this->price = $value;
    }
    public function setInterval($value)
    {
        $this->interval = $value;
    }

    public function headers(array $header = [])
    {
        $headers = ['Authorization' => "Bearer " . $this->token];
        if (count($header)) {
            $headers = array_merge($header, $headers);
        }
        return $headers;
    }
    protected function setBillingCycles()
    {
        $this->billing_cycles = [
            "frequency"      =>
            [
                "interval_unit"  => $this->interval,
                "interval_count" => 1,
            ],
            "tenure_type"    => "REGULAR",
            "sequence"       => 1,
            "total_cycles"   => 0,
            "pricing_scheme" => [
                "fixed_price" => [
                    "value"         => $this->price,
                    "currency_code" => "USD",
                ],
            ],
        ];
    }
    protected function setPaymentPreference()
    {
        $this->payment_preferences = [
            "auto_bill_outstanding"     => true,
            "setup_fee"                 => [
                "value"         => "0",
                "currency_code" => "USD",
            ],
            "setup_fee_failure_action"  => "CONTINUE",
            "payment_failure_threshold" => 3,
        ];
    }
    protected function setTaxes()
    {
        $this->taxes = [
            "percentage" => "10",
            "inclusive"  => true,
        ];
    }

}
