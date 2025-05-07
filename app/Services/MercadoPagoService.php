<?php


namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class MercadoPagoService
{
    /**
     * GuzzleHttp\Client
     */
    private $client = null;
    /**
     * String $baseUrl
     */
    private $baseUrl;
    /**
     * String $token
     */
    private $token;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (getenv('ACCESS_TOKEN') != null) {
            $this->token = getenv('ACCESS_TOKEN');
        }
        $this->baseUrl = getenv('URL_API_MP');
        $this->client = new Client();

    }

    /**
     * statusCodeHandling() function
     * @param \Exception $e
     */
    private function statusCodeHandling(\Exception $e)
    {
        $response = $e->getResponse();

        return $response;
    }

    /**
     * get() method
     * Execute GET operations
     */
    public function getPago($uri)
    {
        try {
            // $url = $this->baseUrl . $uri.'?access_token='.$this->token.'&status=approved&offset=0&limit=100';
            $url = $this->baseUrl . $uri . '?access_token=' . $this->token . '&offset=0&limit=100';
            $response = $this->client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ], \GuzzleHttp\RequestOptions::JSON,
            ]);
        } catch (RequestException $e) {
            $response = $this->StatusCodeHandling($e);
        }
        return json_decode($response->getBody());
    }


}