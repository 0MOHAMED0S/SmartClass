<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'room_id',
        'path'
    ];
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function attendanceCards()
    {
        return $this->hasMany(Attendance::class);
    }
    public function students()
    {
        return $this->hasMany(Student::class, 'room_id');
    }
    public function subjectDoctors()
    {
        return $this->hasMany(SubjectDoctor::class);
    }
}
