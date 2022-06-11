<?php

namespace Transaction\Infra\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,id',
            'registration_number' => 'required|string|registration_number|unique:users,registration_number,id',
            'type' => 'required|string|in:regular,seller',
            'password' => 'required|min:6',
        ];
    }
}
