<?php

namespace App;
use App\Traits\HasLocalDates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable
{
    use Notifiable,HasLocalDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','timezone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','admin','notifications_enabled'
    ];
    protected $guarded = ['admin'];
    public function wallets()
    {
        return $this->hasMany("App\Wallet");
    }
    public function ips()
    {
        return $this->hasMany("App\UserIp");
    }
    public static function table()
    {
        $instance = new static;
        return $instance->getTable();
    }
    public function passwordSecurity()
    {
        return $this->hasOne('App\PasswordSecurity');
    }
    public function anticode()
    {
        return $this->hasOne('App\AntiPhishingCode');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function api()
    {
        return $this->hasMany("App\ApiEntry");
    }
    public function deposits()
    {
        return $this->hasMany('App\Deposit');
    }
}
