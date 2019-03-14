<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends BaseModel
{
    protected $fillable=[
        'user_id','subject','type'
    ];
}
