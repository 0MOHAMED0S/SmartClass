<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectDoctor extends Model
{
    protected $fillable = ['room_id', 'subject_id', 'room_user_id'];
    public function roomUser()
    {
        return $this->belongsTo(RoomUser::class);
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, RoomUser::class, 'id', 'id', 'room_user_id', 'user_id');
    }
}
