<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Auth;

use App\Portfolio;
use App\User;

class SettingsController extends Controller
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
    * Show the application dashboard.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        // Get settings
        $settings = settings();

        // Decrypt exchange keys
        $bittrex['bittrex_key'] = $settings->get('bittrex_key');
        $bittrex['bittrex_secret'] = $settings->get('bittrex_secret');    
        $bittrex['bittrex_fee'] = $settings->get('bittrex_fee');      

        $bitstamp['bitstamp_key'] = $settings->get('bitstamp_key');
        $bitstamp['bitstamp_secret'] = $settings->get('bitstamp_secret');    
        $bitstamp['bitstamp_fee'] = $settings->get('bitstamp_fee');   

        // Get exchange lists
        $exchanges_active  = $settings->get('exchanges');

        // Get portfolio
        $portfolio = Portfolio::where('user_id', Auth::user()->id)->first();

        return view('settings', ['settings' => $settings->all(), 'bittrex' => $bittrex, 'bitstamp' => $bitstamp, 'exchanges_active' => $exchanges_active, 'portfolio' => $portfolio]);
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
        $settings = settings();

        $settings->set('exchanges', null);

        foreach ($request->all() as $key => $value) 
        {
            switch ($key) {
                case 'bittrex_switch':
                    $settings->addExchange('bittrex');
                    break;

                case 'bitstamp_switch':
                    $settings->addExchange('bitstamp');
                    break;

                case 'initialize_portfolio':
                    $portfolio = new Portfolio;
                    $portfolio->user_id = Auth::user()->id;
                    $portfolio->name = "My Portfolio";
                    $portfolio->counter_value = "eur";
                    $portfolio->balance = 0;
                    $portfolio->save();
                    break;

                default:
                    if ($key !== "_token") {
                        $settings->set($key, $value);
                    }
                    break;
            }

        }
        return redirect('/settings');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($setting)
    {
        $settings = settings();
        return $settings->get($setting);
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
