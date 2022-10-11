<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->all();
        
        $user = User::query()->create($data);

        $response = [
            "user" => $user
        ];

        return response()->json($response, 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required"]
        ]);

        if (Auth::attempt($credentials))
        {
            $token = $request->user()->createToken("authToken");

            $response = [
                "token" => $token->plainTextToken,
                "user" => $request->user()
            ];

            return response()->json($response, 200);
        }
        else
        {
            dd("login failed");
        }
    }

    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([], 204);
    }
}