<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserIp extends BaseModel
{
    protected $fillable=['user_id','ip'];
}
