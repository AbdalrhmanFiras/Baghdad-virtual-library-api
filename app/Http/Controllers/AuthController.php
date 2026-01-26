<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRgisterRequest;
use App\Http\Resources\UserAuthResource;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        $telegram = new TelegramService;
        $message = "🆕 مستخدم جديد سجل في الموقع!\n";
        $message .= "👤 الاسم: {$user->name}\n";
        $message .= "📧 البريد: {$user->email}";
        $telegram->sendMessage($message);
        $token = JWTAuth::fromUser($user);

        return $this->responseSuccess(
            ['user' => new UserAuthResource($user), 'token' => $token], 'User Registered successfully', 201);
    }

    /**
     * User Login
     */
    public function login(UserLoginRequest $request)
    {

        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();
        if ($user && $user->auth_provider === 'google') {
            return response()->json([
                'message' => 'This account is registered with Google. Use Google login.',
            ], 403);
        }
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return $this->responseSuccess(['user' => new UserAuthResource(auth()->user()), 'token' => $token], 'logged in', 200);
    }

    /**
     * User Logout
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->responseSuccess(null, 'Logged out', 200);

    }

    /**
     * User Token(me)
     */
    public function me()
    {

        return response()->json(auth()->user());
    }

    /**
     * Login with Google
     */
    public function google(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $googleUser = Socialite::driver('google')
            ->stateless()
            ->userFromToken($request->id_token);

        $user = User::where('google_id', $googleUser->id)
            ->orWhere('email', $googleUser->email)
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'auth_provider' => 'google',
                'password' => null,
            ]);
        } elseif (! $user->google_id) {
            $user->update([
                'google_id' => $googleUser->id,
                'auth_provider' => 'google',
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => new UserAuthResource($user),
            'token' => $token,
        ]);
    }
}
