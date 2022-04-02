<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'original_name',
        'path',
        'extension'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id', 'id');
    }
}
