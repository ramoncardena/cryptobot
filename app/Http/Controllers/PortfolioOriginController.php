<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\User;
use App\Portfolio;
use App\PortfolioOrigin;
use App\PortfolioAsset;

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
    	
    	} catch (\Exception $e) {

    		return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');
    		
    	}
    	
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate form
            $validatedData = $request->validate([
                'origin_type' => 'required',
                'origin_name' => 'required'
            ]);

            $this->user = Auth::user();

            $origin = $this->user->origins->where('id', $id)->first();

            if ($origin) {

                // Save old origin name for later
                $oldName = $origin->name;

                // Update origin data
                $origin->type = $request->origin_type;
                $origin->name = $request->origin_name;
                $request->origin_address ? $origin->address = $request->origin_address : $origin->address = "-";
                $origin->save();

                // Retrieve user's assets to change the origin there as well
                $assets = $this->user->assets;
                $assetsFromOrigin = $assets->where('origin_name', $oldName);

                foreach ($assetsFromOrigin as $asset) {
                    $asset->origin_name = $request->origin_name;
                    $asset->save();
                }
            }

            return redirect('/portfolio');
        
        } catch (\Exception $e) {

            return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');
            
        }
        
    }

    public function destroy(Request $request, $id)
    {
        try {

            $this->user = Auth::user();

            $origin = $this->user->origins->where('id', $id)->first();

            // Retrieve user's assets to change the origin there as well
            $assets = $this->user->assets;
            $assetsFromOrigin = $assets->where('origin_name', $origin->name);

            foreach ($assetsFromOrigin as $asset) {
                PortfolioAsset::destroy($asset->id);
            }

            PortfolioOrigin::destroy($origin->id);

            return redirect('/portfolio');
            
        } catch (\Exception $e) {

             return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');

        }
    }
}
