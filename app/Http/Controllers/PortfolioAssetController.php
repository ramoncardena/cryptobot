<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Library\Services\CoinGuru;

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
	            'asset_symbol' => 'required',
	            'asset_amount' => 'required'
	        ]);

	    	$this->user = Auth::user();

	        $portfolio = Portfolio::where('user_id', $this->user->id)->first();
	        
            

            $guru = new CoinGuru;
            $logoBaseUrl = $guru->cryptocompareCoingetList()->BaseImageUrl;
            $infoBaseUrl = $guru->cryptocompareCoingetList()->BaseLinkUrl;
            $symbol = $request->asset_symbol;
            $coinInfo = $guru->cryptocompareCoingetList()->Data->$symbol;

	        $asset = new PortfolioAsset;
	        $asset->portfolio_id = $portfolio->id;
	        $asset->user_id = $this->user->id;
	        $asset->origin_id = $request->asset_origin;
            $asset->origin_name = $request->asset_origin_name;
	        $asset->symbol = $request->asset_symbol;
	        $asset->amount = $request->asset_amount;
            $asset->full_name = $coinInfo->CoinName;
            $asset->logo_url = $logoBaseUrl . $coinInfo->ImageUrl;
            $asset->info_url = $infoBaseUrl . $coinInfo->Url;
	        $asset->price = 0;
	        $asset->balance = 0;
	        $asset->counter_value = 0;
	        $asset->save();

	        return redirect('/portfolio');
    	
    	} catch (Exception $e) {
            dd($e->getMessage());
    		return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');
    		
    	}
    	
    }
}
