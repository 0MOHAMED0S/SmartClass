<?php

namespace App\Http\Controllers\Subject;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\SubjectRequest;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectDoctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SubjectController extends Controller
{
public function index($Rid, $Sid)
{
    $room = Room::with([
        'attendanceCards' => function ($query) use ($Sid) {
            $query->where('subject_id', $Sid)->with('records');
        }
    ])->findOrFail($Rid);

    $subject = Subject::findOrFail($Sid);
    $user = auth()->user();

    // Check if the user is a member of the room
    $roomUser = RoomUser::where('room_id', $room->id)
        ->where('user_id', $user->id)
        ->first();

    if (!$roomUser) {
        return redirect()->back()->with('error', 'You are not a member of this room.');
    }

    // If user is a member
    if ($roomUser->role === 'member') {
        $student = null;
        $studentQrCode = null;

        if ($roomUser->code) {
            $student = Student::where('room_id', $Rid)
                ->where('code', $roomUser->code)
                ->first();

            if ($student) {
                // Load attendance records only if student exists
                $student->load(['attendanceRecords' => function ($query) use ($room) {
                    $query->whereIn('attendance_id', $room->attendanceCards->pluck('id'));
                }]);

                $studentQrCode = QrCode::size(200)->generate($student->code);
            }
        }

        return view('main.Subjects.UserSubject', compact('room', 'subject', 'student', 'studentQrCode'));
    }

    // Admin or doctor view
    if ($roomUser->role === 'admin' || $roomUser->role === 'doctor') {
        return view('main.Subjects.AdminSubject', compact('room', 'subject'));
    }

    return redirect()->back()->with('error', 'Access denied.');
}




public function doctor($roomId, $subjectId, Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return back()->with('error', 'User not found.');
    }

    // Check if user is in the room
    $roomUser = RoomUser::where('room_id', $roomId)->where('user_id', $user->id)->first();

    if (!$roomUser) {
        return back()->with('error', 'User is not a member of this room.');
    }

    // Optional: check if already assigned
    $alreadyAssigned = SubjectDoctor::where([
        'room_user_id' => $roomUser->id,
        'room_id' => $roomId,
        'subject_id' => $subjectId
    ])->exists();

    if ($alreadyAssigned) {
        return back()->with('error', 'Doctor already assigned to this subject.');
    }

    //Update role to "doctor" if not already
    if ($roomUser->role !== 'doctor') {
        $roomUser->role = 'doctor';
        $roomUser->save();
    }

    // Assign doctor to the subject in the room
    SubjectDoctor::create([
        'room_user_id' => $roomUser->id,
        'room_id' => $roomId,
        'subject_id' => $subjectId,
    ]);

    return back()->with('success', 'Doctor assigned to subject successfully.');
}


    public function store(SubjectRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['room_id'] = $id;
            Subject::create($data);
            DB::commit();
            return redirect()->back()->with('success', 'Subject created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subject creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Subject Error .');
        }
    }
}
