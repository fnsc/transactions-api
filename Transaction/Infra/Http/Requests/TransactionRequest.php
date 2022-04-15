<?php

namespace Transaction\Infra\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'payee_id' => 'required|integer|exists:users,id',
            'payer_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric',
        ];
    }
}
