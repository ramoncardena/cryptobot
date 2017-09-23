<?php

namespace App\Library\Services\Bittrex;

use GuzzleHttp\Client;

class Bittrex
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
    public function __construct($key = null, $secret = null)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->client = new Client([
            'base_uri' => 'https://bittrex.com/api/v1.1/'
        ]);
    }
    /**
     * @return object
     */
    public function getMarkets()
    {
        return $this->request('public/getmarkets');
    }
    /**
     * @return object
     */
    public function getCurrencies()
    {
        return $this->request('public/getcurrencies');
    }
    /**
     * @param string $market
     *
     * @return object
     */
    public function getTicker($market = 'BTC-LTC')
    {
        return $this->request('public/getticker', "market={$market}");
    }
    /**
     * @return object
     */
    public function getMarketSummaries()
    {
        return $this->request('public/getmarketsummaries');
    }
    /**
     * @param string $market
     *
     * @return object
     */
    public function getMarketSummary($market = 'BTC-LTC')
    {
        return $this->request('public/getmarketsummary', "market={$market}");
    }
    /**
     * @param string $market
     * @param string $type
     *
     * @return object
     */
    public function getOrderBook($market = 'BTC-LTC', $type = 'both')
    {
        return $this->request('public/getorderbook', "market={$market}&type={$type}");
    }
    /**
     * @param string $market
     *
     * @return object
     */
    public function getMarketHistory($market = 'BTC-LTC')
    {
        return $this->request('public/getmarkethistory', "market={$market}");
    }
    /**
     * @return object
     */
    public function getBalances()
    {
        return $this->authRequest('account/getbalances');
    }
    /**
     * @param string $currency
     *
     * @return object
     */
    public function getBalance($currency = 'BTC')
    {
        return $this->authRequest('account/getbalance', "currency={$currency}");
    }
    /**
     * @param string $currency
     *
     * @return object
     */
    public function getDepositAddress($currency = 'BTC')
    {
        return $this->authRequest('account/getdepositaddress', "currency={$currency}");
    }
    /**
     * @param        $currency
     * @param        $quantity
     * @param        $address
     * @param string $paymentid
     *
     * @return object
     */
    public function withdraw($currency, $quantity, $address, $paymentid = '')
    {
        $params = "currency={$currency}&quantity={$quantity}&address={$address}&paymentid={$paymentid}";
        return $this->authRequest('account/withdraw', $params);
    }
    /**
     * @param $uuid
     *
     * @return object
     */
    public function getOrder($uuid)
    {
        return $this->authRequest('account/getorder', "uuid={$uuid}");
    }
    /**
     * @param string $market
     *
     * @return object
     */
    public function getOrderHistory($market = '')
    {
        return $this->authRequest('account/getorderhistory', "market={$market}");
    }
    /**
     * @param string $currency
     *
     * @return object
     */
    public function getWithdrawalHistory($currency = '')
    {
        return $this->authRequest('account/getwithdrawalhistory', "currency={$currency}");
    }
    /**
     * @param string $currency
     *
     * @return object
     */
    public function getDepositHistory($currency = '')
    {
        return $this->authRequest('account/getdeposithistory', "currency={$currency}");
    }
    /**
     * @param $market
     * @param $quantity
     * @param $rate
     *
     * @return object
     */
    public function buyLimit($market, $quantity, $rate)
    {
        $params = "market={$market}&quantity={$quantity}&rate={$rate}";
        return $this->authRequest('market/buylimit', $params);
    }
    /**
     * @param $market
     * @param $quantity
     * @param $rate
     *
     * @return object
     */
    public function sellLimit($market, $quantity, $rate)
    {
        $params = "market={$market}&quantity={$quantity}&rate={$rate}";
        return $this->authRequest('market/selllimit', $params);
    }
    /**
     * @param $uuid
     *
     * @return object
     */
    public function cancel($uuid)
    {
        return $this->authRequest('market/cancel', "uuid={$uuid}");
    }
    /**
     * @param string $market
     *
     * @return object
     */
    public function getOpenOrders($market = 'BTC-LTC')
    {
        return $this->authRequest('market/getopenorders', "market={$market}");
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
        $uri        = "https://bittrex.com/api/v1.1/{$endpoint}?{$params}";
        // $ch = curl_init($uri);
        // $execResult = curl_exec($ch);
        // return json_decode($execResult);

        $res = $this->client->get("{$endpoint}?{$params}");
        return json_decode($res->getBody()->getContents());
    }
    /**
     * Makes an authenticated request using API credentials
     *
     * @param string $endpoint
     * @param string $params
     *
     * @return object
     */
    protected function authRequest($endpoint, $params ='')
    {
        $nonce      = time();
        $uri        = "https://bittrex.com/api/v1.1/{$endpoint}?apikey={$this->key}&nonce={$nonce}&{$params}";
        $sign       = hash_hmac('sha512',$uri,$this->secret);

        // $ch = curl_init($uri);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign));
        // $execResult = curl_exec($ch);
        // return json_decode($execResult);

        $client = new Client;
        $res = $client->get($uri, [
            'headers' => [
                'apisign' => $sign
            ]
        ]);
        return json_decode($res->getBody()->getContents());
    }
}