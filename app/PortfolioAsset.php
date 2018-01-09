<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PortfolioAsset extends Model
{
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
}
