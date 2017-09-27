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

		return view('settings', ['settings' => $settings->all()]);
	}
  }
