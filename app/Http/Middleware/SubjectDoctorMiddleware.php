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
    $roomId = $request->route('room');
    $subjectId = $request->route('subject');
    $user = Auth::user();

    if (!$user) {
        return redirect()->back()->with('error', '❌ Access denied. Unauthorized.');
    }

    if ($user->role === 'admin') {
        return $next($request);
    }

    $roomUser = RoomUser::where('room_id', $roomId)
        ->where('user_id', $user->id)
        ->first();

    if (!$roomUser) {
        return redirect()->back()->with('error', '❌ Access denied. You are not a member of this room.');
    }

    $isDoctor = SubjectDoctor::where('room_user_id', $roomUser->id)
        ->where('room_id', $roomId)
        ->where('subject_id', $subjectId)
        ->exists();

    if (!$isDoctor) {
        return redirect()->back()->with('error', '❌ Access denied. You are not the doctor for this subject.');
    }

    return $next($request);
}

}
