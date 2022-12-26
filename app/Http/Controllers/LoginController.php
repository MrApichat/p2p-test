<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


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
            return response()->json(['token' => explode('|',$token->plainTextToken)[1]]);
        }
    }
}
