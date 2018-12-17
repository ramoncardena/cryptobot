<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conditional extends Model
{
	protected  $primaryKey = 'id';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'trade_id', 'exchange', 'pair', 'condition', 'condition_price'
    ];
}
