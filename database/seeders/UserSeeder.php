<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Ahmed',
            'last_name' => 'Hussein',
            'username' => 'a_hussien',
            'email' => 'test@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test1234'), // password
            'remember_token' => Str::random(10),
        ]);

        User::factory()->times(14)->create();
    }
}
