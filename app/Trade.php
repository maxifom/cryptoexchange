<?php

namespace App;

class Trade extends BaseModel
{
    protected $fillable=[
        "user_id","market_id","type","amount","price","fee","fee_sub"
    ];
    protected $hidden=[
        "user_id_taker","user_id"
    ];
    public function toApi()
    {
        return [
            'id'=>$this->id,
            'amount'=>floatval(number_format($this->amount,8,'.','')),
            'price'=>floatval(number_format($this->price,8,'.','')),
            'type'=>$this->type,
            'market'=>$this->market,
            'market_id'=>$this->market_id
        ];
    }
}
