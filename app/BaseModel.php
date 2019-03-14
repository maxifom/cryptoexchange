<?php
namespace App;
use App\Traits\FormatsDate;

class BaseModel extends \Illuminate\Database\Eloquent\Model {
    use FormatsDate;
    public static function table()
    {
        $instance = new static;
        return $instance->getTable();
    }

}