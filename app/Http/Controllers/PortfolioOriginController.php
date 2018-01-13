<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\User;
use App\Portfolio;
use App\PortfolioOrigin;

class PortfolioOriginController extends Controller
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
	            'origin_type' => 'required',
	            'origin_name' => 'required'
	        ]);

	    	$this->user = Auth::user();

	        $portfolio = Portfolio::where('user_id', $this->user->id)->first();

	        $origin = new PortfolioOrigin;
	        $origin->portfolio_id = $portfolio->id;
	        $origin->user_id = $this->user->id;
	        $origin->type = $request->origin_type;
	        $origin->name = $request->origin_name;
	        $request->origin_address ? $origin->address = $request->origin_address : $origin->address = "-";
	        $origin->save();

	        return redirect('/portfolio');
    	
    	} catch (Exception $e) {

    		return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');
    		
    	}
    	
    }
}
