<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stop extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trade_id', 'order_id', 'exchange', 'pair', 'price', 'ammount', 'type'
    ];
}
