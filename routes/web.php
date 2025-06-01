<?php

use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\features\FeatureController;
use App\Http\Controllers\GoogleAuth\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Room\RoomController;
use App\Http\Controllers\Subject\SubjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\StudentController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================
// Room Resource
// ==========================

Route::middleware(['auth', 'joined.room'])->group(function () {


// Connect students to a room
Route::post('/rooms/{room}/students/connect', [RoomController::class, 'connect'])->name('rooms.students.connect');

// Get students in a room
Route::get('/rooms/{room}/students', [StudentController::class, 'getStudents'])->middleware('admin')->name('rooms.students');


// ==========================
// Subject Resource
// ==========================

// List all rooms
Route::get('/rooms/subjects/{room}', [RoomController::class, 'index'])->name('subjects.index')->middleware('joined.room');

// Show subject details
Route::get('subjects/{subject}', [SubjectController::class, 'show'])->name('subjects.show');

// Store subject data (e.g., assign to room)
Route::post('subjects/{room}', [SubjectController::class, 'store'])->middleware('admin')->name('subjects.store');

//make doctor
Route::post('subjects/{room}/{subject}', [SubjectController::class, 'doctor'])->middleware('admin')->name('subjects.doctor');

// Show subject in a room
Route::get('/rooms/{room}/subjects/{subject}', [SubjectController::class, 'index'])->name('rooms.subjects.show');

Route::get('/rooms/{room}/subjects/{subject}/attend/{attend}',
    [AttendanceController::class, 'attend'])
    ->middleware('doctor.subject')
    ->name('subjects.attend');

Route::get('/rooms/{room}/subjects/{subject}/attend/{attend}/students',
    [AttendanceController::class, 'attendStudents'])
    ->middleware('doctor.subject')
    ->name('subjects.attend.students');


Route::middleware('doctor.subject')->group(function () {
    Route::get('/rooms/{room}/subjects/{subject}/attend/{attend}/scan', [AttendanceController::class, 'scanindex'])->name('attend.scan.index');
});

Route::post('/subjects/attend/scan', [AttendanceController::class, 'scan'])->name('subjects.attend.scan');

// ==========================
// Attendance Resource
// ==========================

// Create attendance for a room and subject
Route::post('/rooms/{room}/subjects/{subject}/attendance', [AttendanceController::class, 'store'])->middleware('doctor.subject')->name('attendance.store');

// Get students for attendance in a room
Route::get('/rooms/{room}/attendance/students/{subject}', [AttendanceController::class, 'getStudents'])->middleware('doctor.subject')->name('attendance.students');


// ==========================
// Student Resource
// ==========================

// Import students into a room (you can also use this as POST if uploading files)
Route::get('/rooms/{room}/students/import', [StudentController::class, 'index'])->middleware('admin')->name('students.index');
Route::post('/rooms/{room}/students/import', [StudentController::class, 'importStudents'])->middleware('admin')->name('students.import');

});

Route::middleware('auth')->group(function () {
// Create a new room
Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
// Join a room
Route::post('/rooms/join', [RoomController::class, 'join'])->name('rooms.join');
});

// ==========================
// Google Resource
// ==========================

Route::middleware('guest')->group(function () {
Route::get('/login/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/login/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});




Route::get('/room/{id}/members', [RoomController::class, 'members'])->name('rooms.members');
Route::get('/attendance/create/{id}', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/attendance/create/admin/{id}', [AttendanceController::class, 'indexAdmin'])->name('attendance.indexAdmin');
Route::post('/attendance/student/connect/{roomId}', [AttendanceController::class, 'connect'])->name('attendance.connect');


Route::get('/students/upload', [StudentController::class, 'showUploadForm']);
// Route::post('/students/d:\dashboard\assets', [StudentController::class, 'import'])->name('students.import');
// Route::post('/rooms/{room}/import-students', [StudentController::class, 'importStudents'])
//     ->name('students.import');
require __DIR__.'/auth.php';
