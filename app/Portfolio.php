<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected  $primaryKey = 'id';
    
	protected $fillable = [
        'user_id', 'balance', 'counter_value', 'name', 'assets_count', 'update_id'
    ];
    
    public function assets()
    {
        return $this->hasMany('App\PortfolioAsset');
    }

    public function origins()
    {
        return $this->hasMany('App\PortfolioOrigin');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    
}
