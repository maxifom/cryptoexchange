<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupportTicketText extends BaseModel
{
    protected $fillable=[
        'ticket_id',
        'text',
        'type'
    ];
}
