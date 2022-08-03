<?php

namespace App\Http\Requests;

use App\Rules\DividentOfFiveRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * Handle a failed validation attempt.
     *
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(
            ['error' => $validator->errors()->first()],
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
