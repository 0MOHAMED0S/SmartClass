<?php

use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\features\FeatureController;
use App\Http\Controllers\GoogleAuth\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Room\RoomController;
use App\Http\Controllers\Subject\SubjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\StudentController;

Route::get('/', function () {return view('welcome');})->name('home');//done


// ==========================
// Google Resource
// ==========================

Route::middleware('guest')->group(function () {
Route::get('/login/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('login'); //done
Route::get('/login/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']); //done
});


// ==========================
// Room Resource
// ==========================

Route::middleware('auth')->group(function () {
// Create a new room
Route::post('/rooms', [RoomController::class, 'store'])->middleware('admin')->name('rooms.store'); //done
// Join a room
Route::post('/rooms/join', [RoomController::class, 'join'])->name('rooms.join'); //done
Route::get('/rooms/quick-join/{code}', [RoomController::class, 'quickJoin'])->name('rooms.quickJoin');//done
});


Route::middleware(['auth', 'joined.room'])->group(function () {
// List all rooms
Route::get('/rooms/{room}/subjects', [RoomController::class, 'index'])->name('subjects.index');//done
// Connect students to a room
Route::post('/rooms/{room}/subjects', [RoomController::class, 'connect'])->name('rooms.subjects.connect');//done



// ==========================
// Subject Resource
// ==========================

// Store subject
Route::post('/rooms/{room}/subjects/store', [SubjectController::class, 'store'])->middleware('admin')->name('rooms.subjects.store');//done

// Show subject in a room
Route::get('/rooms/{room}/subjects/{subject}', [SubjectController::class, 'index'])->name('rooms.subjects.show');//done

//make doctor
Route::post('subjects/{room}/subjects{subject}', [SubjectController::class, 'doctor'])->middleware('admin')->name('rooms.subjects.doctor');//done



// ==========================
// Student Resource
// ==========================

// Import students into a room
Route::get('/rooms/{room}/subjects/students/import', [StudentController::class, 'index'])->middleware('admin')->name('rooms.students.index');//done
Route::post('/rooms/{room}/subjects/students/import', [StudentController::class, 'importStudents'])->middleware('admin')->name('rooms.students.import');//done
// Get students in a room
Route::get('/rooms/{room}/students', [StudentController::class, 'getStudents'])->middleware('admin')->name('rooms.students');//done


// ==========================
// Attendance Resource
// ==========================

// Create attendance for a room and subject
Route::post('/rooms/{room}/subjects/{subject}/attendance', [AttendanceController::class, 'store'])->middleware('doctor.subject')->name('rooms.subjects.attendance.store');//done
// Get students for attendance in a room
Route::get('/rooms/{room}/attendance/students/{subject}', [AttendanceController::class, 'getStudents'])->middleware('doctor.subject')->name('attendance.students');//done
// Create attendance for a subject
Route::get('/rooms/{room}/subjects/{subject}/attend/{attend}',[AttendanceController::class, 'attend'])->middleware('doctor.subject')->name('subjects.attend');//done
//get subject attend
Route::get('/rooms/{room}/subjects/{subject}/attend/{attend}/students',[AttendanceController::class, 'attendStudents'])->middleware('doctor.subject')->name('subjects.attend.students');//done
//scan
Route::post('/rooms/{room}/subjects/{subject}/attend/{attend}/scan',[AttendanceController::class, 'scanindex'])->middleware('doctor.subject')->name('attend.scan.index');//done

Route::post('/subjects/{room}/subject/{subject}/attend/check/{attend}/check', [AttendanceController::class, 'scan'])->middleware('doctor.subject')->name('subjects.attend.scan');
//export
Route::get('/subjects/{room}/subject/{subject}/attendance/export', [AttendanceController::class, 'exportExcel'])->middleware('doctor.subject')->name('attendance.export');

});





Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
