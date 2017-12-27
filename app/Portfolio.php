<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    public function assets()
    {
        return $this->hasMany('App\Asset');
    }

    public function origins()
    {
        return $this->hasMany('App\Origin');
    }

}
