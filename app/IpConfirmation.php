<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IpConfirmation extends BaseModel
{
    protected $fillable=['user_id','ip','token'];
}
