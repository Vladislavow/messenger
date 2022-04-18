<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Jobs\SendAllContacts;
use Illuminate\Http\Request;
use App\Events\ChatUpdated;
use App\Http\Requests\OnlineStatusRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
{
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
        $user->avatar =  $request->avatar->store('/avatars', 'public');
        $user->save();
        (new SendAllContacts(ChatUpdated::class, $user))->handle();
        return response()->json('Avatar updated', 200);
    }

    public function setOnlineStatus(OnlineStatusRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $validatedfields = $request->validated();
        $user->online = $validatedfields['status'];
        $user->last_seen = $validatedfields['status'] == true ? $user->last_seen : Carbon::now();
        $user->save();
        return response()->json('Status changed', 200);
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json('User not found', 404);
        }
        $status = Password::sendResetLink($request->only('email'));
        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['status' => __($status)]);
        }
        return response()->json('1' . $status);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password
                ]);
                $user->save();
                $user->tokens()->delete();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json('Password updated')
            : response()->json(['email' => [__($status)]]);
    }
}
