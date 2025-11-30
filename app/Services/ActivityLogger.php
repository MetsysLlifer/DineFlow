<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Events\ActivityLogged;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, ?string $subjectType = null, ?int $subjectId = null, array $metadata = []): void
    {
        try {
            $log = ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'metadata' => $metadata,
            ]);
            event(new ActivityLogged($log));
        } catch (\Throwable $e) {
            // Silently fail to avoid breaking primary flow
        }
    }
}
