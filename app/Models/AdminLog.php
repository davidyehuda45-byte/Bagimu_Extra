<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $fillable = [
        'admin_id',
        'admin_email',
        'ip_address',
        'user_agent',
        'actor_role',
        'action',
        'target_type',
        'target_id',
        'detail',
        'status',
    ];

    public static function record(
        string $action,
        ?string $targetType = null,
        string|int|null $targetId = null,
        ?string $detail = null,
        ?string $status = 'SUCCESS',
        ?string $forcedEmail = null,
        string|int|null $forcedId = null,
        ?string $forcedRole = null
    ): self {
        $user = auth()->user();
        $ip = request()?->ip();
        $userAgent = request()?->userAgent();

        $role = $forcedRole ?? ($user ? ($user->role?->value ?? (string) $user->role) : 'GUEST');
        $adminId = $forcedId ?? ($user?->getAuthIdentifier() ?? 'system');
        $adminEmail = $forcedEmail ?? ($user?->email ?? 'system');

        return self::create([
            'admin_id' => (string) $adminId,
            'admin_email' => (string) $adminEmail,
            'ip_address' => $ip,
            'user_agent' => $userAgent ? substr($userAgent, 0, 500) : null,
            'actor_role' => $role,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId !== null ? (string) $targetId : null,
            'detail' => $detail,
            'status' => $status ?? 'SUCCESS',
        ]);
    }
}
