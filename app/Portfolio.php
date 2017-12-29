<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
	protected $fillable = [
        'user_id', 'balance', 'counter_value', 'name'
    ];
    
    public function assets()
    {
        return $this->hasMany('App\PortfolioAsset');
    }

    public function origins()
    {
        return $this->hasMany('App\PortfolioOrigin');
    }

    
}
