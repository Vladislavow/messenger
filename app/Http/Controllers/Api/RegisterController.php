<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedFields = $request->validated();
        $user = User::create($validatedFields);
        $token = $user->createToken($user->email)->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }
}
