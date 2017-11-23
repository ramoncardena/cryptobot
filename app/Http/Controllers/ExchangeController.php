<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exchange;

class ExchangeController extends Controller
{
	protected $exchange;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {

    }

    public function getfee($name) {
    	$this->exchange = Exchange::where('name', strtolower($name))
               ->get();;
        return $this->exchange[0]->fee;
    }
}
