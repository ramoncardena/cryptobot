<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Library\Services\CoinGuru;

use App\User;
use App\Ticker;

class TickerController extends Controller
{

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
	       	$user = Auth::user();
		    $guru = new CoinGuru;

	        if ($user->tickers->where('symbol', $request->asset_symbol)->count() == 0) {

		    	$coinInfo = $guru->getCoinInfo($request->asset_symbol);
		        $ticker = new Ticker;
		        $ticker->user_id = $user->id;
		        $ticker->symbol = $request->asset_symbol;
		        $ticker->coinname = $coinInfo->coinname;
		        $ticker->fullname = $coinInfo->fullname;
		        $ticker->imageurl = $coinInfo->imageurl;
		        $ticker->url = $coinInfo->url;
	            $ticker->exchange = " "; 
		        $ticker->save();
		        $request->session()->flash('status-text', 'Ticker for ' . $request->asset_symbol . ' added!');
                $request->session()->flash('status-class', 'success');
		    }
		    else {
		    	$request->session()->flash('status-text', 'Ticker for ' . $request->asset_symbol . ' already exists!');
                $request->session()->flash('status-class', 'alert');
		    }

	        return redirect('/dashboard');
    	
    	} catch (\Exception $e) {
    
    		return response($e->getMessage(). " " . $e->getCode() . " " . $e->getFile() . ":" . $e->getLine(), 500)->header('Content-Type', 'text/plain');
    		
    	}
    	
    }

     public function destroy(Request $request, $symbol)
    {
         try {
            $user = Auth::user();

            $ticker = $user->tickers->where('symbol', $symbol)->first();
            Ticker::destroy($ticker->id);

            $request->session()->flash('status-text', 'Ticker for ' . $symbol . ' removed!');
            $request->session()->flash('status-class', 'success');
            return response("OK", 200)->header('Content-Type', 'text/plain');


        } catch (\Exception $e) {

            return response($e->getMessage(). " " . $e->getCode() . " " . $e->getFile() . ":" . $e->getLine(), 500)->header('Content-Type', 'text/plain');

        }
    }
}
