<?php

namespace User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use User\Store\User as UserValueObject;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,id',
            'fiscal_doc' => 'required|string|fiscal_doc|unique:users,fiscal_doc,id',
            'type' => 'required|string|in:regular,seller',
            'password' => 'required|min:6',
        ];
    }

    public function getUserValueObject(): UserValueObject
    {
        return new UserValueObject($this->all());
    }
}
