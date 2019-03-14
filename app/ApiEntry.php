<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
/*
 * @property integer id
 * @property integer user_id
 * @property string token
 * @property string created_at
 * @property string updated_at
 * @property boolean trade
 * @property boolean wallet
 */
class ApiEntry extends Model
{
    protected $fillable=[
      'user_id',
      'wallet',
      'trade',
      'token'
    ];
    public function ips()
    {
        return $this->hasMany("App\ApiIp",'api_id');
    }
}
