<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\ConnectRequest;
use App\Http\Requests\Room\JoinRoomRequest;
use App\Http\Requests\Room\RoomRequest;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoomController extends Controller
{
    public function index($id)
    {
        try {
            $room = Room::findOrFail($id);
            $joinUrl = route('rooms.quickJoin', ['code' => $room->code]);
            $roomQrCode = QrCode::size(200)->generate($joinUrl);
            $student = null;
            $studentQrCode = null;
            $roomUser = RoomUser::where('user_id', Auth::id())
                ->where('room_id', $id)
                ->first();
            if ($roomUser && $roomUser->code) {
                $student = Student::where('room_id', $id)
                    ->where('code', $roomUser->code)
                    ->first();

                if ($student) {
                    $studentQrCode = QrCode::size(200)->generate($student->code);
                }
            }
            return view('main.Room.room', compact('room', 'roomQrCode', 'student', 'studentQrCode'));
        }catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Room or Room not found.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the room.');
        }
    }
    public function store(RoomRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $userId = Auth::id();
            // Generate a unique room code
            do {
                $roomCode = Str::random(6); // Generates a random 6-character string
            } while (Room::where('code', $roomCode)->exists()); // Ensure uniqueness

            $data['code'] = $roomCode;
            $room = Room::create($data);
            RoomUser::create([
                'user_id' => $userId,
                'room_id' => $room->id,
                'role' => 'admin',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Room created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Room creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Room Error .');
        }
    }

    public function join(JoinRoomRequest $request)
    {
        try {
            $roomCode = $request->input('code');
            $room = Room::where('code', $roomCode)->first();

            if (!$room) {
                return redirect()->back()->with('error', '❌ Invalid room code.');
            }

            $userId = Auth::id();

            $alreadyJoined = RoomUser::where('user_id', $userId)
                ->where('room_id', $room->id)
                ->first();

            if ($alreadyJoined) {
                return redirect()->route('subjects.index', $room->id)->with('info', 'ℹ️ You are already in this room.');
            }

            // Add user to room
            RoomUser::create([
                'user_id' => $userId,
                'room_id' => $room->id,
                'role' => 'member',
            ]);

            return redirect()->back()->with('success', '✅ You joined the room successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ An error occurred while joining the room.');
        }
    }

    public function quickJoin($code)
    {
        try {
            $room = Room::where('code', $code)->first();

            if (!$room) {
                return redirect()->route('home')->with('error', '❌ Invalid room code.');
            }

            $userId = Auth::id();

            // Check if already joined
            $alreadyJoined = RoomUser::where('user_id', $userId)
                ->where('room_id', $room->id)
                ->exists();

            if ($alreadyJoined) {
                return redirect()->route('subjects.index', $room->id)->with('info', 'ℹ️ You are already in this room.');
            }

            // Join the room
            RoomUser::create([
                'user_id' => $userId,
                'room_id' => $room->id,
                'role' => 'member',
            ]);

            return redirect()->route('rooms.show', $room->id)->with('success', '✅ You joined the room successfully.');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', '❌ Error joining room.');
        }
    }

    public function connect(ConnectRequest $request, $roomId)
    {
        try {
            // Find the logged-in user's RoomUser record for the given room
            $roomUser = RoomUser::where('user_id', auth()->id())
                ->where('room_id', $roomId)
                ->first();

            if (!$roomUser) {
                return back()->with('error', '❌ You are not assigned to this room.');
            }

            // Check if a student with the provided code exists in the room
            $studentExists = Student::where('room_id', $roomId)
                ->where('code', $request->code)
                ->exists();

            if (!$studentExists) {
                return back()->with('error', '❌ Invalid code. No student found with this code.');
            }

            // Update the RoomUser's code field
            $roomUser->update(['code' => $request->code]);

            return back()->with('success', '✅ Successfully connected and updated your code.');
        } catch (\Exception $e) {
            Log::error('Connect error: ' . $e->getMessage());
            return back()->with('error', '❌ An unexpected error occurred. Please try again.');
        }
    }










    public function show($id)
    {
        $room = Room::with('students', 'roomUsers')->findOrFail($id);
        $qrCode = QrCode::size(200)->generate($room->code);
        $adminCount = $room->roomUsers->where('role', 'admin')->count();
        // Get the current user's role in the room
        $userRole = $room->userRole(auth()->id());
        return view('main.room', compact('room', 'qrCode', 'userRole', 'adminCount'));
    }
    public function members($id)
    {
        $room = Room::with('roomUsers.user')->findOrFail($id);
        $qrCode = QrCode::size(200)->generate($room->code);
        // Count admins (teachers)
        $adminCount = $room->roomUsers->where('role', 'admin')->count();
        return view('main.members', compact('room', 'qrCode', 'adminCount'));
    }
}
