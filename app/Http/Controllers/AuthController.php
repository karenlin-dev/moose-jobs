<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // 如果你想直接保存用户到数据库
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // 注册 API
    public function register(Request $request)
    {
        // 数据验证
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', 'in:worker,employer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // 创建用户
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message'=>'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message'=>'登录成功',
            'token'=>$token,
            'token_type'=>'Bearer'
        ]);
        
        //return response()->json(['access_token'=>$token, 'token_type'=>'Bearer']);

    }
}
