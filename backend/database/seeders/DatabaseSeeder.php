<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CategorySeeder::class,
            SubcategoriesSeeder::class,
            UserSeeder::class,
            TutorSeeder::class,
           
            
        ]);
    }
}