<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Traits\ApiResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginUserRequest $request)
    {
        // validate
        $request->validated($request->only(['username', 'password']));

        // check credentials
        $credentials = $request->only(['username', 'password']);
        $credentials['isActive'] = 1;

        if(!Auth::attempt($credentials))
        {
            return $this->error('', 'Wrong authentication data', 401);
        }

        // fetch user instance
        $user = User::whereUsername($request->username)->first();
        // delete previous tokens
        $user->tokens()->delete();
        // create new token
        $token = $user->createToken('Login_token_'.$user->username)->plainTextToken;
        // set cokkie
        $cookie = Cookie('Sanctum_Token', $token, 60 * 24);

        //response
        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'User Logged in Successfully')->withCookie($cookie);

    }

    public function register(StoreUserRequest $request)
    {
        // validate
        $request->validated($request->only([
            'first_name', 'last_name', 'username', 'email', 'password'
        ]));

        // create user
        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request['password']),
            ]);
        } catch (\Throwable $th) {
            return $this->error([
                'error' => $th,
            ], 'Something went wrong', 401);
        }

        // response
        if($user)
        {
            return $this->success([
                'user' => $user,
            ], 'User Created Successfully');
        }

    }

    public function logout()
    {
        //delete current access token
        Auth::user()->currentAccessToken()->delete();
        //delete current Sanctum_Token cookie
        $cookie = Cookie::forget('Sanctum_Token');
        // response
        return $this->success('', 'User Logged out successfully')->withCookie($cookie);
    }

}
