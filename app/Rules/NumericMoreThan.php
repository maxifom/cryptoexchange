<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NumericMoreThan implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($max)
    {
        $this->max=$max;
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
        if (floatval($value)<=$this->max)
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
        return 'Value is more than maximum amount';
    }
}
