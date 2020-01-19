<?php

use App\Backend\Permission\Urp; 
use App\Backend\Models\Role;
use App\Backend\Models\Permission;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetupRolesPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (Urp::roles() as $role) {
            Role::findOrCreate($role, 'api');
        }
        $adminRole = Role::findByName(Urp::ROLE_ADMIN);
        $managerRole = Role::findByName(Urp::ROLE_MANAGER);
        $editorRole = Role::findByName(Urp::ROLE_EDITOR);
        $userRole = Role::findByName(Urp::ROLE_USER);
        $visitorRole = Role::findByName(Urp::ROLE_VISITOR);

        foreach (Urp::permissions() as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        // Setup basic permission
        $adminRole->givePermissionTo(Urp::permissions());
        $managerRole->givePermissionTo(Urp::permissions([Urp::PERMISSION_PERMISSION_MANAGE]));
        $editorRole->givePermissionTo(Urp::menuPermissions());
        $editorRole->givePermissionTo(Urp::PERMISSION_ARTICLE_MANAGE);
        $userRole->givePermissionTo([
            Urp::PERMISSION_VIEW_MENU_ELEMENT_UI,
            Urp::PERMISSION_VIEW_MENU_PERMISSION,
        ]);
        $visitorRole->givePermissionTo([
            Urp::PERMISSION_VIEW_MENU_ELEMENT_UI,
            Urp::PERMISSION_VIEW_MENU_PERMISSION,
        ]);

        foreach (Urp::roles() as $role) {
            /** @var \App\User[] $users */
            $users = \App\Backend\Models\User::where('role', $role)->get();
            $role = Role::findByName($role);
            foreach ($users as $user) {
                $user->syncRoles($role);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('editor');
            });
        }

        /** @var \App\User[] $users */
        $users = \App\Backend\Models\User::all();
        foreach ($users as $user) {
            $roles = array_reverse(Urp::roles());
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    $user->role = $role;
                    $user->save();
                }
            }
        }
    }
}
