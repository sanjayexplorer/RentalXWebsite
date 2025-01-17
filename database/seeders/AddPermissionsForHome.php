<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class AddPermissionsForHome extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'home']);
        $agent=Role::whereId(2)->first();
        $agent->givePermissionTo('home');
        $partner=Role::whereId(3)->first();
        $partner->givePermissionTo('home');
      
    }
}
