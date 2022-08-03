<?php

namespace App\Http\Requests;

use App\Rules\DividentOfFiveRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductsUpdateRequest extends FormRequest
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
            'name' => ['sometimes', 'string','max:255'],
            'cost' => ['sometimes', 'numeric', 'min:0', new DividentOfFiveRule()],
            'amount_available' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
