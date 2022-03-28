<?php

namespace App\Models;

use Attribute;
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

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'message_id', 'id');
    }
}
