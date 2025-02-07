<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatesTravelOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'city'              => 'required|min:2|max:70',
            'state'             => 'required|min:2|max:50',
            'country'           => 'required|min:2|max:60',
            'departure_date'    => 'required|date|after:tomorrow',
            'return_date'       => 'required|date|after:tomorrow',
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            //
        ];
    }
}
