<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discourse;

class DiscoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Update factory method to Laravel 10 syntax
        \App\Models\Discourse::factory()->count(30)->create();
    }
}
