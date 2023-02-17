<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|unique:users|email',
                'password' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            $response = [
                'access_token' => $user->createToken($user->email)->accessToken,
                'user' => $user
            ];

            return response()->json([
                'status' => true, 
                'message' => 'Register Success',
                'data' => $response
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false,'message' => $e->getMessage()], 500);
        }
    }
}
