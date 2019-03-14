<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TradingFee extends Model
{
    protected $fillable=[
      'coin_id','fee'
    ];
}
