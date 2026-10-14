<?php

namespace App\Models;

use App\Enums\ItemKondisi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'eskul_id',
        'nama',
        'jumlah',
        'kondisi',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'kondisi' => ItemKondisi::class,
        ];
    }

    public function eskul(): BelongsTo
    {
        return $this->belongsTo(Eskul::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(InventoryLoan::class, 'item_id');
    }
}
