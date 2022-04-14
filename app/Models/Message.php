<?php

namespace App\Models;

use Attribute;
use Carbon\Carbon;
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

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Europe/Kiev')->toDateTimeString();
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'message_id', 'id');
    }
}
