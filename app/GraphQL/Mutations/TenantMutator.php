<?php

namespace App\GraphQL\Mutations;

use App\Models\Tenant;

class TenantMutator
{

    public function current($_, array $args)
    {
        $tenant = tenant();
        if (!$tenant) {
            throw new \Exception('Tenant not initialized or not found.');
        }
        $tenantRecord = Tenant::find($tenant->id);
        if (!$tenantRecord) {
            throw new \Exception('Tenant record not found.');
        }
        $data = $tenantRecord->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at'], $data['data']);
        return $data;
    }

    public function updateData($_, array $args)
    {
        $tenant = tenant();
        if (!$tenant) {
            throw new \Exception('Tenant not initialized or not found.');
        }
        if (empty($args['data']) || !is_array($args['data'])) {
            throw new \Exception('Invalid or missing "data" field. Must be a valid JSON object.');
        }
        Tenant::where('id', $tenant->id)->update([
            'data' => $args['data'],
        ]);
        $updatedTenant = Tenant::find($tenant->id);
        return $updatedTenant->toArray();
    }
}
