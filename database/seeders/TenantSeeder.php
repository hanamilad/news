<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    public function run()
    {
        $tenant = Tenant::create([
            'id' => 'site1', 
            'name' => 'site1',
        ]);

        $tenant->domains()->create([
            'domain' => 'site1.localhost',
        ]);

        $adminUser = User::first();

        if ($adminUser) {
            $adminUser->tenant_id = $tenant->id;
            $adminUser->save();
        }

        $this->command->info("✅ Tenant 'site1' created with domain 'site1.localhost' and linked to first user.");
    }
}
