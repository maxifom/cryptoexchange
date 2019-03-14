<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailConfirmation extends BaseModel
{
    protected $fillable=[
        'user_id','token'
    ];
}
