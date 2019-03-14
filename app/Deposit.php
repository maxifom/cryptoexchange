<?php

namespace App;
class Deposit extends BaseModel
{
    protected $hidden = [
        "updated_at","wallet_id","created_at","wallet"
    ];
    protected $fillable = [
        "tx","value","wallet_id","tx_time","confirmations"
    ];
    protected $formattedDates = ['created_at','updated_at'];
    public function wallet()
    {
        return $this->belongsTo("App\Wallet");
    }
}
