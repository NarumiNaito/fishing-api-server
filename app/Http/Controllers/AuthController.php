<?php

namespace App\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function User()
    {
        
        $user_id = Auth::id(); 
        $users = User::where('id', $user_id)
        ->select('id','name','email','password','image','created_at','updated_at')
        ->get();    

        return response()->json($users);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!Auth::guard('user')->attempt($credentials)) {
            throw new AuthenticationException('ログインに失敗しました。');
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'ログインしました。'
        ]);
    }

    public function register(RegisterRequest $request)
    {
        
        $existsEmail = User::where('email', $request->email)->exists();

        if ($existsEmail) {
            return response()->json([
                'message' => 'メールアドレスがすでに登録されています。'
            ],410
        );
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('user')->login($user);

        return response()->json([
            'message' => 'ユーザ登録が完了しました。',
        ]);
        
    }

}
