<?php

namespace App;
class UserTrade extends BaseModel
{
    protected $fillable=[
        "trade_id","user_id_maker","user_id_taker","type","price","amount","market_id"
    ];

}
