<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected  $primaryKey = 'id';
    
	protected $fillable = [
        'amount', 'label'
    ];

    public function asset()
    {
        return $this->belongsTo('App\ProtfolioAsset');
    }

    public function portfolio()
    {
        return $this->belongsTo('App\Protfolio');
    }

   	public function user()
    {
        return $this->belongsTo('App\User');
    }

}
