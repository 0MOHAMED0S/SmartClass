<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = ['room_id', 'attendance_id', 'student_id', 'status', 'created_at', 'updated_at'];
        public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function attendance()
{
    return $this->belongsTo(Attendance::class, 'attendance_id');
}

}
