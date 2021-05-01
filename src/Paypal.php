<?php

/*
 * This file is part of the Laravel Paypal package.
 *
 * (c) Awonusi Olajide <awonusiolajide@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenithcoder\Paypal;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Zenithcoder\Paypal\Exceptions\IsNullException;
use Zenithcoder\Paypal\Exceptions\PaymentVerificationFailedException;

class Paypal
{

    /**
     * @var string
     */
    protected $client_secret;

    /**
     * @var string
     */
    protected $client_id;

    /**
     * @var string
     */
    protected $access_token;
    
    /**
     * Instance of Client
     * @var Client
     */
    protected $client;

    /**
     *  Response from requests made to Paypal
     * @var mixed
     */
    protected $response;

    /**
     * Paypal API base Url
     * @var string
     */
    protected $baseUrl;

    public function __construct()
    {
        $this->setClient_id();
        $this->setClient_secret();
        $this->getAccessToken();
        // $this->setKey();
        $this->setBaseUrl();
        $this->setRequestOptions();
    }
        
    /**
     * Get the value of client_secret
     *
     * @return  string
     */
    public function getClient_secret()
    {
        return $this->client_secret;
    }

    /**
     * Set the value of client_secret
     *
     * @param  string  $client_secret
     *
     * @return  self
     */
    public function setClient_secret()
    {
        $this->client_secret = Config::get('paypal.client_secret');

        return $this;
    }

    /**
     * Get the value of client_id
     *
     * @return  string
     */
    public function getClient_id()
    {
        return $this->client_id;
    }

    /**
     * Set the value of client_id
     *
     *
     *
     * @return  self
     */
    public function setClient_id()
    {
        $this->client_id = Config::get('paypal.client_id');

        return $this;
    }



    /**
     * Get paypal API base Url
     *
     * @return  string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set paypal API base Url
     *
     *
     * @return  self
     */
    public function setBaseUrl()
    {
        $this->baseUrl = Config::get('paypal.paymentUrl');

        return $this;
    }

    /**
    * Set options for making the Client request
    */
    private function setRequestOptions()
    {
        $authBearer = 'Bearer '. $this->access_token;

        $this->client = new Client(
            [
                'base_uri' => $this->baseUrl,
                'headers' => [
                    'Authorization' => $authBearer,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json'
                ]
            ]
        );
    }

    public function getAccessToken()
    {
        $this->apiEndPoint = 'v1/oauth2/token';
        $client = new Client();

        $response = $client->request(
            'POST',
            'https://api-m.sandbox.paypal.com/v1/oauth2/token',
            [
      'auth' => [$this->client_id, $this->client_secret],
      'client_token' => [
        'grant_type' => 'client_credentials'
      ],
      'form_params' => [
        'grant_type' => 'client_credentials'
    ]
    ]
        );

        $res = json_decode($response->getBody()->getContents(), true);
         

        if (isset($res['access_token'])) {
            $this->setAccessToken($res);
        }

        return $this;
    }

    /**
    * Set PayPal Rest API access token.
    *
    * @param array $response
    *
    * @return void
    */
    public function setAccessToken($response)
    {
        $this->access_token = $response['access_token'];

        $this->options['headers']['Authorization'] = "{$response['token_type']} {$this->access_token}";
    }


    /**

    * Initiate a payment request to Paypal
    * Included the option to pass the payload to this method for situations
    * when the payload is built on the fly (not passed to the controller from a view)
    * @return Paypal
    */

    public  function makePaymentRequest($data = null)
    {
        if ($data == null) {
            $data = [
                "intent"=> "CAPTURE",
               "purchase_units" => [
               0 => [
                    "amount"=> [
                   "currency_code"=> request()->currency_code,
                   "value" => intval(request()->amount),
                   'metadata' => request()->metadata
                ]
                ]
                ]
            ];
            // Remove the fields which were not sent (value would be null)
            array_filter($data);
        }

        $this->setHttpResponse('/v2/checkout/orders', 'POST', $data);

        return $this;
    }


    /**
     * @param string $relativeUrl
     * @param string $method
     * @param array $body
     * @return Paypal
     * @throws IsNullException
     */
    private function setHttpResponse($relativeUrl, $method, $body = [])
    {
        if (is_null($method)) {
            throw new IsNullException("Empty method not allowed");
        }

        $this->response = $this->client->{strtolower($method)}(
            $this->baseUrl . $relativeUrl,
            ["body" => json_encode($body)]
        );

        return $this;
    }

    /**
     * Get the authorization url from the callback response
     * @return Paypal
     */
    public function getAuthPaymentUrl()
    {
        $this->makePaymentRequest();

        //  dd( $this->getResponse());
        $this->url = $this->getResponse()['links'][1]['href'];
        //dd($this->url );
        return $this;
    }
    
    /**
    * Get the authorization callback response
    * @return array
    */
    public function getAuthorizationResponse($data)
    {
        $this->makePaymentRequest($data);

        $this->url = $this->getResponse()['links'][1]['href'];

        return $this->getResponse();
    }
    

    /**
     * Get Payment details from webhooks
     * @return json
     * @throws PaymentVerificationFailedException
     */
    public function getPaymentData()
    {
        return request()->all();
        //   $this->event_types = request()->query('event_types');

    /*    switch ($this->event_types)
        {
            case "PAYMENT.SALE.COMPLETED":
                // Handle payment completed
                break;
            case "BILLING.SUBSCRIPTION.PAYMENT.FAILED":
                // Handle payment failed
                break;
                // Handle other webhooks
            case "BILLING.SUBSCRIPTION.CANCELLED":
                    // Handle subscription cancelled
                break;
            case "BILLING.SUBSCRIPTION.SUSPENDED":
                    // Handle subscription suspended
                break;
                    // Handle other webhooks
            default:
                break;
        }*/
    }

    /**
     * Fluent method to redirect to Paypal Payment Page
     */
    public function redirectNow()
    {
        return redirect($this->url);
    }

    /**
    * Get the whole response from a get operation
    * @return array
    */
    private function getResponse()
    {
        return json_decode($this->response->getBody(), true);
    }

    /**
     * Get the data response from a get operation
     * @return array
     */
    private function getData()
    {
        return $this->getResponse();
    }
}
