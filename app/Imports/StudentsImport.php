<?php

namespace App\Imports;

use App\Jobs\ImportStudentsJob;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;
use Illuminate\Validation\ValidationException;

class StudentsImport implements ToCollection, WithHeadingRow
{
    private $room_id;
    private $validRows = [];

    public function __construct($room_id)
    {
        $this->room_id = $room_id;
    }

    public function collection(Collection $rows)
    {
        $codesInFile = [];
        $seenCodes = [];

        foreach ($rows as $index => $row) {
            $row = array_change_key_case(array_map('trim', $row->toArray()), CASE_LOWER);

            $validator = Validator::make($row, [
                'name' => 'required|string|max:255',
                'code' => 'required|string',
                'sec'  => 'required|integer|min:1|max:5',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'errors' => ["Row " . ($index + 2) . ": " . implode(', ', $validator->errors()->all())]
                ]);
            }

            $code = $row['code'] ?? null;

            // Check for duplicate code in file
            if (in_array($code, $seenCodes)) {
                throw ValidationException::withMessages([
                    'errors' => ["Duplicate code found in file: {$code} at row " . ($index + 2)]
                ]);
            }
            $seenCodes[] = $code;

            // Check for existing code in database
            if (Student::where('code', $code)->exists()) {
                throw ValidationException::withMessages([
                    'errors' => ["Code '{$code}' already exists in the database at row " . ($index + 2)]
                ]);
            }

            $this->validRows[] = $row;
        }

        // Prepare and dispatch bulk insert job
        $studentsToInsert = array_map(function ($row) {
            return [
                'name'       => $row['name'],
                'code'       => $row['code'],
                'section'    => $row['sec'],
                'room_id'    => $this->room_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $this->validRows);

        dispatch(new ImportStudentsJob($studentsToInsert));
    }
}
