<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PortfolioAsset extends Model
{
    protected  $primaryKey = 'id';
    
	protected $fillable = [
        'user_id', 'update_id', 'initial_price'
    ];

    public function portfolio()
    {
        return $this->belongsTo('App\Portfolio');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }
}
