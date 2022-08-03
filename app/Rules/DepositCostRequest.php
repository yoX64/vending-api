<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DepositCostRequest implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return $value == 5 || $value == 10 || $value == 20 || $value == 50 || $value == 100;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Cost must be 5, 10, 20, 50 or 100';
    }
}
