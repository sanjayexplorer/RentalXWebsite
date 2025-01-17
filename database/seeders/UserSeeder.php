<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user=User::create(['mobile'=>'9001719023','password'=>bcrypt('MIquRU7VQBv6ixA')]);
        $user->assignRole('superAdmin');
    }
}
