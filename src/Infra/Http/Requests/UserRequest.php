<?php

namespace Transaction\Infra\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Transaction\Application\StoreUser\User as UserValueObject;

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
            'registration_number' => 'required|string|fiscal_doc|unique:users,registration_number,id',
            'type' => 'required|string|in:regular,seller',
            'password' => 'required|min:6',
        ];
    }

    public function getUserValueObject(): UserValueObject
    {
        return new UserValueObject($this->all());
    }
}
