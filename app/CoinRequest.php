<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoinRequest extends Model
{
    protected $fillable=[
        'name',
        'source',
        "block_explorer",
        'announcement',
        'type',
        'needed_confirmations',
        'user_id'
    ];
}
