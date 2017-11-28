<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected  $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'order_id', 'trade_id', 'exchange'
    ];
}
