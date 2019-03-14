<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawalConfirmation extends BaseModel
{
    protected $fillable=[
      'withdrawal_id','token'
    ];
}
