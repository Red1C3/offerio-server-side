<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
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
        Category::insert([
            ['name' => 'Fruit'],
            ['name' => 'Veggies'],
            ['name' => 'Meat'],
            ['name' => 'Candy'],
            ['name' => 'Sauce'],
            ['name' => 'Beverages'],
            ['name' => 'Drugs'],
            ['name' => 'Makeups']
        ]);
        User::insert([[
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin')
        ]]);
    }
}
