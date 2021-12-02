<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::query()->create(['id' => 'user', 'description' => 'User']);
        Role::query()->create(['id' => 'admin-organization', 'description' => 'Organization Administrator']);
    }
}
