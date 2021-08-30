<?php

namespace App\Http\Validators;

use Illuminate\Support\Facades\Validator;

class UserApiValidator
{

    public static function register($inputs)
    {
        $messages = [
            'name.required' => 'name is required',
            'email.required' => 'email is required',
            'email.email' => 'email must be valid',
            'email.unique' => 'email is already taken',
            'password.required' => 'password is required',
        ];

        return Validator::make($inputs, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ], $messages);
    }

    public static function login($inputs)
    {
        $messages = [
            'email.required' => 'email is required',
            'password.required' => 'password is required',
        ];

        return Validator::make($inputs, [
            'email' => 'required',
            'password' => 'required',
        ], $messages);
    }

}
