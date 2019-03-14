<?php

namespace App;
class Market extends BaseModel
{
    protected $hidden=[
        'base_coin','trade_coin','created_at','updated_at'
    ];
    public function base_coin()
    {
        return $this->belongsTo("App\Coin","base_currency_id");
    }
    public function trade_coin()
    {
        return $this->belongsTo("App\Coin","trade_currency_id");
    }
    public function trades()
    {
        return $this->hasMany('App\Trade');
    }

}
