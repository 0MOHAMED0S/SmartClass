<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\Subject;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Cache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
// In AttendanceController.php

public function scanindex(Request $request, $roomId, $subjectId, $attendId)
{
    $attend = $attendId;
    $subject = Subject::findOrFail($subjectId);
    $room = Room::findOrFail($roomId);
    $attendance = Attendance::findOrFail($attendId);

    return view('main.attend.scan', compact('room', 'subject', 'attend', 'attendance'));
}

public function scan(Request $request)
{
    $code = $request->input('qr_code');
    $roomId = $request->input('room_id');
    $subjectId = $request->input('subject_id');
    $attendId = $request->input('attend_id');

    // Find the record
    $record = AttendanceRecord::where('attendance_id', $attendId)
        ->where('room_id', $roomId)
        ->where('subject_id', $subjectId)
        ->first();

    if (!$record) {
        return response()->json(['message' => '❌ No matching attendance record found.'], 404);
    }

    // Update the status to 1 (present)
    $record->status = 1;
    $record->save();

    return response()->json(['message' => "✅ Attendance marked for student code: $code"]);
}




public function attendStudents(Request $request, $roomId, $subjectId, $attendId)
{
    $records = AttendanceRecord::with('student')
        ->where('room_id', $roomId)
        ->where('subject_id', $subjectId)
        ->where('attendance_id', $attendId);

    return datatables()->of($records)
        ->addColumn('name', fn($record) => $record->student->name ?? '-')
        ->addColumn('code', fn($record) => $record->student->code ?? '-')
        ->addColumn('section', fn($record) => $record->student->section ?? '-')
        ->addColumn('status', fn($record) => $record->status == 1
            ? '<span class="badge bg-success">✅ Present</span>'
            : '<span class="badge bg-danger">❌ Absent</span>'
        )
        ->rawColumns(['status'])
        ->make(true);
}


public function attend($room, $subject, $attend)
{
    $subject = Subject::findOrFail($subject);
    $room = Room::findOrFail($room);
    $attendance=Attendance::findOrFail($attend);
    return view('main.attend.Attend', compact('room', 'subject', 'attend','attendance'));
}


    public function indexAdmin($id)
    {
        // Get the subject
        $subject = Subject::findOrFail($id);

        // Get the room with filtered attendanceCards by subject
        $room = Room::with([
            'students',
            'attendanceCards' => function ($query) use ($id) {
                $query->where('subject_id', $id)->with('records');
            }
        ])->findOrFail($subject->room_id);

        return view('main.Subjects.AdminSubject', compact('room', 'subject'));
    }

    public function index($roomId)
    {
        $room = Subject::with(['attendanceCards.records', 'students'])->findOrFail($roomId);

        // Get the logged-in user's RoomUser record
        $roomUser = RoomUser::where('user_id', auth()->id())
            ->where('room_id', $roomId)
            ->first();

        if (!$roomUser || !$roomUser->code) {
            return view('main.attend.memberattendance', compact('room'))
                ->with('error', 'You have not entered a code yet. Please enter your code.');
        }

        // Find the student by code
        $student = Student::where('room_id', $roomId)
            ->where('code', $roomUser->code)
            ->with('attendanceRecords')
            ->first();

        if (!$student) {
            return view('main.attend.memberattendance', compact('room'))->with('error', 'No student found with your code.');
        }

        // Generate QR Code
        $qrCode = QrCode::size(200)->generate($student->code);

        return view('main.attend.memberattendance', compact('room', 'student', 'qrCode'));
    }

    public function store(Request $request, $roomId, $subId)
    {
        $room = Room::findOrFail($roomId);

        $attendance = Attendance::create([
            'name' => $request->name,
            'room_id' => $roomId,
            'subject_id' => $subId
        ]);

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

        return redirect()->route('attendance.indexAdmin', $subId)->with('success', 'The Attend Session Maded Succussfully');
    }

    public function getStudents($roomId, Request $request)
    {
        $subjectId = $request->query('subject_id');
        $students = Room::findOrFail($roomId)->students()->select(['id', 'name', 'code', 'section'])->get();
        $attendances = Attendance::where('room_id', $roomId)
            ->where('subject_id', $subjectId)
            ->orderBy('id')->pluck('id')->toArray();

        return DataTables::of($students)
            ->addColumn('attendance', function ($student) use ($roomId, $attendances) {
                $attendanceRecords = AttendanceRecord::where('student_id', $student->id)
                    ->where('room_id', $roomId)
                    ->whereIn('attendance_id', $attendances)
                    ->pluck('status', 'attendance_id');

                // Output as an indexed array
                $statuses = [];
                foreach ($attendances as $attendanceId) {
                    $statuses[] = isset($attendanceRecords[$attendanceId]) ? ($attendanceRecords[$attendanceId] ? '✅' : '❌') : '❌';
                }

                return $statuses; // indexed array — good
            })
            ->make(true);
    }


    public function connect(Request $request, $roomId)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $room = Room::findOrFail($roomId);

        // Find the logged-in user's RoomUser record for the given room
        $roomUser = RoomUser::where('user_id', auth()->id())
            ->where('room_id', $roomId)
            ->first();

        if (!$roomUser) {
            return back()->with('error', 'You are not assigned to this room.');
        }

        // Check if a student with the provided code exists in the room
        $studentExists = Student::where('room_id', $roomId)
            ->where('code', $request->code)
            ->exists();

        if (!$studentExists) {
            return back()->with('error', 'Invalid code. No student found with this code.');
        }

        // Update the RoomUser's code field
        $roomUser->update(['code' => $request->code]);

        return back()->with('success', 'Successfully connected and updated your code.');
    }
}
