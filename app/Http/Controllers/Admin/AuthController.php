<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Login by jwt via credentials
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        $token = auth('admins')->attempt($credentials);

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
     * Log the admin out (Invalidate the token).
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('admins')->logout();

        return response()->json(['is_success' => true, 'message' => 'Successfully logged out']);
    }

    /**
     * Register an admin
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

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = JWTAuth::fromUser($admin);

        return response()->json(compact('admin', 'token'), 201);
    }

    /** 
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json(auth()->user());
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
}
