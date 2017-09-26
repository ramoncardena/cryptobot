<?php

namespace App\Library\Services\Bitcoin;

use GuzzleHttp\Client;

class Bitcoin
{
    /**
     * @var $key
     */
    protected $key;
    /**
     * @var $secret
     */
    protected $secret;
    protected $client;
    /**
     * Bittrex constructor.
     *
     * @param $key
     * @param $secret
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://blockchain.info/ticker'
        ]);
    }

    /**
     * @return object
     */
    public function getTicker()
    {
        return $this->request('');
    }

    /**
     * Makes a public request
     *
     * @param string $endpoint
     * @param string $params
     *
     * @return object
     */
    protected function request($endpoint, $params = '')
    {
        //$uri        = "https://bittrex.com/api/v1.1/{$endpoint}?{$params}";
        // $ch = curl_init($uri);
        // $execResult = curl_exec($ch);
        // return json_decode($execResult);

        $res = $this->client->get("{$endpoint}");
        return json_decode($res->getBody()->getContents());
    }
}