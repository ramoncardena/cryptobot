<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticker extends Model
{
	protected  $primaryKey = 'id';

	protected $fillable = [
        'user_id', 'symbol', 'exchange'
    ];
	
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
