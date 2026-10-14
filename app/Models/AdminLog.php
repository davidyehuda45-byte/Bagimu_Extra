<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $fillable = [
        'admin_id',
        'admin_email',
        'action',
        'target_type',
        'target_id',
        'detail',
    ];

    public static function record(
        string $action,
        ?string $targetType = null,
        string|int|null $targetId = null,
        ?string $detail = null,
    ): self {
        $user = auth()->user();

        return self::create([
            'admin_id' => $user?->getAuthIdentifier() ?? 'system',
            'admin_email' => $user?->email ?? 'system',
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId !== null ? (string) $targetId : null,
            'detail' => $detail,
        ]);
    }
}
