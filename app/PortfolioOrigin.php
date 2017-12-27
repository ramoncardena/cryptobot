<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PortfolioOrigin extends Model
{
    public function portfolio()
    {
        return $this->belongsTo('App\Portfolio');
    }
}
