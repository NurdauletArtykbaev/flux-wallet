<?php

namespace Nurdaulet\FluxWallet\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class EpayPayPageRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'required',
            'amount' => 'required',
        ];
    }
}
