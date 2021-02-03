<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequestsValid extends FormRequest
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
            'text_request' => 'required|min:15|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'text_request.required' => 'введите текст Предложение к проекту!',
        ];
    }
}
