<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PortfolioOrigin extends Model
{
	protected  $primaryKey = 'id';
	
    public function portfolio()
    {
        return $this->belongsTo('App\Portfolio');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
