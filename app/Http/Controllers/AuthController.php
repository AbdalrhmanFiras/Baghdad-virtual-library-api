<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRgisterRequest;

class AuthController extends Controller
{
    public function register(UserRgisterRequest $request)
        {
            $data = $request->validated();
            $user = \App\Models\User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Registered',
                'user'    => $user,
                'token'   => $token
            ], 201);
        }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->validated();

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Logged in',
            'token' => $token,
            'user' => auth()->user()
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logged out']);
    }

    public function me()
    {
    
        return response()->json(auth()->user());
    }
}
