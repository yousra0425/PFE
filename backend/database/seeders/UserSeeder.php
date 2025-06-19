<?php

// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Generate 10 users, 5 of which will be tutors
        User::factory()->count(10)->create(['role' => 'tutor']);
        User::factory()->count(5)->create(['role' => 'client']);
    }
}
