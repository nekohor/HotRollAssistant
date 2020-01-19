<?php

use App\Backend\Models\User;
use App\Backend\Permission\Urp;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@hot.roll',
            'password' => Hash::make('11235813'),
        ]);
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@hot.roll',
            'password' => Hash::make('22501580'),
        ]);
        $editor = User::create([
            'name' => '娄亚彬',
            'email' => 'lyb@4gz.mg',
            'password' => Hash::make('lyb1580'),
        ]);
        $user = User::create([
            'name' => '管控',
            'email' => 'gk@4gz.mg',
            'password' => Hash::make('22501580'),
        ]);
        $visitor = User::create([
            'name' => 'Visitor',
            'email' => 'visitor@hot.roll',
            'password' => Hash::make('22501580'),
        ]);

        $adminRole = Role::findByName(Urp::ROLE_ADMIN);
        $managerRole = Role::findByName(Urp::ROLE_MANAGER);
        $editorRole = Role::findByName(Urp::ROLE_EDITOR);
        $userRole = Role::findByName(Urp::ROLE_USER);
        $visitorRole = Role::findByName(Urp::ROLE_VISITOR);
        $admin->syncRoles($adminRole);
        $manager->syncRoles($managerRole);
        $editor->syncRoles($editorRole);
        $user->syncRoles($userRole);
        $visitor->syncRoles($visitorRole);
    }
}
