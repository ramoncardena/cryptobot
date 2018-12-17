<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Library\Services\Broker;

class BrokerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPairs($exchange)
    {
        try {
            // New Broker
            $broker = new Broker;
            $broker->setUser(Auth::user());
            $broker->setExchange($exchange);

            $brokerResponse = $broker->getPairs();

            return $brokerResponse;
            
        } catch (\Exception $e) {

                Log::critical("[BrokerController] Exception getting pairs: " . $e->getMessage());
        }

    }
    
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function getFee($exchange)
    {
        try {

            // New broker
            $broker = new Broker;
            $broker->setUser(Auth::user());
            $broker->setExchange($exchange);

            // Get exchange fee
            $brokerResponse = $broker->getFee();

            return $brokerResponse;
            
        } catch (\Exception $e) {

                Log::critical("[BrokerController] Exception getting fee: " . $e->getMessage());

        }

    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function getTicker($exchange, $coin, $base)
    {
        try {

            // New broker
            $broker = new Broker;
            $broker->setUser(Auth::user());
            $broker->setExchange($exchange);

            // Get exchange fee
            $market = $coin . '/' . $base;

            $brokerResponse = $broker->getSymbolTicker($market);

            return $brokerResponse;
            
        } catch (\Exception $e) {

             Log::critical("[BrokerController] Exception getting fee: " . $e->getMessage());
             return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');

        }

    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function getBalances($exchange)
    {
        try {

            // New broker
            $broker = new Broker;
            $broker->setUser(Auth::user());
            $broker->setExchange($exchange);

            // Get exchange fee

            $brokerResponse = $broker->getBalances2();

            return response()->json($brokerResponse);
            
        } catch (\Exception $e) {

             Log::critical("[BrokerController] Exception getting balance: " . $e->getMessage());
             return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');

        }

    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function getCoinInfo($coin)
    {
        try {

            // New broker
            $broker = new Broker;
            $broker->setUser(Auth::user());

            // Get exchange fee

            $brokerResponse = $broker->getCoinInfo($coin);

            return response()->json($brokerResponse);
            
        } catch (\Exception $e) {

             Log::critical("[BrokerController] Exception getting balance: " . $e->getMessage());
             return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');

        }

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
