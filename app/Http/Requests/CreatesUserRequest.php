<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatesUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'      => 'required|min:2|max:100',
            'email'     => 'required|min:5|max:255|email|unique:users',
            'password'  => 'required|confirmed|min:8|max:255',
            'role'      => 'in:user,admin',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'O nome é obrigatório.',
            'email.required'        => 'O e-mail é obrigatório.',
            'password.required'     => 'A senha é obrigatória.',
            'name.min'              => 'O nome deve ter no mínimo :min caracteres.',
            'email.min'             => 'O e-mail deve ter no mínimo :min caracteres.',
            'password.min'          => 'A senha deve ter no mínimo :min caracteres.',
            'name.max'              => 'O nome deve ter no máximo :max caracteres.',
            'email.max'             => 'O e-mail deve ter no máximo :max caracteres.',
            'password.max'          => 'A senha deve ter no máximo :max caracteres',
            'email.email'           => 'Informe um e-mail válido.',
            'email.unique'          => 'Este e-mail já está sendo utilizado.',
            'password.confirmed'    => 'As senhas não coincidem.',
            'role.in'               => 'A função deve ser "user" ou "admin".',
        ];
    }
}
