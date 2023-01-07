<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        //response
        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'User Logged in Successfully');

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
            throw $th;
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
        // response
        return $this->success('', 'User Logged out successfully');
    }

}
