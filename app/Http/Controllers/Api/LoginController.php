<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Jobs\SendAllContacts;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validatedFields = $request->validated();
        $user = User::where('email', $validatedFields['email'])->first();

        if (!$user || !Hash::check($validatedFields['password'], $user->password)) {
            return response()->json('Invalid credentials', 404);
        }

        $token = $user->createToken($request->browser)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function logout()
    {
        if (auth()->check()) {
            /** @var User $user  */
            $user =  auth()->user();

            $user->online = false;
            $user->last_seen = Carbon::now();
            $user->currentAccessToken()->delete();
            $user->save();


            return response()->json('Logged out', 200);
        } else {
            return redirect('/login');
        }
    }
}
