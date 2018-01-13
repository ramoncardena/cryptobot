<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    
	protected  $primaryKey = 'id';
    
    protected $fillable = [];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
