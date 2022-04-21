<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'nickname',
        'phone',
        'avatar',
        'birthdate',
        'bio',
        'online',
        'last_seen',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $appends = [
        'last_message',
        'unread',
        'typing'
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthdate' => 'date:Y-m-d',
        'last_seen' => 'datetime:Y-m-d H:i:s',
        'online' => 'boolean'
    ];

    public function getTypingAttribute()
    {
        return false;
    }

    public function getLastSeentAttribute($value)
    {
        return Carbon::parse($value)->timezone('Europe/Kiev')->toDateTimeString();
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function getAvatarAttribute($value)
    {
        return Storage::url($value);
    }

    public function getUnreadAttribute($value)
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user) {
            return count(Message::where('recipient', $user->id)
                ->where('sender', $this->id)
                ->where('read', false)->get());
        }
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender', 'id');
    }

    public function getLastMessageAttribute()
    {
        if (auth()->check()) {
            /** @var User $user */
            $user = auth()->user();
            $id = $this->id;
            if ($id != $user->id) {
                return Message::with('attachments')->where(function ($query) use ($id, $user) {
                    $query->where('sender', $user->id);
                    $query->where('recipient', $id);
                })->orWhere(function ($query) use ($id, $user) {
                    $query->where('recipient', $user->id);
                    $query->where('sender', $id);
                })->orderBy('created_at', 'desc')->first();
            }
        }
    }

    public function scopeSearch(Builder $builder, $value)
    {
        /** @var User $user */
        $user = auth()->user();
        return $builder->where('id', '!=', $user->id)
            ->where('firstname',  "like", "%$value%")
            ->orWhere('lastname',  "like", "%$value%")
            ->orWhere('nickname',  "like", "%$value%")
            ->orWhere('email',  "like", "%$value%")
            ->orWhere('phone',  "like", "%$value%");
    }
}
