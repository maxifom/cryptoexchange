<?php

namespace App;

class Withdrawal extends BaseModel
{
    protected $hidden = [
      "wallet","wallet_id","updated_at"
    ];
    protected $fillable =[
        "wallet_id",'value','address','status'
    ];
    public function wallet()
    {
        return $this->belongsTo('App\Wallet');
    }
}
