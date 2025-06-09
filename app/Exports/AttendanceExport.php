<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\Student;

class AttendanceExport implements FromArray, WithHeadings
{
    protected $roomId;
    protected $subjectId;
    protected $month;
    protected $section;
    protected $attendances;

    public function __construct($roomId, $subjectId, $month = null, $section = [])
    {
        $this->roomId = $roomId;
        $this->subjectId = $subjectId;
        $this->month = $month;
        $this->section = $section;

        $this->attendances = Attendance::where('room_id', $roomId)
            ->where('subject_id', $subjectId)
            ->when($month, function ($query) use ($month) {
                $query->whereMonth('created_at', $month);
            })
            ->orderBy('created_at')
            ->get();
    }


    public function array(): array
    {
        $students = Student::whereHas('attendanceRecords', function ($q) {
            $q->whereIn('attendance_id', $this->attendances->pluck('id'));
        })
            ->when($this->section, function ($q) {
                $q->where('section', $this->section);
            })
            ->get();


        $data = [];

        foreach ($students as $student) {
            $row = [
                $student->name,
                $student->code,
                $student->section,
            ];

            foreach ($this->attendances as $attendance) {
                $record = $student->attendanceRecords
                    ->where('attendance_id', $attendance->id)
                    ->first();

                $status = $record ? ($record->status ? '✔' : '✘') : '-';
                $row[] = $status;
            }

            $data[] = $row;
        }

        return $data;
    }

    public function headings(): array
    {
        $base = ['Name', 'Code', 'Section'];
        $headers = [];

        foreach ($this->attendances as $index => $att) {
            $headers[] = $att->created_at->format('Y-m-d'); // or "Session " . ($index + 1)
        }

        return array_merge($base, $headers);
    }
}
