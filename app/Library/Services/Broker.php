<?php

namespace App\Library\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Library\Services\Facades\Bittrex;
use App\Exchange;
use App\User;

/**
 * Summary
 */
class Broker
{
	public $exchanges;

	public $exchange;

	public $user;


    public function __construct()
    {
        $exchanges = Exchange::all();

        foreach ($exchanges as $exchange) {
		    $this->exchanges[$exchange->name] = $exchange;
		}

    }
 
 	public function setExchange($exchangeName) {

 		if (array_key_exists($exchangeName, $this->exchanges)) {

 			$this->exchange = $this->exchanges[$exchangeName];
 			return $this->exchange;

 		}
 		else {

 			return  false;

 		}

 	}

 	public function setUser($user) {

 		$this->user = $user;

 	}

     public function getFee() {

        try {
            
            switch (strtolower($this->exchange->name)) {
                case 'bittrex':
                    return $this->user->settings()->get('bittrex_fee');
                    break;
            } 

        } catch (Exception $e) {

            // LOG: Exception trying to show trades
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage();

        }

    }

    /**
     * Get no-zero balances from the exchange
     * @return [type] [description]
     */
    public function getBalances() {

        try {

            switch (strtolower($this->exchange->name)) {
                case 'bittrex':
                    Bittrex::setAPI($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));

                    $exchangeResponse= Bittrex::getBalances();

                    if ($exchangeResponse->success == true) {

                        if ($exchangeResponse->result) {
                            $response = new \stdClass();
                            $response->success=true;
                            $response->message="";

                            $allBalances = collect($exchangeResponse->result);
                            $nonZeroBalances = $allBalances->filter(function ($item) {
                                return $item->Balance > 0;
                            });

                            $nonZeroBalances = $nonZeroBalances->map( function ($balance) {
                                unset($balance->Available); 
                                unset($balance->Pending); 
                                return $balance;
                            });

                            $response->result = $nonZeroBalances;
                        }
                        else {
                            $response = new \stdClass();
                            $response->success = false;
                            $response->message = "";
                        }
                        
                        return $response;
                    }
                    else {

                        $response = new \stdClass();
                        $response->success=false;
                        $response->message= $exchangeResponse->message;

                        return $response;
                    }

                    dd($exchangeResponse);

                break;
            }
            
            
        } catch (Exception $e) {

           // LOG: Exception
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage(); 
        }

        ;
    }

    public function getPurchasePrice ($market, $amount)
    {
        try {

            switch (strtolower($this->exchange->name)) {
                case 'bittrex':
                    $exchangeResponse = Bittrex::getOrderHistory($market);

                    if ($exchangeResponse->success == true) {

                        if ($exchangeResponse->result) {
                            
                            $initialAmount = $amount;
                            $amount = 0;
                            $price = 0;
                            foreach ($exchangeResponse->result as $order) {
                                //if ($order->CommissionPaid) $comission = $order->CommissionPaid;
                            
                                if ($order->OrderType == "LIMIT_BUY" && $initialAmount > 0) {
                                      $initialAmount = floatval($initialAmount) - ( floatval($order->Quantity) - floatval($order->QuantityRemaining) );
                                      $amount = $amount + ( floatval($order->Quantity) - floatval($order->QuantityRemaining));
                                      $price = $price + ( ( floatval($order->Quantity) - floatval($order->QuantityRemaining)) * ( floatval($order->PricePerUnit) ) );
                                }
                                elseif ($order->OrderType == "LIMIT_SELL" && $initialAmount > 0) {
                                      $initialAmount = floatval($initialAmount) + ( floatval($order->Quantity) - floatval($order->QuantityRemaining) );
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
                            
                        }
                        else {
                            $response = new \stdClass();
                            $response->success = false;
                            $response->message = "";
                        }

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

            return $exchangeResponse;
            
        } catch (Exception $e) {

            // LOG: Exception trying to show trades
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage();

        }
    }

    public function getTicker ($market)
    {
    	try {

    		switch (strtolower($this->exchange->name)) {
    			case 'bittrex':
    				$exchangeResponse = Bittrex::getTicker($market);

    				if ($exchangeResponse->success == true) {

                        if ($exchangeResponse->result) {
        					$response = new \stdClass();
    				        $response->success=true;
    				        $response->message="";
    				        $response->result = new \stdClass();
    				        $response->result->Bid = $exchangeResponse->result->Bid;
    				        $response->result->Ask = $exchangeResponse->result->Ask;
    				        $response->result->Last = $exchangeResponse->result->Last;
                        }
                        else {
                            $response = new \stdClass();
                            $response->success = false;
                            $response->message = "";
                        }

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

    		return $exchangeResponse;
    		
    	} catch (Exception $e) {

    		// LOG: Exception trying to show trades
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage();

    	}
    }

    public function buyLimit($currency, $amount, $price)
    {
    	try {

    		switch (strtolower($this->exchange->name)) {
    			case 'bittrex':
    				Bittrex::setAPI($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));
    				$exchangeResponse = Bittrex::buyLimit($currency, $amount, $price);
    			
	    			if ($exchangeResponse->success) {

    					$response = new \stdClass();
				        $response->success=true;
				        $response->message="";
				        $response->result = new \stdClass();
				        $response->result->uuid = $exchangeResponse->result->uuid;

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
    		
    	} catch (Exception $e) {
    		
    		// LOG: Exception trying to show trades
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage();

    	}

    }

    public function sellLimit($currency, $amount, $price)
    {
    	try {

    		switch (strtolower($this->exchange->name)) {
    			case 'bittrex':
    				Bittrex::setAPI($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));
    				$exchangeResponse = Bittrex::sellLimit($currency, $amount, $price);
    			
	    			if ($exchangeResponse->success) {
	    
    					$response = new \stdClass();
				        $response->success=true;
				        $response->message="";
				        $response->result = new \stdClass();
				        $response->result->uuid = $exchangeResponse->result->uuid;

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
    		
    	} catch (Exception $e) {
    		
    		// LOG: Exception trying to show trades
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage();

    	}

    }
    public function cancelOrder($orderId) {

        try {

            switch (strtolower($this->exchange->name)) {
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
        } catch (Exception $e) {
        
            // LOG: Exception trying to show trades
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage();
        }


    }


    public function getOrder($orderId)
    {
    	try {

    		switch (strtolower($this->exchange->name)) {
    			case 'bittrex':
    				Bittrex::setAPI($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));
    				$exchangeResponse = Bittrex::getOrder($orderId);
    			
	    			if ($exchangeResponse->success) {

    					$response = new \stdClass();
				        $response->success=true;
				        $response->message="";
				        $response->result = new \stdClass();
				        $response->result->PricePerUnit = $exchangeResponse->result->PricePerUnit;
				        $response->result->Closed = $exchangeResponse->result->Closed;
				        $response->result->OrderUuid = $exchangeResponse->result->OrderUuid;

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
    		
    	} catch (Exception $e) {
    		
    		// LOG: Exception trying to show trades
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage();

    	}

    }
    public function getOrderHistory()
    {
    	try {

    		switch (strtolower($this->exchange->name)) {
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

    	} catch (Exception $e) {

    		// LOG: Exception trying to show trades
            Log::critical("[Broker] Exception: " . $e->getMessage());

            return $e->getMessage();

    	}

    }



}