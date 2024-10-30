<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            throw new UnauthorizedException('Invalid login credentials');
        }

        $user = Auth::user();
        $token = $user->createToken('AuthToken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => ['token' => $token],
        ], 200);
    }
}
