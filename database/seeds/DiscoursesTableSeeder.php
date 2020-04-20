<?php

use Illuminate\Database\Seeder;

class DiscoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Discourse::class, 30)->create();
    }
}
