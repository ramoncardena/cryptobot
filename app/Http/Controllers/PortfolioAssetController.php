<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\User;
use App\Portfolio;
use App\PortfolioOrigin;
use App\PortfolioAsset;

class PortfolioAssetController extends Controller
{
    private $user;

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
     * Show the trades dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	try {
    		// Validate form
	        $validatedData = $request->validate([
	            'asset_name' => 'required',
	            'asset_amount' => 'required'
	        ]);

	    	$this->user = Auth::user();

	        $portfolio = Portfolio::where('user_id', $this->user->id)->first();
	        

	        $asset = new PortfolioAsset;
	        $asset->portfolio_id = $portfolio->id;
	        $asset->user_id = $this->user->id;
	        $asset->origin_id = $request->asset_origin;
	        $asset->name = $request->asset_name;
	        $asset->amount = $request->asset_amount;
	        $asset->price = 0;
	        $asset->balance = 0;
	        $asset->counter_value = 0;
	        $asset->save();

	        return redirect('/portfolio');
    	
    	} catch (Exception $e) {

    		return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');
    		
    	}
    	
    }
}
