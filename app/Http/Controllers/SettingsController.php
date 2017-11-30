<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\Handler;
use App\Library\Services\Facades\Bittrex;

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
        $settings = settings();
         // $settings->set('bittrex_key','a8sd98as8ajlk3lkj4h88snal');
         // $settings->set('bittrex_secret','k90welf90af455wgfm_a3kng9');

        if ($settings->get('bittrex_key')!="") {
            $bittrex_key = $settings->get('bittrex_key');
        }
        
        if ($settings->get('bittrex_secret')!="") {
            $bittrex_secret =$settings->get('bittrex_secret');
        }

        return view('settings', ['settings' => $settings->all(), 'decrypt_bittrex_key' => $bittrex_key, 'decrypt_bittrex_secret' => $bittrex_secret]);
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

        foreach ($request->all() as $key => $value) 
        {
            if ($key !== "_token") {
                $settings->set($key, $value);
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
