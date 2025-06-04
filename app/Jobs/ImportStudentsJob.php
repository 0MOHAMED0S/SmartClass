<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Student;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class ImportStudentsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $students;

    public function __construct(array $students)
    {
        $this->students = $students;
    }

    public function handle()
    {
        // Perform bulk insert
        Student::insert($this->students);
    }
}
