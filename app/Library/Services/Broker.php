<?php

namespace App\Library\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Library\Services\CoinGuru;
use App\Library\Services\Facades\Bittrex;
use App\User;
use App\Connection;

/**
* Summary
*/
class Broker
{
    public $exchanges;

    public $exchange;

    public $user;

    // Multi
    public function __construct()
    {
        $this->exchanges = \ccxt\Exchange::$exchanges;

    }

    // Multi
    public function setExchange($exchangeName) 
    {

        if (in_array($exchangeName, $this->exchanges)) {

            $this->exchange = $exchangeName;

            return $this->exchange;

        }
        else {

            return  false;

        }

    }

    // Multi
    public function setUser($user) 
    {

        $this->user = $user;

    }

    // Multi
    public function getFee() 
    {

        try {

            $exchangeName = $this->exchange;
            $connections = $this->user->connections;
            $connection = $connections->where('exchange', $exchangeName)->first();

            $fee = $connection->fee;

            return $fee;

        } catch (\Exception $e) {

            // LOG: Exception trying to show trades
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage();

        }

    }



    // Multi
    public function getBalances2() 
    {
        try {

            $exchangeName = $this->exchange;
            $connections = $this->user->connections;
            $connection = $connections->where('exchange', $exchangeName)->first();
            $api = decrypt($connection->api);
            $secret =decrypt($connection->secret);

            $myexchange = '\\ccxt\\' . $exchangeName;
            date_default_timezone_set ('UTC');
            $exchangeConnection  = new $myexchange  (array (
                'apiKey' => $api,
                'secret' => $secret,
            ));

            $balance = $exchangeConnection->fetch_balance();

            $allBalances = $balance['total'];

            $nonZeroBalances = array_where($allBalances, function ($value, $key) {
                return $value > 0;
            });

            $response = new \stdClass();
            $response->success=true;
            $response->message="";
            $response->result = $nonZeroBalances;

            return $response;
            

        } catch (\Exception $e) {

            // LOG: Exception
            Log::critical("[Broker getBalances] Exception: " . $e->getMessage());

            $response = new \stdClass();
            $response->success = false;
            $response->message = $e->getMessage();

            return $response; 
        }

    }

 

    // Multi
    public function getPurchasePrice2 ($market, $amount)
    {
        try {

            if($market=='BTC/BTC') {
                $response = new \stdClass();
                $response->success=true;
                $response->message="";
                $response->result = new \stdClass();
                $response->result->AvaragePrice = 0;
                return $response;
            }
            elseif ($market == 'IOT/BTC') {
                $market = 'IOTA/BTC';
            }

            $exchangeName = $this->exchange;
            $connections = $this->user->connections;

            $connection = $connections->where('exchange', $exchangeName)->first();
            $api = decrypt($connection->api);
            $secret =decrypt($connection->secret);

            $myexchange = '\\ccxt\\' . $exchangeName;
            date_default_timezone_set ('UTC');
            $exchangeConnection  = new $myexchange  (array (
                'apiKey' => $api,
                'secret' => $secret,
            ));

            // Get orders from exchange for this market
            $orders = $exchangeConnection->fetchOrders($market);    

            $initialAmount = $amount;
            $amount = 0;
            $price = 0;
            foreach ($orders as $order) {
                //if ($order->CommissionPaid) $comission = $order->CommissionPaid;

                array_key_exists('average', $order) ? $orderPrice = $order['average'] : $orderPrice = $order['price'];

                if (strtolower($order['side']) == "buy" && $initialAmount > 0) {
                    $initialAmount = floatval($initialAmount) - ( floatval($order['amount']) - floatval($order['remaining']) );
                    $amount = $amount + ( floatval($order['amount']) - floatval($order['remaining']));
                    $price = $price + ( ( floatval($order['amount']) - floatval($order['remaining'])) * ( floatval($orderPrice) ) );
                }
                elseif (strtolower($order['side']) == "sell" && $initialAmount > 0) {
                    $initialAmount = floatval($initialAmount) + ( floatval($order['amount']) - floatval($orderPrice) );
                }
            }

            $response = new \stdClass();
            $response->success=true;
            $response->message="";
            $response->result = new \stdClass();

            if ( $amount <= 0) {
                $response->result->AvaragePrice = 0;
            }
            else {
                $response->result->AvaragePrice = floatval($price)/floatval($amount);
            }

            return $response;

        } catch (\Exception $e) {

            // LOG: Exception trying to show trades
            Log::critical("[Broker getPurchasePrice] Exception: " . $e->getMessage());

            $response = new \stdClass();
            $response->success=false;
            $response->message= $e->getMessage();

            return $response;

        }
    
    }

    // Multi
    public function getPairs()
    {
        try {

            $exchangeName = $this->exchange;
            $connections = $this->user->connections;

            $connection = $connections->where('exchange', $exchangeName)->first();
            $api = decrypt($connection->api);
            $secret =decrypt($connection->secret);

            $myexchange = '\\ccxt\\' . $exchangeName;
            date_default_timezone_set ('UTC');
            $exchangeConnection  = new $myexchange  (array (
                'apiKey' => $api,
                'secret' => $secret,
            ));
      
            // Get pairs from exchange
            $exchangeConnection->loadMarkets(); 
            $pairs = $exchangeConnection->symbols;

            $response = new \stdClass();
            $response->success=true;
            $response->message="";
            $response->result = new \stdClass();
            $response->result->pairs = $pairs;            

            return response()->json($response);

            
        } catch (\Exception $e) {
            // LOG: Exception trying to show trades
            Log::critical("[Broker getPairs] Exception: " . $e->getMessage());

            $response = new \stdClass();
            $response->success=false;
            $response->message= $e->getMessage();

            return response()->json($response);
        }
    }

    // Multi
    public function getSymbolTicker($market)
    {
        try {

            $exchangeName = $this->exchange;

            $myexchange = '\\ccxt\\' . $exchangeName;
            date_default_timezone_set ('UTC');
            $exchangeConnection  = new $myexchange();

            // Get pairs from exchange
            $ticker = $exchangeConnection->fetchTicker($market); 

            $response = new \stdClass();
            $response->success=true;
            $response->message="";
            $response->result = new \stdClass();
            $response->result->ticker = $ticker;            

            return response()->json($response);
            
        } catch (\Exception $e) {
            // LOG: Exception trying to show trades
            Log::critical("[Broker getSymbolTicker] Exception: " . $e->getMessage());

            $response = new \stdClass();
            $response->success=false;
            $response->message= $e->getMessage();

            return response()->json($response);
        }
    }

    // Multi
    public function getCoinInfo($coin)
    {
        try {

            // Get Cryptocompare coin list properties
            $guru = new CoinGuru;
            $coinList = $guru->cryptocompareCoingetList();

            // TODO controlar si retorna error
            $logoBaseUrl = $coinList->BaseImageUrl;
            $infoBaseUrl = $coinList->BaseLinkUrl;

            $symbol = strtoupper($coin);
            if ($symbol == "IOTA") $symbol = "IOT"; 
            $coinInfo = $coinList->Data->$symbol;

            $logoUrl = $logoBaseUrl . $coinInfo->ImageUrl;
            $infoUrl = $infoBaseUrl . $coinInfo->Url;
            $fullName = $coinInfo->CoinName;

            $response = new \stdClass();
            $response->success=true;
            $response->message="";
            $response->result = new \stdClass();
            $response->result->FullName = $fullName;
            $response->result->LogoUrl = $logoUrl;
            $response->result->InfoUrl = $infoUrl;

            return $response;

        } catch (\Exception $e) {
            // LOG: Exception trying to show trades
            Log::critical("[Broker getCoinInfo] Exception: " . $e->getMessage());

            $response = new \stdClass();
            $response->success=false;
            $response->message= $e->getMessage();

            return $response;
        }
    }


    // Multi
    public function getTicker2 ($market)
    {
        try {

            $exchangeName = $this->exchange;

            $myexchange = '\\ccxt\\' . $exchangeName;
            date_default_timezone_set ('UTC');
            $exchangeConnection  = new $myexchange();

            $ticker = $exchangeConnection->fetchTicker($market); 

            $response = new \stdClass();
            $response->success=true;
            $response->message="";
            $response->result = new \stdClass();

            $response->result->Bid = $ticker['bid'];
            $response->result->Ask = $ticker['ask'];
            $response->result->Last = $ticker['last'];    

            return $response;

        } catch (\Exception $e) {

            // LOG: Exception trying to show trades
            Log::critical("[Broker getTicker] Exception: " . $e->getMessage());

            $response = new \stdClass();
            $response->success=false;
            $response->message= $exchangeResponse->message;

            return $response;

        }
    
    }

    // Multi
    public function buyLimit2($currency, $amount, $price)
    {
        try {

            $exchangeName = $this->exchange;
            $connections = $this->user->connections;

            $connection = $connections->where('exchange', $exchangeName)->first();
            
            $api = decrypt($connection->api);
            $secret =decrypt($connection->secret);

            $myexchange = '\\ccxt\\' . $exchangeName;
            date_default_timezone_set ('UTC');
            $exchangeConnection  = new $myexchange  (array (
                'apiKey' => $api,
                'secret' => $secret,
            ));

            $order = $exchangeConnection->createLimitBuyOrder($currency, $amount, $price);

 
            $response = new \stdClass();
            $response->success=true;
            $response->message="";
            $response->result = new \stdClass();
            $response->result->uuid = $order['id'];

            return $response;
                

        } catch (\Exception $e) {

            // LOG: Exception trying to show trades
            Log::critical("[Broker buyLimit] Exception Buying: " . $e->getMessage());

            $response = new \stdClass();
            $response->success = false;
            $response->message = $e->getMessage();
            return $response; 

        }

    }



    // Multi
    public function sellLimit2($currency, $amount, $price)
    {
        try {

            $exchangeName = $this->exchange;
            $connections = $this->user->connections;

            $connection = $connections->where('exchange', $exchangeName)->first();
            $api = decrypt($connection->api);
            $secret =decrypt($connection->secret);

            $myexchange = '\\ccxt\\' . $exchangeName;
            date_default_timezone_set ('UTC');
            $exchangeConnection  = new $myexchange  (array (
                'apiKey' => $api,
                'secret' => $secret,
            ));

            $order = $exchangeConnection->createLimitSellOrder($currency, $amount, $price);

            $response = new \stdClass();
            $response->success=true;
            $response->message="";
            $response->result = new \stdClass();
            $response->result->uuid = $order['id'];

            return $response;
         

        } catch (\Exception $e) {

            // LOG: Exception trying to show trades
            Log::critical("[Broker sellLimit] Exception: " . $e->getMessage());

            $response = new \stdClass();
            $response->success = false;
            $response->message = $e->getMessage();
            return $response; 

        }

    }


    // Multi
    public function cancelOrder2($orderId) 
    {

        try {

            $exchangeName = $this->exchange;
            $connections = $this->user->connections;
            $order = Order::where('order_id', $orderId)->first();

            $symbol = $order->trade->pair;

            $connection = $connections->where('exchange', $exchangeName)->first();
            $api = decrypt($connection->api);
            $secret =decrypt($connection->secret);

            $myexchange = '\\ccxt\\' . $exchangeName;
            date_default_timezone_set ('UTC');
            $exchangeConnection  = new $myexchange  (array (
                'apiKey' => $api,
                'secret' => $secret,
            ));

            $exchangeResponse = $exchangeConnection->cancelOrder($orderId, $symbol);

            $response = new \stdClass();
            $response->success=true;
            $response->message="";
            $response->result = new \stdClass();
            $response->response = $exchangeResponse;
            return $response;
           

        } catch (\Exception $e) {

            // LOG: Exception trying to show trades
            Log::critical("[Broker cancelOrder] Exception: " . $e->getMessage());

            $response = new \stdClass();
            $response->success=false;
            $response->message= $exchangeResponse->message;

            return $response;
        }

    }
    // Old
    public function cancelOrder($orderId) 
    {

        try {

            switch (strtolower($this->exchange)) {
                case 'bittrex':
                Bittrex::setAPI($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));
                $exchangeResponse = Bittrex::cancel($orderId);

                if ($exchangeResponse->success) {

                    $response = new \stdClass();
                    $response->success=true;
                    $response->message="";
                    $response->result = new \stdClass();

                    return $response;
                }
                else {

                    $response = new \stdClass();
                    $response->success=false;
                    $response->message= $exchangeResponse->message;

                    return $response;
                }
                break;
            }
        } catch (\Exception $e) {

        // LOG: Exception trying to show trades
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage();
        }

    }

 

    // Multi
    public function getOrder2($orderId)
    {
        try {

            $exchangeName = $this->exchange;
            $connections = $this->user->connections;

            $connection = $connections->where('exchange', $exchangeName)->first();
            $api = decrypt($connection->api);
            $secret =decrypt($connection->secret);

            $myexchange = '\\ccxt\\' . $exchangeName;
            date_default_timezone_set ('UTC');
            $exchangeConnection  = new $myexchange  (array (
                'apiKey' => $api,
                'secret' => $secret,
            ));


            // Get orders from exchange for this market
            $order = $exchangeConnection->fetchOrder($orderId);    


            ($order['status'] == 'closed') ? $orderIsClosed = true :  $orderIsClosed = false;

            if ($orderIsClosed) {
                array_key_exists('average', $order) ? $pricePerUnit = $order['average'] : $pricePerUnit = $order['price'];
             
            }
            else {
                $pricePerUnit = 0;
            }

            $response = new \stdClass();
            $response->success=true;
            $response->message="";
            $response->result = new \stdClass();
            $response->result->OrderUuid = $order['id'];
            $response->result->PricePerUnit = $pricePerUnit;
            $response->result->Closed = $orderIsClosed;

            return $response;


        } catch (\Exception $e) {

        // LOG: Exception trying to show trades
            Log::critical("[Broker getORder] Exception: " . $e->getMessage());
            
            $response = new \stdClass();
            $response->success=false;
            $response->message= $exchangeResponse->message;

            return $response;

        }

    }

    public function getOrderHistory()
    {
        try {

            switch (strtolower($this->exchange)) {
                case 'bittrex':

                Bittrex::setAPI($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));
                $exchangeResponse = Bittrex::getOrderHistory();

                if ($exchangeResponse->success) {
                    return $exchangeResponse->result;
                }
                else {
                    return ["success" => false, "message" => $exchangeResponse->message];
                }
                break;
            }

        } catch (\Exception $e) {

        // LOG: Exception trying to show trades
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage();

        }

    }

}