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
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubjectController extends Controller
{
    public function index($Rid, $Sid)
    {
        try {
            // Load room with related attendance cards and records
            $room = Room::with([
                'attendanceCards' => function ($query) use ($Sid) {
                    $query->where('subject_id', $Sid)->with('records');
                }
            ])->findOrFail($Rid);

            // Load subject and doctors assigned to it
            $subject = Subject::findOrFail($Sid);
            $doctors = SubjectDoctor::with('user')
                ->where('room_id', $Rid)
                ->where('subject_id', $Sid)
                ->get();
            $user = auth()->user();

            // Check if the authenticated user is a member of the room
            $roomUser = RoomUser::where('room_id', $Rid)
                ->where('user_id', $user->id)
                ->first();

            if (!$roomUser) {
                return redirect()->back()->with('error', 'You are not a member of this room.');
            }

            $student = null;
            $studentQrCode = null;

            // Handle student logic
            if ($roomUser->code) {
                $student = Student::where('room_id', $Rid)
                    ->where('code', $roomUser->code)
                    ->first();

                if ($student) {
                    $student->load(['attendanceRecords' => function ($query) use ($room) {
                        $query->whereIn('attendance_id', $room->attendanceCards->pluck('id'));
                    }]);

                    $studentQrCode = QrCode::size(200)->generate($student->code);
                }
            }

            // If doctor but not assigned to the subject, treat as user
            if ($roomUser->role === 'doctor') {
                $isAssignedDoctor = SubjectDoctor::where([
                    'room_id' => $Rid,
                    'subject_id' => $Sid,
                    'room_user_id' => $roomUser->id,
                ])->exists();

                if (!$isAssignedDoctor) {
                    return view('main.Subjects.UserSubject', compact('room', 'subject', 'student', 'studentQrCode', 'doctors'));
                }

                return view('main.Subjects.AdminSubject', compact('room', 'subject', 'doctors'));
            }

            // Admin always sees the admin subject view
            if ($roomUser->role === 'admin') {
                return view('main.Subjects.AdminSubject', compact('room', 'subject', 'doctors'));
            }

            // Default user view
            return view('main.Subjects.UserSubject', compact('room', 'subject', 'student', 'studentQrCode', 'doctors'));
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Room or subject not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
    public function store(SubjectRequest $request, $roomId)
    {
        try {
            $room = Room::findOrFail($roomId);
            $subjectData = $request->validated();
            $subjectData['room_id'] = $room->id;
            Subject::create($subjectData);
            return redirect()->back()->with('success', '✅ Subject created successfully.');
        } catch (\Throwable $e) {
            Log::error("Subject creation failed: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', '❌ An unexpected error occurred while creating the subject.');
        }
    }
    public function doctor($roomId, $subjectId, Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return back()->with('error', 'User not found.');
            }

            // Check if user is in the room
            $roomUser = RoomUser::where('room_id', $roomId)
                ->where('user_id', $user->id)
                ->first();

            if (!$roomUser) {
                return back()->with('error', 'User is not a member of this room.');
            }

            // ❌ Prevent assigning admin as doctor
            if ($roomUser->role === 'admin') {
                return back()->with('error', 'Admin users cannot be assigned as doctors.');
            }

            // ✅ Prevent duplicate assignment
            $alreadyAssigned = SubjectDoctor::where([
                'room_user_id' => $roomUser->id,
                'room_id' => $roomId,
                'subject_id' => $subjectId
            ])->exists();

            if ($alreadyAssigned) {
                return back()->with('info', 'Doctor already assigned to this subject.');
            }

            // Set role to doctor if not already
            if ($roomUser->role !== 'doctor') {
                $roomUser->role = 'doctor';
                $roomUser->save();
            }

            // Create subject-doctor relation
            SubjectDoctor::create([
                'room_user_id' => $roomUser->id,
                'room_id' => $roomId,
                'subject_id' => $subjectId,
            ]);

            return back()->with('success', 'Doctor assigned to subject successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Room or Subject not found.');
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
