<?php

// database/factories/UserFactory.php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\TutorSeeder;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $baseLat = 35.7595;
        $baseLng = -5.83395;

        // Generate small random offset within ±0.01 degrees
        $latOffset = $this->faker->randomFloat(6, -0.01, 0.01);
        $lngOffset = $this->faker->randomFloat(6, -0.01, 0.01);

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'address' => 'Tangier, Morocco',
            'birth_date' => $this->faker->date(),
            'telephone' => $this->faker->phoneNumber(),
            'city' => 'Tangier',
            'cin' => $this->faker->numerify('##########'),
            'cin_status' => 'approved',
            'role' => 'tutor',
            'latitude' => $baseLat + $latOffset,
            'longitude' => $baseLng + $lngOffset,
            'remember_token' => Str::random(10),
        ];
    }

    public function run()
{
    // Create 20 tutors
    \App\Models\User::factory()->count(20)->create([
        'role' => 'tutor',
    ]);

    // Then create matching Tutor profiles
    $this->call(TutorSeeder::class);
}
}
