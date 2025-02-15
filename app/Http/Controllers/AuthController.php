<?php

namespace App\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function User()
    {
        
        $user_id = Auth::id(); 
        $users = User::where('id', $user_id)
        ->select('id','name','email','image','password','created_at','updated_at')
        ->get();    

        return response()->json($users);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!Auth::guard('user')->attempt($credentials)) {
            throw new AuthenticationException('ログインに失敗しました。入力内容を確認して下さい。');
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'ログインしました。'
        ])->cookie('user_session', "user_session", 120);
    }

    public function register(RegisterRequest $request)
    {
        
        $existsEmail = User::where('email', $request->email)->exists();

        if ($existsEmail) {
            return response()->json([
                'message' => 'メールアドレスがすでに登録されています。'
            ]
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
        ])->cookie('user_session', "user_session", 120);
    }

    public function update(UpdateRequest $request)
        {
            $profile = User::find($request->id);
    
            if (is_null($profile)) {
                return response()->json([
                    'message' => '更新対象のプロフィールが存在しません。'
                ]);
            }
    
            $oldImage = $profile->image;
    
            if (is_null($request->image)) {
                $profile->update([
                    'name' => $request->name,
                    'image' => null,
                ]);
    
                $oldImage && Storage::disk('s3')->delete($oldImage);
        
                return response()->json([
                    'message' => 'プロフィール情報を更新しました。'
                ]);
            }
    
            $extension = $request->image->extension();
            $fileName = Str::uuid().'.'.$extension;
    
            $uploadedFilePath = Storage::disk('s3')->putFile('images', $request->image, $fileName);
    
            $profile->update([
                'name' => $request->name,
                'image' => $uploadedFilePath,
            ]);
    
            $oldImage && Storage::disk('s3')->delete($oldImage);
    
            return response()->json([
                'message' => 'プロフィール情報を更新しました。'
            ]);
        }
    

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Cookie::queue(Cookie::forget('user_session'));

        return response()->json([
            'message' => 'ログアウトしました。',
        ]);
    }

}
