<?php

namespace App;

class Settings 
{
	protected $user;

	protected $settings = [];

	protected $allowed = [];

	public function get($key)
	{
		try {
			$value = array_get($this->settings, $key);

			if ( $key == 'bittrex_key' || $key == 'bittrex_secret') {
	
				if ( $value == "" ) {
	            		return array_get($this->settings, $key);
	            	}
	            	else {
	                	return decrypt(array_get($this->settings, $key));
	                }
			}
			else {
				return array_get($this->settings, $key);
			}
		} catch(\Exception $e) {
				var_dump( $e->getMessage());
		}
	}

	public function set($key, $value)
	{
		if ($key == 'bittrex_key' || $key == 'bittrex_secret') {
			if ( $value == "" ) {
				$this->settings[$key] = $value;
			}
			else {
				$this->settings[$key] = encrypt($value);
			}
		}
		else {
			$this->settings[$key] = $value;
		}

		$this->persist();

	}

	public function has($key) 
	{

		return array_key_exists($key, $this->settings);

	}


	public function all() 
	{

		return $this->settings;

	}

	public function __construct(array $settings, User $user)
	{
		$this->settings = $settings;

		$this->user = $user;
	}

	public function merge(array $attributes)
	{
		$this->settings = array_only(

			$this->settings,

			array_only($attributes,array_keys($this->settings))
		);

		return $this->persis();
	}

	protected function persist()
	{

		return $this->user->update(['settings' => $this->settings]);
	}

	public function __get($key)
	{
		if ($this->has($key)) {
			return $this->get($key);
	    }
		throw new Exception ("The {$key} setting doesn't exist.");
	}

}