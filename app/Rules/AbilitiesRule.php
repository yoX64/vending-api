<?php

namespace App\Rules;

use App\Providers\AuthServiceProvider;
use Illuminate\Contracts\Validation\Rule;

class AbilitiesRule implements Rule
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
    public function passes($attribute, $value): bool
    {
        return collect(json_decode($value))->filter(function ($ability) {
            return !in_array($ability, [AuthServiceProvider::ABILITY_BUY]);
        })->isEmpty();
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'The ability provided is not supported.';
    }
}
