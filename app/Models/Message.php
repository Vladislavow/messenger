<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender',
        'recipient',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function sender()
    {
        return $this->hasOne(User::class, 'id', 'sender');
    }

    public function recipient()
    {
        return $this->hasOne(User::class, 'id', 'recipient');
    }
}
