<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::Where('email', 'admin@uaeicons.com')->first();

        if (is_null($user)) {
            $user = User::create([
                'name' => 'admin',
                'email' => 'admin@uaeicons.com',
                'mobile' => '1234568',
                'password' => bcrypt('abc123'),
                'status' => 1
            ]);

            $user->assignRole('Admin');
        }

        $user = User::where('email', 'superadmin@uaeicons.com')->first();

        if (is_null($user)) {
            $user = User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@uaeicons.com',
                'mobile' => '1234567890', // Update with desired mobile number
                'password' => bcrypt('superpassword'), // Update with a strong password
                'status' => 1
            ]);

            $user->assignRole('Super Admin');
        }

    }
}
