<?php

namespace Doonamis\Auth\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login()
    {
        return response()->json(['message' => 'Hello World']);
    }
}