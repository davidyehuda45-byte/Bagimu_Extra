<?php

namespace App\Models;

use App\Enums\NotifChannel;
use App\Enums\NotifStatus;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'channel',
        'to',
        'template',
        'payload',
        'status',
        'attempts',
        'provider_resp',
        'dedupe_key',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'channel' => NotifChannel::class,
            'status' => NotifStatus::class,
            'sent_at' => 'datetime',
        ];
    }
}
