<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();
        Role::insert([
            ['name' => 'user', 'code' => 'forum-user'],
            ['name' => 'admin', 'code' => 'admin']
        ]);
    }
}
