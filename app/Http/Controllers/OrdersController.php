<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Library\Services\Facades\Bittrex;

class OrdersController extends Controller
{
        
    protected $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::check()) 
        {
            $this->user = Auth::user();

            Bittrex::setAPI($this->user->settings()->get('bittrex_key'), $this->user->settings()->get('bittrex_secret'));

            $bittrexOpenOrders = collect(Bittrex::getOpenOrders()->result);
            $bittrexOrderHistory = collect(Bittrex::getOrderHistory()->result);
            
            foreach ($bittrexOpenOrders as $order) {
            	$order->Limit = number_format($order->Limit,8);
            	if ($order->OrderType == "LIMIT_SELL") $order->OrderType = "Limit Sell";
            	if ($order->OrderType == "LIMIT_BUY") $order->OrderType = "Limit Buy";
            }
            foreach ($bittrexOrderHistory as $order) {
            	$order->Limit = number_format($order->Limit,8);
            	$order->Price = number_format($order->Price,8);
            	$order->PricePerUnit = number_format($order->PricePerUnit,8);
            	$order->Commission = number_format($order->Commission,8);
            	if ($order->OrderType == "LIMIT_SELL") $order->OrderType = "Limit Sell";
            	if ($order->OrderType == "LIMIT_BUY") $order->OrderType = "Limit Buy";
            	$order->Closed = substr($order->Closed, 0, strpos($order->Closed, 'T'));

            }

            return view('orders', ['bittrexOpenOrders' => $bittrexOpenOrders, 'bittrexOrderHistory' => $bittrexOrderHistory]);
        }
    }

    public function cancel($order)
    {
    	
    }
}
