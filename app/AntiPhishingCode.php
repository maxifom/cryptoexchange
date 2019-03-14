<?php

namespace App;

/*
 * @property integer id
 * @property integer code
 * @property integer user_id
 * @property string created_at
 * @property string updated_at
 */
class AntiPhishingCode extends BaseModel
{
    protected $fillable=[
      'code','user_id'
    ];
}
