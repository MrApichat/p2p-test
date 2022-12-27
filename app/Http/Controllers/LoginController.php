<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class LoginController extends Controller
{
    public function get()
    {
        return  response()->json(['message' => 'Please login first'], 401);
    }

    public function post()
    {
        $credential = request()->only(['email', 'password']);

        if (!auth()->validate($credential)) {
            return response()->json(['message' => 'Incorrect email or password'], 401);
        } else {
            $user = User::where('email', $credential['email'])->first();
            $user->tokens()->delete();
            $token = $user->createToken('');
            return response()->json(['token' => explode('|', $token->plainTextToken)[1]]);
        }
    }

    public function register()
    {
        try {
            $validate = request()->validate([
                'name' => ['required'],
                'email' => ['required', 'unique:users'],
                'password' => ['required'],
                'confirm_password' => ['required', 'same:password']
            ]);

            $validate['password'] = bcrypt($validate['password']);
            $user = User::create($validate);
            $token = $user->createToken('');
            return response()->json(['token' => explode('|', $token->plainTextToken)[1]]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => $e->validator->errors()], 422);
        }
    }
}
