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
            '*.required'            => 'O campo :attribute é obrigatório.',
            'name.min'              => 'O campo nome deve ter no mínimo 2 caracteres.',
            'name.max'              => 'O campo nome deve ter no máximo 100 caracteres.',
            'email.min'             => 'O campo :attribute deve ter no mínimo 5 caracteres.',
            'email.max'             => 'O campo :attribute deve ter no máximo 255 caracteres.',
            'email.email'           => 'O campo :attribute deve ser um e-mail válido.',
            'email.unique'          => 'O campo :attribute já está sendo utilizado.',
            'password.confirmed'    => 'As senhas não coincidem.',
            'password.min'          => 'O campo :attribute deve ter no mínimo 8 caracteres',
            'password.max'          => 'O campo :attribute deve ter no máximo 255 caracteres',
            'role.in'               => 'O campo :attribute deve ser "user" ou "admin".',
        ];
    }
}
