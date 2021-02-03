<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserValid extends FormRequest
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
            'first_name' => 'required|max:70',
            'last_name' => 'required|max:70',
            'email' => 'confirmed',
            'password' => 'confirmed',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'введите Имя!',
            'last_name.required' => 'введите Фамилия!',
            'email.required' => 'введите Коректрий пошту!',
            'password.required' => 'введите Коректрий пароль!',
        ];
    }
}
