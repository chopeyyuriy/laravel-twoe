<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyAccountValid extends FormRequest
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
            'last_name' => 'required|max:255',
            'login' => 'required|unique:users|max:255',
        ];
    }

    public function messages()
    {
        return [
            'last_name.required' => 'Заполните поле Фамилия !',
            'login.required' => 'Заполните поле Логин !',
            'login.unique' => 'Такой логин уже существует !',
        ];
    }
}
