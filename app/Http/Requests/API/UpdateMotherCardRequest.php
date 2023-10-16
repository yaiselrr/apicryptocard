<?php

namespace App\Http\Requests\API;

use App\Models\MotherCard;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateMotherCardRequest extends FormRequest
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
        // return MotherCard::$rules;
        return [
            'id_account' => 'required|integer|unique:mother_cards,id_account',
            'card_number' => 'required|string|unique:mother_cards,card_number|min:16|max:16',
            'balance' => 'required',
            'card_provider_id' => 'required|integer'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors'=>$validator->errors()], 422));
    }
}
