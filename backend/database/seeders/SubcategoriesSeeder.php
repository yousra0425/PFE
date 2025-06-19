<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubcategoriesSeeder extends Seeder
{
    public function run()
    {
        $subcategories = [
            // Mathematics - category_id 1
            ['name' => 'Calculus', 'category_id' => 1],
            ['name' => 'Algebra', 'category_id' => 1],
            ['name' => 'Geometry', 'category_id' => 1],
            ['name' => 'Statistics', 'category_id' => 1],
            ['name' => 'Number Theory', 'category_id' => 1],

            // Physics - category_id 2
            ['name' => 'Classical Mechanics', 'category_id' => 2],
            ['name' => 'Electromagnetism', 'category_id' => 2],
            ['name' => 'Quantum Mechanics', 'category_id' => 2],
            ['name' => 'Thermodynamics', 'category_id' => 2],
            ['name' => 'Optics', 'category_id' => 2],

            // Chemistry - category_id 3
            ['name' => 'Organic Chemistry', 'category_id' => 3],
            ['name' => 'Inorganic Chemistry', 'category_id' => 3],
            ['name' => 'Physical Chemistry', 'category_id' => 3],
            ['name' => 'Analytical Chemistry', 'category_id' => 3],
            ['name' => 'Biochemistry', 'category_id' => 3],

            // Biology - category_id 4
            ['name' => 'Botany', 'category_id' => 4],
            ['name' => 'Zoology', 'category_id' => 4],
            ['name' => 'Genetics', 'category_id' => 4],
            ['name' => 'Microbiology', 'category_id' => 4],
            ['name' => 'Ecology', 'category_id' => 4],

            // English - category_id 5
            ['name' => 'Literature', 'category_id' => 5],
            ['name' => 'Grammar', 'category_id' => 5],
            ['name' => 'Writing Skills', 'category_id' => 5],
            ['name' => 'Reading Comprehension', 'category_id' => 5],
            ['name' => 'Speaking & Listening', 'category_id' => 5],

            // French - category_id 6
            ['name' => 'French Grammar', 'category_id' => 6],
            ['name' => 'French Literature', 'category_id' => 6],
            ['name' => 'Conversational French', 'category_id' => 6],
            ['name' => 'French Writing', 'category_id' => 6],
            ['name' => 'French Phonetics', 'category_id' => 6],

            // Computer Science - category_id 7
            ['name' => 'Algorithms', 'category_id' => 7],
            ['name' => 'Data Structures', 'category_id' => 7],
            ['name' => 'Databases', 'category_id' => 7],
            ['name' => 'Operating Systems', 'category_id' => 7],
            ['name' => 'Networking', 'category_id' => 7],

            // History - category_id 8
            ['name' => 'Ancient History', 'category_id' => 8],
            ['name' => 'Medieval History', 'category_id' => 8],
            ['name' => 'Modern History', 'category_id' => 8],
            ['name' => 'World Wars', 'category_id' => 8],
            ['name' => 'History of Science', 'category_id' => 8],

            // Geography - category_id 9
            ['name' => 'Physical Geography', 'category_id' => 9],
            ['name' => 'Human Geography', 'category_id' => 9],
            ['name' => 'Geographical Information Systems', 'category_id' => 9],
            ['name' => 'Climatology', 'category_id' => 9],
            ['name' => 'Cartography', 'category_id' => 9],

            // Economics - category_id 10
            ['name' => 'Microeconomics', 'category_id' => 10],
            ['name' => 'Macroeconomics', 'category_id' => 10],
            ['name' => 'International Economics', 'category_id' => 10],
            ['name' => 'Econometrics', 'category_id' => 10],
            ['name' => 'Development Economics', 'category_id' => 10],
        ];

        DB::table('subcategories')->insert($subcategories);
    }
}
