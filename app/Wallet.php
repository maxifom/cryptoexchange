<?php

namespace App;
class Wallet extends BaseModel
{
    protected $fillable=[
        "coin_id","user_id"
    ];
    protected $hidden = [
        "coin_id","coin","created_at","updated_at","user_id"
    ];
    public function coin()
    {
        return $this->belongsTo('App\Coin');
    }
    public function deposits()
    {
        return $this->hasMany("App\Deposit");
    }
    public function withdrawals()
    {
        return $this->hasMany("App\Withdrawal");
    }
}
