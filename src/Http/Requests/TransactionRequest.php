<?php

namespace Nurdaulet\FluxWallet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'from_date' => 'nullable',
            'to_date' => 'nullable'
        ];
    }
}
