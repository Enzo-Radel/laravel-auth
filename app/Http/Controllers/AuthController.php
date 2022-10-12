<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->all();
        
        $user = User::query()->create($data);

        if (!is_null($user)) {
            event(new Registered($user));
        }

        $token = $user->createToken("authToken");


        $response = [
            "user" => $user,
            "token" => $token->plainTextToken
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

    public function verifyEmail(Request $request)
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            $response = [
                "message"   => "usuário já verificado"
            ];
            return response()->json($response, 402);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $response = [
            "message"   => "usuário verificado com sucesso"
        ];

        return response()->json($response, 204);
    }

    public function sendEmailVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
    }
}