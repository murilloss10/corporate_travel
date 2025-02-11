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
        return $this->user()->tokenCan('user-permission');
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
            'departure_date'    => 'required|date|after:today',
            'return_date'       => 'required|date|after_or_equal:departure_date',
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'city.required'             => 'A cidade é obrigatória.',
            'state.required'            => 'O estado é obrigatório.',
            'country.required'          => 'O país é obrigatório.',
            'departure_date.required'   => 'A data de partida é obrigatória.',
            'return_date.required'      => 'A data de retorno é obrigatória.',
            'city.min'                  => 'A cidade deve ter no mínimo :min caracteres.',
            'state.min'                 => 'O estado deve ter no mínimo :min caracteres.',
            'country.min'               => 'O país deve ter no mínimo :min caracteres.',
            'city.max'                  => 'A cidade deve ter no máximo :max caracteres.',
            'state.max'                 => 'O estado deve ter no máximo :max caracteres.',
            'country.max'               => 'O país deve ter no máximo :max caracteres.',
            'departure_date.date'       => 'A data de partida deve ser uma data válida.',
            'return_date.date'          => 'A data de retorno deve ser uma data válida.',
            'departure_date.after'      => 'A data de partida deve ser posterior à data atual.',
            'return_date.after_or_equal'=> 'A data de retorno deve ser igual ou posterior à data de partida.',
        ];
    }
}
