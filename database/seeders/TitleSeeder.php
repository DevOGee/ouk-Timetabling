<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TitleSeeder extends Seeder
{
    public function run()
    {
        DB::table('titles')->insert([
            ['name' => 'Dr'],
            ['name' => 'Mr'],
            ['name' => 'Miss'],
            ['name' => 'Mrs'],
            ['name' => 'Prof'],
        ]);
    }
}
