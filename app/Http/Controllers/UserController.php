<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * Login by jwt via credentials
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $validator = Validator::make(request(['email', 'password']), $this->rules());
        if($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'message' => $validator->messages(),
            ], 400);
        }
        $credentials = request(['email', 'password']);
        $token = auth('users')->attempt($credentials);

        if (!$token) {
            return response()->json([
                'is_success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'is_success' => true,
            'token_type' => 'bearer',
            'access_token' => $token
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('users')->logout();

        return response()->json(['is_success' => true, 'message' => 'Successfully logged out']);
    }

    /**
     * Register a user
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json(['is_success' => false, 'error' => $validator->errors()], 401);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([ 
            'is_success' => true,
            'user' => $user,
            'token_type' => 'bearer',
            'access_token' => $token,
        ], 201);
    }

    /** 
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json([
            'is_success' => true, 
            'data' => auth()->user(),
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function rules() {
        return [
            'email' => 'required|max:255',
            'password' => 'required|max:255',
        ];
    }
}