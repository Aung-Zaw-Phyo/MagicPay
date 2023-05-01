<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferFormValidatioin extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'to_phone' => ['required'],
            'amount' => ['required', 'integer'],
            'hash_value' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'to_phone.required' => "Please fill the accepter's phone number.",
            'amount.required' => 'Please fill the amount field.',
            'hash_value.required' => 'The given data is invalid.'
        ];
    }
}
