<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $table = 'notification';


    protected $fillable = [
        'user_id',
        'type',
        'message',
        'notifiable_id',
        'notifiable_type',
        'is_read',
        'icon',
        'title'
    ];


    public function notifiable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
