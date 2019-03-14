<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoinMeta extends Model
{
    protected $fillable=[
        'source',
        'block_explorer',
        'announcement',
        'type',
        'coin_id',
    ];
}
