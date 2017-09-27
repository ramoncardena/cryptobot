<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

		$bittrex_key = $settings->get('bittrex_key');
		$bittrex_secret = $settings->get('bittrex_secret');

		return view('settings', ['settings' => $settings->all()]);
	}
  }
