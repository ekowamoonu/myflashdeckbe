<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function checkLogin()
    {
        if (Auth::check()) {
            return response()->json([
                "message" => "Already logged in"
            ]);
        }

        return response()->json([
            "message" => "Authentication successful"
        ]);
    }
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed|string|min:6",
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => Arr::flatten($validator->errors()->all())
            ], 401);
        }

        $user = User::create($request->only("name", "email", "password"));
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "token" => $token
        ]);
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            if (Hash::check($credentials['password'], $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    "token" => $token
                ]);
            } else {
                return response()->json([
                    'message' => ['Invalid credentials']
                ], 401);
            }
        } else {
            return response()->json([
                'message' => ['Invalid credentials']
            ], 401);
        }
    }
}
