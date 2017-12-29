<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PortfolioAsset extends Model
{
    public function portfolio()
    {
        return $this->belongsTo('App\Portfolio');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
