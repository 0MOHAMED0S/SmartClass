<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['room_id', 'name', 'subject_id'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
