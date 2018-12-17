<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
	protected  $primaryKey = 'id';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'order_id', 'stop_id', 'profit_id', 'status', 'exchange', 'pair', 'price', 'ammount', 'total', 'stop_loss', 'take_profit', 'closing_price', 'condition_id', 'condition', 'condition_price'
    ];

    public function order()
    {
        return $this->hasOne('App\Order');
    }

}
