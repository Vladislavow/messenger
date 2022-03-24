<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatUpdated;
use App\Events\ChatUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Jobs\SendAllContacts;
use App\Models\User;
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
        $token = $user->createToken($user->email)->plainTextToken;
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
            $user->tokens()->delete();
            return response()->json('Logged out', 200);
        } else {
            return redirect('/login');
        }
    }

    public function updateUser(UpdateUserRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $validatedFields = $request->validated();
        $user->update($validatedFields);
        $user->save();
        (new SendAllContacts(ChatUpdated::class, $user))->handle();
        return response()->json('Profile updated', 200);
    }

    public function updateAvatar(UpdateUserAvatarRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $validatedFields = $request->validated();
        $user->avatar =  $request->avatar->store('/', 'public');
        $user->save();
        (new SendAllContacts(ChatUpdated::class, $user))->handle();
        return response()->json('Avatar updated', 200);
    }
}
