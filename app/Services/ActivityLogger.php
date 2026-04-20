<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Contracts\Auth\Authenticatable;

final class ActivityLogger
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public static function log(string $action, ?Authenticatable $user = null, ?string $subjectType = null, ?int $subjectId = null, array $properties = []): void
    {
        ActivityLog::query()->create([
            'user_id' => $user?->getAuthIdentifier(),
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'properties' => $properties ?: null,
        ]);
    }
}
