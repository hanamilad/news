<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Roles
            'roles.view' => 'عرض الأدوار',
            'roles.create' => 'إنشاء دور جديد',
            'roles.managePermissions' => 'إدارة صلاحيات الأدوار',

            // Permissions
            'permissions.view' => 'عرض الصلاحيات',
            'permissions.create' => 'إنشاء صلاحية جديدة',

            // Users
            'user.manageUserAccess' => 'إدارة وصول المستخدمين',
            'user.removeUserAccess' => 'إزالة وصول المستخدمين',
            'user.viewAny' => 'عرض جميع المستخدمين',
            'user.view' => 'عرض بيانات المستخدم',
            'user.create' => 'إنشاء مستخدم جديد',
            'user.update' => 'تعديل بيانات المستخدم',
            'user.deactivate' => 'تعطيل المستخدم',
            'user.restore' => 'استعادة المستخدم',
            'user.delete' => 'حذف المستخدم',

            // Videos
            'video.view' => 'عرض الفيديوهات',
            'video.create' => 'إضافة فيديو',
            'video.update' => 'تعديل الفيديو',
            'video.delete' => 'حذف الفيديو',

            // Reels
            'reel.view' => 'عرض الريلز',
            'reel.create' => 'إضافة ريل',
            'reel.update' => 'تعديل الريل',
            'reel.delete' => 'حذف الريل',

            // Reel Groups
            'reel_group.view' => 'عرض مجموعات الريلز',
            'reel_group.create' => 'إنشاء مجموعة ريلز',
            'reel_group.update' => 'تعديل مجموعة الريلز',
            'reel_group.delete' => 'حذف مجموعة الريلز',

            // Podcasts
            'podcast.view' => 'عرض البودكاست',
            'podcast.create' => 'إضافة بودكاست',
            'podcast.update' => 'تعديل البودكاست',
            'podcast.delete' => 'حذف البودكاست',

            // News
            'news.view' => 'عرض الأخبار',
            'news.create' => 'إضافة خبر',
            'news.update' => 'تعديل الخبر',
            'news.delete' => 'حذف الخبر',

            // Articles
            'article.view' => 'عرض المقالات',
            'article.create' => 'إضافة مقال',
            'article.update' => 'تعديل المقال',
            'article.delete' => 'حذف المقال',

            // Hashtags
            'hashtag.create' => 'إنشاء هاشتاج',
            'hashtag.update' => 'تعديل الهاشتاج',
            'hashtag.delete' => 'حذف الهاشتاج',

            // Categories
            'categories.create' => 'إنشاء تصنيف',
            'categories.update' => 'تعديل التصنيف',
            'categories.delete' => 'حذف التصنيف',

            // Templates
            'templates.create' => 'إنشاء قالب',
            'templates.update' => 'تعديل القالب',
            'templates.delete' => 'حذف القالب',

            // Contact Messages
            'contact_messages.view' => 'عرض رسائل التواصل',
            'contact_messages.update' => 'تعديل رسائل التواصل',
            'contact_messages.delete' => 'حذف رسائل التواصل',

            // Tenant
            'tenant.update' => 'تعديل إعدادات المنصة',

            // Team Members
            'team_member.view' => 'عرض أعضاء الفريق',
            'team_member.create' => 'إضافة عضو فريق',
            'team_member.update' => 'تعديل عضو الفريق',
            'team_member.delete' => 'حذف عضو الفريق',

            // Tasks
            'task.view' => 'عرض المهام',
            'task.create' => 'إنشاء مهمة',
            'task.update' => 'تعديل المهمة',
            'task.delete' => 'حذف المهمة',

            // Ads
            'ads.view' => 'عرض الإعلانات',
            'ads.create' => 'إضافة إعلان',
            'ads.update' => 'تعديل الإعلان',
            'ads.delete' => 'حذف الإعلان',

            // activityLogs
            'activityLogs.view' => 'عرض سجل انشطة الموظفين',
        ];

        foreach ($permissions as $name => $displayName) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'api'],
                ['display_name' => $displayName]
            );
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
