<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Achievement;
use App\Models\Eskul;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function store(Request $request, Eskul $eskul)
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:200'],
            'tingkat' => ['nullable', 'string', 'max:50'],
            'tanggal' => ['nullable', 'date'],
            'peserta' => ['nullable', 'string', 'max:500'],
        ]);

        $prestasi = Achievement::create([
            'eskul_id' => $eskul->id,
            ...$data,
        ]);

        AdminLog::record('achievements.store', 'Achievement', (string) $prestasi->id, "Tambah prestasi {$prestasi->judul} ({$eskul->name}).");

        return back()->with('status', 'Prestasi ditambahkan.');
    }

    public function destroy(Achievement $achievement)
    {
        $judul = $achievement->judul;
        $achievement->delete();

        AdminLog::record('achievements.destroy', 'Achievement', (string) $achievement->id, "Hapus prestasi {$judul}.");

        return back()->with('status', 'Prestasi dihapus.');
    }
}
