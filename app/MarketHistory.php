<?php

namespace App;
class MarketHistory extends BaseModel
{
    protected $fillable=[
      'market_time','high','low','close','open','market_id','trade_count','volume','volume_base'
    ];
    public function toArray(){
        return array($this->market_time,floatval($this->open),floatval($this->high),floatval($this->low),floatval($this->close),$this->trade_count,$this->volume_base);
    }
}
