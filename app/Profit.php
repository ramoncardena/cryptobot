<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profit extends Model
{

	protected  $primaryKey = 'id';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trade_id', 'order_id', 'exchange', 'pair', 'price', 'ammount', 'type'
    ];
}
