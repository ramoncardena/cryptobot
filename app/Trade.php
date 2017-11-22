<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'status', 'exchange', 'pair', 'price', 'ammount', 'total', 'stop_loss', 'take_profit'
    ];
}
