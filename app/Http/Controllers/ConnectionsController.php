<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Connection;

class ConnectionsController extends Controller
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
        // Get exchange connections
        $connections = Auth::user()->connections;

        $decryptedConnections = $connections->map(function ($item, $key) {
            $item->api = decrypt($item->api);
            $item->secret = decrypt($item->secret);
            return $item;
        });

        // CCTX Exchange list
        $exchangesAvailable = \ccxt\Exchange::$exchanges;


        return view('connections', ['exchanges' => json_encode($exchangesAvailable), 'connections' => $decryptedConnections]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
        // Validate form
            $validatedData = $request->validate([
                'new_exchange' => 'required',
                'new_exchange_api_key' => 'required',
                'new_exchange_api_secret' => 'required'
            ]);

            $user = Auth::user();

            $existingConnections = $user->connections->pluck('exchange');
            if (in_array($request->new_exchange, $existingConnections->all())) {
                // SESSION FLASH: New Trade
                $request->session()->flash('status-text', 'Exchange ' . ucfirst($request->new_exchange) . ' already exists!');
                $request->session()->flash('status-class', 'alert');
            }
            else {
                $connection = new Connection;
                $connection->user_id = $user->id;
                $connection->exchange = $request->new_exchange;
                $connection->api = encrypt($request->new_exchange_api_key);
                $connection->secret = encrypt($request->new_exchange_api_secret);
                $request->new_exchange_fee ? $connection->fee = $request->new_exchange_fee : $connection->fee = 0;
                $connection->active = true;
                $connection->save();

                // SESSION FLASH: New Trade
                $request->session()->flash('status-text', 'Exchange ' . ucfirst($connection->exchange) . ' added!');
                $request->session()->flash('status-class', 'success');
            }


            

            return redirect('/connections');
        
        } catch (Exception $e) {
    
            return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');
            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $user = Auth::user();
            $connections = $user->connections;

            $connection = $connections->find($id);
            Connection::destroy($connection->id);

            return response("OK", 200)->header('Content-Type', 'text/plain');
        
        } catch (Exception $e) {
    
            return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');
            
        }
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
        try {

            // Validate form
            $validatedData = $request->validate([
                'new_exchange_api_key' => 'required',
                'new_exchange_api_secret' => 'required'
            ]);

            $user = Auth::user();
            $connections = $user->connections;

            $connection = $connections->find($id);
            $connection->api = encrypt($request->new_exchange_api_key);
            $connection->secret = encrypt($request->new_exchange_api_secret);
            $request->new_exchange_fee ? $connection->fee = $request->new_exchange_fee : $connection->fee = 0;
            $connection->save();

            return response("OK", 200)->header('Content-Type', 'text/plain');
        
        } catch (Exception $e) {
    
            return response($e->getMessage(), 500)->header('Content-Type', 'text/plain');
            
        }
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



    
}
