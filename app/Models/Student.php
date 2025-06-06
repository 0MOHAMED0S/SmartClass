<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code','section','room_id'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id', 'id');
    }
}
