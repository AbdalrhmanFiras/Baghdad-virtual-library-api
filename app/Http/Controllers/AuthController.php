<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\UserAuthResource;
use App\Http\Requests\UserRgisterRequest;
/**
 * @tags Auth Endpoint
 */
class AuthController extends Controller
{
    /**
     * User Register 
     */
    public function register(UserRgisterRequest $request)
        {
            $data = $request->validated();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);
            $token = JWTAuth::fromUser($user);
   
                 return $this->responseSuccess(
                    ['user' => new UserAuthResource($user),'token' => $token],'User Registered successfully',201);
        }
     /**
     * User Login 
     */
    public function login(UserLoginRequest $request)
    {
        $credentials = $request->validated();

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return $this->responseSuccess(['user' => new UserAuthResource(auth()->user()),'token' => $token]
                 ,'logged in',200);
    }
    
    /**
     * User Logout 
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->responseSuccess(null
                 ,'Logged out',200);
        
    }
     /**
     * User Token(me) 
     */
    public function me()
    {
    
        return response()->json(auth()->user());
    }
}
