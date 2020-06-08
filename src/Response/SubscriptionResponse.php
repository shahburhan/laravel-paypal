<?php

namespace ShahBurhan\LaravelPayPal\Response;

class SubscriptionResponse
{
    /**
     * Token Received From PayPal
     * @var $token
     */
    public $token;
    
    /**
     * ID returned from PayPal
     * @var
     */
    public $id;

    /**
     * Status returned from PayPal
     * @var
     */
    public $status;

    /**
     * Response returned from PayPal
     * @var
     */
    public $response;

    /**
     * Links returned from PayPal
     * @var
     */
    public $links;

    public function __construct($response)
    {
        $this->status   = $response['status'];
        $this->response = $response;
        $this->links    = $response['links'];
        $this->id       = $response['id'];
    }

}
