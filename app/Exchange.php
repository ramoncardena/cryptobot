<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'url', 'fee'
    ];
}
