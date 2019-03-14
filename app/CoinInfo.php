<?php

namespace App;

class CoinInfo extends BaseModel
{
    protected $dates=['block_time'];
    public function coin()
    {
        return $this->belongsTo("App\Coin");
    }
}
