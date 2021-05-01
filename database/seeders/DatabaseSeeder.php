<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Link;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
         ->has(Link::factory()->count(500))
         ->create([
             'name' => 'Mazin',
             'email' => 'mazin@admin.com',
         ]);
    }
}
