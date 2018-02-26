<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Library\Services\CoinGuru;

use App\User;
use App\Ticker;

class TickerController extends Controller
{
	public $user;

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
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
	    // DATA FOR MODALS (New Ticker)
	    // Coin list
	    $guru = new CoinGuru;
	}

	 public function store(Request $request)
    {
    	try {
    		// Validate form
	        $validatedData = $request->validate([
	            'asset_symbol' => 'required',
	        ]);

	    	$this->user = Auth::user();
	    	$guru = new CoinGuru;

	    	$coinInfo = $guru->getCoinInfo($request->asset_symbol);
	        $ticker = new Ticker;
	        $ticker->user_id = $this->user->id;
	        $ticker->symbol = $request->asset_symbol;
	        $ticker->coinname = $coinInfo->coinname;
	        $ticker->fullname = $coinInfo->fullname;
	        $ticker->imageurl = $coinInfo->imageurl;
	        $ticker->url = $coinInfo->url;
            $ticker->exchange = " "; 
	        $ticker->save();

	        return redirect('/dashboard');
    	
    	} catch (\Exception $e) {
    
    		return response($e->getMessage(). " " . $e->getCode() . " " . $e->getFile() . ":" . $e->getLine(), 500)->header('Content-Type', 'text/plain');
    		
    	}
    	
    }

}
