<?php
namespace App\Helpers;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class GuzzleHelper
{
    private $client, $url, $headers, $type;

    function __construct($url, $headers = [], $type)
    {
        $this->url = $url;
        $this->headers = $headers;
        $this->type = $type;
        $this->client = new \GuzzleHttp\Client();
    }

    function sendRequest()
    {
        $response = $this->client->request($this->type, $this->url, $this->headers);
        $responseCode = $response->getStatusCode();
        if ($responseCode == 200)
        {
            $body = $response->getBody()->getContents();
            $collection = collect(json_decode($body));

            return $collection;
        }
        
        return false;
    }
}