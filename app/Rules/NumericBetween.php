<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NumericBetween implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($min,$max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (floatval($value)>=$this->min && floatval($value)<=$this->max)
        return true;
        else return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Value is less than minimal or more than maximum amount';
    }
}
