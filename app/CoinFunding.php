<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoinFunding extends Model
{
    protected $fillable = [
        'coin_id', 'address', 'needed_amount', 'amount', 'needed_confirmations', 'funding_coin_id'
    ];

    public function coin()
    {
        return $this->belongsTo("App\Coin", 'coin_id');
    }

    public function funding_coin()
    {
        return $this->belongsTo("App\Coin", 'funding_coin_id');
    }
}
