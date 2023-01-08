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
        $credentials = $request->validated();

        $credentials['isActive'] = 1;

        // check credentials
        if(!Auth::attempt($credentials))
        {
            return $this->error('', 'Wrong authentication data', 401);
        }

        // fetch user instance
        $user = User::whereUsername($credentials['username'])->first();
        // delete previous tokens
        $user->tokens()->delete();
        // create new token
        $token = $user->createToken('Login_token_'.$user->username)->plainTextToken;
        // set cookie
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
        $validated = $request->validated();

        // create user
        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
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
        if(Auth::check())
        {
            //delete current access token
            Auth::user()->currentAccessToken()->delete();
            //delete current Sanctum_Token cookie
            $cookie = Cookie::forget('Sanctum_Token');
            // response
            return $this->success('', 'User Logged out successfully')->withCookie($cookie);
        }
    }

}
