<?php

namespace Doonamis\Auth\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Doonamis\Auth\Application\Request\LoginRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = [
            'email' => $request->email(),
            'password' => $request->password(),
        ];
  
        if (! $token = Auth::attempt($credentials)) {
            throw new AuthenticationException('Credenciales incorrectas');
        }
  
        return response()->json([
            'access_token' => $token,
        ]);
    }
}