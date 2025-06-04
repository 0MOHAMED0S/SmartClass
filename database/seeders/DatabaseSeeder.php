<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Mohammed Sayed',
            'email' => 'msa0back@gmail.com',
            'role' => "admin",
            'google_id' => '102736598776507148394',
            'path'=>"https://lh3.googleusercontent.com/a/ACg8ocJuV29IzntNVm690tYLoKq2uaR_5AHvrQTiHFfV_fMRetpsME9s=s96-c"
        ]);
    }
}
