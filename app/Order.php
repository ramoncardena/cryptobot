<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected  $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'order_id', 'trade_id', 'exchange', 'type', 'cancel'
    ];

    public function trade()
    {
        return $this->belongsTo('App\Trade');
    }
    
}
