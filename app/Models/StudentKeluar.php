<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentKeluar extends Model
{
    protected $table = 'students_keluar';

    protected $fillable = [
        'student_id',
        'nis',
        'nisn',
        'nama',
        'kelas_terakhir',
        'jurusan',
        'whatsapp',
        'status_keluar',
        'alasan',
        'tanggal_keluar',
        'diproses_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_keluar' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
