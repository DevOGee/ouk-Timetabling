<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// use TitleSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(TitleSeeder::class);
    }
}
