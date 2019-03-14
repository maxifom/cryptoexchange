<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/*
 * @property integer id
 * @property integer api_id
 * @property integer api_ip
 * @property string created_at
 * @property string updated_at
 */
class ApiIp extends Model
{
    protected $fillable=[
        'api_ip',
        'api_id'
    ];
}
