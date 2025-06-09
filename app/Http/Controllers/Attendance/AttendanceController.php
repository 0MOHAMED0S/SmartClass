<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\AttendanceExportRequest;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\Subject;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exports\AttendanceExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function store(Request $request, $roomId, $subId)
    {
        try {
            // Validate the request input
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            // Find the room or fail, with students relationship loaded
            $room = Room::with('students')->findOrFail($roomId);

            // Check if the room has any students
            if ($room->students->isEmpty()) {
                return back()->with('error', 'Cannot create attendance session: no students in this room.');
            }

            // Create the attendance session
            $attendance = Attendance::create([
                'name' => $request->name,
                'room_id' => $roomId,
                'subject_id' => $subId
            ]);

            // Generate attendance records for all students in the room
            $records = $room->students->map(function ($student) use ($roomId, $subId, $attendance) {
                return [
                    'student_id' => $student->id,
                    'room_id' => $roomId,
                    'attendance_id' => $attendance->id,
                    'subject_id' => $subId,
                    'status' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            AttendanceRecord::insert($records);

            return redirect()
                ->back()
                ->with('success', 'The attendance session was created successfully.');
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Room not found.');
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function getStudents($roomId, Request $request)
    {
        try {
            $subjectId = $request->query('subject_id');

            // Validate subject_id existence
            if (!$subjectId) {
                return response()->json(['error' => 'Subject ID is required.'], 400);
            }

            // Retrieve room and its students
            $room = Room::with('students')->findOrFail($roomId);
            $students = $room->students()->select(['id', 'name', 'code', 'section'])->get();

            // Get all attendance session IDs for this room and subject
            $attendances = Attendance::where('room_id', $roomId)
                ->where('subject_id', $subjectId)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            return DataTables::of($students)
                ->addColumn('attendance', function ($student) use ($roomId, $attendances) {
                    // Retrieve student's attendance status for all attendance sessions
                    $attendanceRecords = AttendanceRecord::where('student_id', $student->id)
                        ->where('room_id', $roomId)
                        ->whereIn('attendance_id', $attendances)
                        ->pluck('status', 'attendance_id'); // [attendance_id => status]

                    // Map each session to ✅ or ❌
                    $statuses = [];
                    foreach ($attendances as $attendanceId) {
                        $status = $attendanceRecords[$attendanceId] ?? 0;
                        $statuses[] = $status ? '✅' : '❌';
                    }

                    return $statuses;
                })
                ->make(true);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Room not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function attend($room, $subject, $attend)
    {
        try {
            $subject = Subject::findOrFail($subject);
            $room = Room::findOrFail($room);
            $attendance = Attendance::findOrFail($attend);
            return view('main.attend.Attend', compact('room', 'subject', 'attend', 'attendance'));
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Room, Subject, or Attendance session not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function attendStudents(Request $request, $roomId, $subjectId, $attendId)
    {
        try {
            $records = AttendanceRecord::with('student')
                ->where('room_id', $roomId)
                ->where('subject_id', $subjectId)
                ->where('attendance_id', $attendId)
                ->get();

            return datatables()->of($records)
                ->addColumn('name', fn($record) => $record->student->name ?? '-')
                ->addColumn('code', fn($record) => $record->student->code ?? '-')
                ->addColumn('section', fn($record) => $record->student->section ?? '-')
                ->addColumn('status', function ($record) {
                    return $record->status == 1
                        ? '<span class="badge bg-success">✅ Present</span>'
                        : '<span class="badge bg-danger">❌ Absent</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while retrieving attendance data.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function scan(Request $request)
    {
        $code = $request->input('qr_code');
        $roomId = $request->input('room_id');
        $subjectId = $request->input('subject_id');
        $attendId = $request->input('attend_id');
        // Get the student
        $student = Student::where('code', $code)->first();

        if (!$student) {
            return response()->json(['message' => '❌ Student not found.'], 404);
        }

        // Find the attendance record
        $record = AttendanceRecord::where('attendance_id', $attendId)
            ->where('room_id', $roomId)
            ->where('subject_id', $subjectId)
            ->where('student_id', $student->id)
            ->first();

        if (!$record) {
            return response()->json(['message' => '❌ No matching attendance record found.'], 404);
        }

        // Check if already marked present
        if ($record->status == 1) {
            return response()->json([
                'message' => "ℹ️ Attendance already marked for student: {$student->name} {$code}"
            ], 200);
        }

        // Mark as present
        $record->status = 1;
        $record->save();

        return response()->json([
            'message' => "✅ Attendance marked for student: {$student->name} {$code}"
        ]);
    }


   public function scanIndex(Request $request, $roomId, $subjectId, $attendId)
{
    try {
        $room = Room::findOrFail($roomId);
        $subject = Subject::findOrFail($subjectId);
        $attendance = Attendance::findOrFail($attendId);
        $attend=$attendId;
        $sections = $request->input('sections', []);

        // dd($sections);

        return view('main.attend.scan', compact('room', 'subject', 'attendance','attend', 'sections'));

    } catch (ModelNotFoundException $e) {
        return redirect()->back()->with('error', '❌ Room, Subject, or Attendance session not found.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', '❌ An unexpected error occurred: ' . $e->getMessage());
    }
}


    public function exportExcel(AttendanceExportRequest $request, $roomId, $subjectId)
    {
        try {
            // Check if room exists
            $room = Room::findOrFail($roomId);
            // Check if subject exists and belongs to the room
            $subject = Subject::where('id', $subjectId)
                ->where('room_id', $roomId)
                ->firstOrFail();

            // Get filter values
            $month = $request->input('month'); // single value
            $section = $request->input('section'); // array

            // Check if there are attendances in this month
            if ($month != null) {
                $hasAttendance = Attendance::where('room_id', $roomId)
                    ->where('subject_id', $subjectId)
                    ->whereMonth('created_at', $month)
                    ->exists();

                if (!$hasAttendance) {
                    return back()->withErrors([
                        'export' => 'No attendance records found for the selected month.'
                    ]);
                }
            }
            // Proceed with export
            return Excel::download(
                new AttendanceExport($room->id, $subject->id, $month, $section),
                'Filtered_Attendance.xlsx'
            );
        } catch (ModelNotFoundException $e) {
            return back()->withErrors([
                'export' => 'Room or Subject not found or mismatched.'
            ]);
        } catch (\Exception $e) {
            Log::error('Excel export failed: ' . $e->getMessage());
            return back()->withErrors([
                'export' => 'An unexpected error occurred during export. Please try again.'
            ]);
        }
    }
}
