<?php

namespace App\Support;

use App\Models\ActivityLog;

class ActivityLogger
{
    /**
     * Hii function inarekodi matukio yote muhimu yanayotokea kwenye system.
     */
    public static function log(string $action, $user, string $modelClass, int $modelId, array $data = []): void
    {
        ActivityLog::create([
            'user_id'    => $user?->id, // Inaweza kuwa null kama ni mteja anabook (Guest)
            'action'     => $action,
            'model_type' => $modelClass,
            'model_id'   => $modelId,
            'data'       => $data,
        ]);
    }
}
