<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Mathematics',
            'Physics',
            'Chemistry',
            'Biology',
            'English',
            'French',
            'Computer Science',
            'History',
            'Geography',
            'Economics',
        ];

        foreach ($categories as $category) {
            Category::create(['label' => $category]);
        }
    }
}
