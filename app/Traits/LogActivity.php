<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogActivity
{
    protected function log($userId, string $action, string $modelType, $modelId, $old = null, $new = null): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => ucfirst($action) . ' ' . $modelType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }
}
