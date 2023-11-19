<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('username', $request->username)->first();

        if (Hash::check($request->password, $user->password)) {
            $token = JWTAuth::fromUser($user, ['exp' => now()->addMinutes(1)->timestamp]);
            $data = [
                'token' => $token,
                'user' => $user
            ];
            return response()->json(['message' => 'Login success..', 'data' => $data], 200);
        }

        return response()->json(['message' => 'Login error..'], 500);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = new User();

        if ($user::where('username', $request->username)->orWhere('email', $request->email)->first()) {
            return response()->json(['message' => 'The username or email is already taken.'], 422);
        }

        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->save();

        return response()->json(['status' => true, 'message' => 'create user success.']);
    }
}
