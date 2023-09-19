<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

         for ($i = 1; $i <= 5; $i++) {

             \App\Models\User::factory()->create([
                 'name' => 'Test User' . $i,
                 'email' => "test{$i}@example.com",
                 'token' => Str::random(10)
             ]);
         }

        for ($i = 1; $i <= 5; $i++) {
            DB::table('tasks')->insert([
                'user_id' => random_int(1, 5),
                'status' => 0,
                'priority' => random_int(1, 5),
                'title' => 'Title' . $i,
                'description' => 'Description' . $i,
                'createdAt' => '2023-09-18 10:50:15',
            ]);
        }
    }
}
