<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLoan extends Model
{
    protected $fillable = [
        'item_id',
        'peminjam',
        'jumlah',
        'tgl_pinjam',
        'tgl_kembali',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pinjam' => 'date',
            'tgl_kembali' => 'date',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'DIPINJAM';
    }
}
