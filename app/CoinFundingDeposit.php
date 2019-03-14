<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoinFundingDeposit extends Model
{
    protected $fillable=[
      'funding_id','tx','value','confirmations'
    ];
}
