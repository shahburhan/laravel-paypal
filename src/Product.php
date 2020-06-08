<?php

namespace ShahBurhan\LaravelPayPal;

use GuzzleHttp\Exception\ClientException;

class PayPalProduct
{
    /**
     * Stores GuzzleClient Instance
     * @var object
     */
    protected $client = null;
    /**
     * The PayPal Endpoint to create a product
     * @var string
     */
    protected $store = '/v1/catalogs/products';
    /**
     * The PayPal Endpoint to update a product
     * @var string
     */
    protected $update = '/v1/catalogs/products/{product_id}';
    /**
     * The PayPal Endpoint to list all product
     * @var string
     */
    protected $list = '/v1/catalogs/products';
    /**
     * The PayPal Endpoint to fetch a product
     * @var string
     */
    protected $show = '/v1/catalogs/products/{product_id}';
    /**
     * Product Category
     * @var string
     */
    protected $category = 'ONLINE_SERVICES';
    /**
     * Product Type
     * @var string
     */
    protected $type = 'DIGITAL';
    /**
     * Generated Access Token
     * @var string | null
     */
    protected $token = null;
    /**
     * Name associated with a product
     * @var string
     */
    protected $name = '';
    /**
     * Description associated with a product
     * @var string
     */
    protected $description = '';
    /**
     * ID associated with a product
     * @var string | null
     */
    protected $ID = null;

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
        $body = [
            "id"          => $this->id,
            "name"        => $this->name,
            "description" => $this->description,
            "type"        => $this->type,
        ];

        try {
            $response = $this->client->request("POST", $this->store, ["headers" => $this->headers(), "json" => $body]);
        } catch (ClientException $e) {
            dd($e->getMessage());
        }

        return $response->getBody();
    }

    /**
     * Method to fetch a product
     * @return Response
     */
    public function show()
    {
        $this->show = str_replace('{product_id}', $this->id, $this->show);
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
    public function headers(array $header = [])
    {
        $headers = ['Authorization' => "Bearer " . $this->token];
        if (count($header)) {
            $headers = array_merge($header, $headers);
        }
        return $headers;
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
}
