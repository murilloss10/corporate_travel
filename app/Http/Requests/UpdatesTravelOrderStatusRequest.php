<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatesTravelOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->tokenCan('admin-permission');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:Aprovado,Cancelado'
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'status.required'   => 'O status é obrigatório.',
            'status.in'         => "O status deve ser 'Aprovado' ou 'Cancelado'."
        ];
    }
}
