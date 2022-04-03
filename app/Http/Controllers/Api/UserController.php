<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserAvatarRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Jobs\SendAllContacts;
use Illuminate\Http\Request;
use App\Events\ChatUpdated;
use App\Http\Requests\OnlineStatusRequest;
use Carbon\Carbon;

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
}
