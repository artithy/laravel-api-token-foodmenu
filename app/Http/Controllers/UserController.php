<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\Token;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = UserModel::where('email', $request->email)->first();
        if ($user) {
            return response()->json([
                'message' => 'user already exist',
            ], 400);
        }
        $user = UserModel::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if (!$user) {
            return response()->json([
                'message' => 'user creation failed',
            ]);
        }

        $token_string = bin2hex(random_bytes(32));
        $token = Token::create([
            'token' => $token_string,
            'user_id' => $user->id,
            'is_active' => 1,
        ]);

        return response()->json([
            'message' => 'user created successfully',
            'user' => $user->id,
            'token' => $token,
        ], 201);
    }


    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = UserModel::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'invalid image or password',
            ]);
        }
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'invalid image or password',
            ]);
        }

        $token_string = bin2hex(random_bytes(32));
        $token = Token::create([
            'token' => $token_string,
            'user_id' => $user->id,
            'is_active' => 1,
        ]);

        if (!$token) {
            return response()->json([
                'message' => 'token is not created',
            ]);
        }

        return response()->json([
            'message' => 'Login Successful',
            'user' => $user,
            'token' => $token->token,
        ], 201);
    }


    public function dashboard(Request $request)
    {

        $token = $request->attributes->get('token');


        $user = UserModel::find($token->user_id);

        return response()->json([
            'user' => $user,
        ]);
    }


    public function logout(Request $request)
    {
        $token = $request->attributes->get('token');


        $token->is_active = 0;
        $token->save();

        return response()->json([
            'message' => 'logout successfully',
        ]);
    }
}
