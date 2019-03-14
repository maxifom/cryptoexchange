<?php

namespace App;
/*
 * @property integer id
 * @property string name
 * @property string user
 * @property string pass
 * @property integer port
 * @property integer needed_confirmations
 * @property string created_at
 * @property string updated_at
 * @property integer coinbase_maturity
 * @property enum status
 * @property boolean base_coin
 * @property boolean trade_coin
 */
class Coin extends BaseModel
{
    protected $hidden = ["user","pass","port","created_at","updated_at","id",'staus'];
    public function coin_info()
    {
        return $this->hasOne('App\CoinInfo');
    }
    public function fee()
    {
        return $this->hasOne('App\Fee');
    }
    public static function all($columns = ['*'])
    {
        return Coin::where('status','confirmed')->get();
    }
    public function meta ()
    {
        return $this->hasOne('App\CoinMeta');
    }
    public function trading_fee ()
    {
        return $this->hasOne('App\TradingFee');
    }
}
