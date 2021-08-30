<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        UserRole::truncate();
        $user = User::create(['name' => 'admin', 'email' => 'admin@gmail.com', 'email_verified_at' => now(), 'password' => '$2y$10$616hsKgkzYvHCJ2uimxRV.3Xad3RnoxB1wA8VeB9VPNZEoYlItKXO']);
        $role = Role::where('code', '=', 'admin')->first();
        $user->addRole('admin');
    }
}
