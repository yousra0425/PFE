<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tutor;
use Illuminate\Support\Facades\DB;

class TutorSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate safely
        \App\Models\Tutor::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $baseLat = 35.7595;
        $baseLng = -5.83395;

        $usersWithTutorRole = \App\Models\User::where('role', 'tutor')->get();

        foreach ($usersWithTutorRole as $user) {
            $latOffset = fake()->randomFloat(6, -0.01, 0.01);
            $lngOffset = fake()->randomFloat(6, -0.01, 0.01);

            Tutor::create([
                'user_id' => $user->id,
                'category_id' => rand(1, 4),
                'bio' => fake()->paragraph(),
                'hourly_rate' => fake()->randomFloat(2, 10, 50),
                'experience_years' => fake()->numberBetween(1, 15),
                'profile_picture' => 'https://via.placeholder.com/200x200.png/00dd88?text=people',
                'rating' => fake()->randomFloat(2, 3, 5),
                'cin_image' => 'default_cin.jpg',
                'is_verified' => true,
                'latitude' => $baseLat + $latOffset,
                'longitude' => $baseLng + $lngOffset,
            ]);
        }
    }
}
