<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; 
class PermissionRoleSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'roles.view',
            'roles.create',
            'roles.managePermissions',
            'permissions.view',
            'permissions.create',
            'user.manageUserAccess',
            'user.removeUserAccess',
            'user.viewAny',
            'user.view',
            'user.create',
            'user.update',
            'user.deactivate',
            'user.restore',
            'user.delete',
            'video.view',
            'video.create',
            'video.update',
            'video.delete',
            'reel.view',
            'reel.create',
            'reel.update',
            'reel.delete',
            'podcast.view',
            'podcast.create',
            'podcast.update',
            'podcast.delete',
            'news.view',
            'news.create',
            'news.update',
            'news.delete',
            'hashtag.create',
            'hashtag.update',
            'hashtag.delete',
            'categories.create',
            'categories.update',
            'categories.delete',
            'article.create',
            'article.update',
            'article.delete',
            'article.view',
            'templates.create',
            'templates.update',
            'templates.delete',
            'contact_messages.update',
            'contact_messages.view',
            'contact_messages.delete',
            'tenant.update',
            'team_member.view',
            'team_member.create',
            'team_member.update',
            'team_member.delete',
            'reel_group.view',
            'reel_group.create',
            'reel_group.update',
            'reel_group.delete',
            'task.view',
            'task.create',
            'task.update',
            'task.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'api']);
        }

        $super_admin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'api']);
        // $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        // $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'api']);

        $super_admin->givePermissionTo(Permission::all());


        $super_adminUser = User::first(); 
        if ($super_adminUser) {
            $super_adminUser->assignRole('super_admin');
        }
    }
}