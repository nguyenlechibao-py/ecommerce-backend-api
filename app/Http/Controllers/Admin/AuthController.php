<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
                'isSuccess' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'isSuccess' => true,
            'token_type' => 'bearer',
            'access_token' => $token
        ]);
    }
}
