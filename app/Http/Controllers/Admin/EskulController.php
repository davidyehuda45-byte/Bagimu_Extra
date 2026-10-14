<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;

class EskulController extends Controller
{
    public function index()
    {
        $eskuls = Eskul::with(['pembina', 'jadwals'])->orderBy('name')->paginate(15);

        return view('admin.eskuls.index', compact('eskuls'));
    }

    public function create()
    {
        $pembinas = User::where('role', Role::PEMBINA)->orderBy('name')->get();

        return view('admin.eskuls.create', compact('pembinas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:eskuls,name'],
            'description' => ['nullable', 'string'],
            'kuota' => ['nullable', 'integer', 'min:0'],
            'kuota_aktif' => ['nullable', 'boolean'],
            'aktif' => ['nullable', 'boolean'],
            'pembina_id' => ['nullable', 'exists:users,id'],
        ]);

        $data['kuota_aktif'] = $request->boolean('kuota_aktif');
        $data['aktif'] = $request->boolean('aktif');
        if (empty($data['pembina_id'])) {
            $data['pembina_id'] = null;
        }

        $eskul = Eskul::create($data);

        AdminLog::record('eskul.create', 'eskul', $eskul->id, "Membuat eskul {$eskul->name}");

        return redirect()->route('admin.eskuls.show', $eskul)->with('status', 'Eskul berhasil dibuat.');
    }

    public function show(Eskul $eskul)
    {
        $eskul->load(['pembina', 'tempPembina', 'jadwals']);

        $pendaftar = Registration::selectRaw('status, COUNT(*) as total')
            ->where('eskul_utama', $eskul->name)
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $hariList = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU', 'MINGGU'];

        $pembinas = User::where('role', Role::PEMBINA)->orderBy('name')->get();

        return view('admin.eskuls.show', compact('eskul', 'pendaftar', 'hariList', 'pembinas'));
    }

    public function delegasi(Request $request, Eskul $eskul)
    {
        $data = $request->validate([
            'temp_pembina_id' => ['nullable', 'exists:users,id'],
            'temp_pembina_sampai' => ['nullable', 'date'],
        ]);

        $eskul->update([
            'temp_pembina_id' => $data['temp_pembina_id'] ?? null,
            'temp_pembina_sampai' => $data['temp_pembina_sampai'] ?? null,
        ]);

        AdminLog::record('eskul.delegasi', 'eskul', (string) $eskul->id, "Delegasi sementara eskul {$eskul->name}.");

        return back()->with('status', 'Delegasi sementara disimpan.');
    }

    public function edit(Eskul $eskul)
    {
        $pembinas = User::where('role', Role::PEMBINA)->orderBy('name')->get();

        return view('admin.eskuls.edit', compact('eskul', 'pembinas'));
    }

    public function update(Request $request, Eskul $eskul)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:eskuls,name,'.$eskul->id],
            'description' => ['nullable', 'string'],
            'kuota' => ['nullable', 'integer', 'min:0'],
            'kuota_aktif' => ['nullable', 'boolean'],
            'aktif' => ['nullable', 'boolean'],
            'pembina_id' => ['nullable', 'exists:users,id'],
        ]);

        $data['kuota_aktif'] = $request->boolean('kuota_aktif');
        $data['aktif'] = $request->boolean('aktif');
        if (empty($data['pembina_id'])) {
            $data['pembina_id'] = null;
        }

        $eskul->update($data);

        AdminLog::record('eskul.update', 'eskul', $eskul->id, "Mengubah eskul {$eskul->name}");

        return redirect()->route('admin.eskuls.show', $eskul)->with('status', 'Eskul berhasil diperbarui.');
    }

    public function destroy(Eskul $eskul)
    {
        // Ditolak bila masih ada pendaftar — disarankan nonaktifkan saja.
        $adaPendaftar = Registration::where('eskul_utama', $eskul->name)
            ->orWhere('eskul_cadangan', $eskul->name)
            ->exists();

        if ($adaPendaftar) {
            return back()->withErrors(['eskul' => "Eskul {$eskul->name} masih memiliki pendaftar. Nonaktifkan saja bila tidak dipakai."]);
        }

        $nama = $eskul->name;
        $eskul->delete();

        AdminLog::record('eskul.delete', 'eskul', $eskul->id, "Menghapus eskul {$nama}");

        return redirect()->route('admin.eskuls.index')->with('status', 'Eskul berhasil dihapus.');
    }
}
