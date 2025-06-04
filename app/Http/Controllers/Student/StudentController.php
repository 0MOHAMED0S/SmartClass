<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\StudentsImport;
use App\Models\Room;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StudentController extends Controller
{
    public function index($id)
    {
        try {
            // Load room and students
            $room = Room::with('students')->findOrFail($id);
            return view('main.Room.import', compact('room'));
        }catch (\Exception $e) {
            Log::error("Error loading room: " . $e->getMessage());
            return redirect()->back()->with('error', '❌ Something went wrong while loading the room.');
        }
    }

    public function getStudents($roomId)
    {
        try {
            $room = Room::with('students')->findOrFail($roomId);
            $students = $room->students()->select(['id', 'name', 'code', 'section'])->get();
            return DataTables::of($students)
                ->addIndexColumn()
                ->make(true);
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Room or Subject not found.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function importStudents(Request $request, $room_id)
    {
        $room = Room::findOrFail($room_id);
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);

        try {
            Excel::import(new StudentsImport($room->id), $request->file('file'));
            return back()->with('importsuccess', 'Students imported successfully.<br>Please wait 1 minute then refresh the page.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Room  not found.');
        } catch (\Exception $e) {
            return back()->withErrors(['import_error' => $e->getMessage()]);
        }
    }
}
