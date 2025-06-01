<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RoomUser;
use App\Models\SubjectDoctor;

class SubjectDoctorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $roomId = $request->route('room');     // From route {room}
        $subjectId = $request->route('subject'); // From route {subject}

        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Get room_user record
        $roomUser = RoomUser::where('room_id', $roomId)
            ->where('user_id', $user->id)
            ->first();

        if (!$roomUser) {
            abort(403, 'You are not a member of this room.');
        }

        // Check if this room_user is assigned as doctor for this subject
        $isDoctor = SubjectDoctor::where('room_user_id', $roomUser->id)
            ->where('room_id', $roomId)
            ->where('subject_id', $subjectId)
            ->exists();

        if (!$isDoctor) {
        return redirect()->back()->with('error', 'Access denied. You are not the doctor for this subject.');
    }

        return $next($request);
    }
}
