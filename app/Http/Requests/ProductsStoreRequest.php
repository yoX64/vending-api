<?php

namespace App\Http\Requests;

use App\Rules\DividentOfFiveRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductsStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cost' => ['required', 'numeric', 'min:0', new DividentOfFiveRule()],
            'amount_available' => ['required', 'numeric', 'min:0'],
        ];
    }
}
