<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ItemKondisi;
use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Eskul;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $eskuls = Eskul::orderBy('name')->get();

        $query = InventoryItem::with('eskul')->latest();

        if ($request->filled('eskul_id')) {
            $query->where('eskul_id', $request->query('eskul_id'));
        }

        return view('admin.inventory.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'eskuls' => $eskuls,
            'filterEskul' => $request->query('eskul_id', ''),
        ]);
    }

    public function create()
    {
        return view('admin.inventory.create', [
            'eskuls' => Eskul::orderBy('name')->get(),
            'kondisiList' => ItemKondisi::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'eskul_id' => ['nullable', 'exists:eskuls,id'],
            'nama' => ['required', 'string', 'max:100'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'kondisi' => ['required', 'in:BAIK,RUSAK_RINGAN,RUSAK_BERAT,HILANG'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $item = InventoryItem::create($data);

        AdminLog::record('inventory.store', 'InventoryItem', (string) $item->id, "Tambah barang {$item->nama} ({$item->jumlah}).");

        return redirect()->route('admin.inventory.index')->with('status', 'Barang inventaris ditambahkan.');
    }

    public function show(InventoryItem $inventory)
    {
        $inventory->load(['eskul', 'loans']);

        return view('admin.inventory.show', [
            'item' => $inventory,
            'loans' => $inventory->loans()->latest()->get(),
        ]);
    }

    public function edit(InventoryItem $inventory)
    {
        return view('admin.inventory.edit', [
            'item' => $inventory,
            'eskuls' => Eskul::orderBy('name')->get(),
            'kondisiList' => ItemKondisi::cases(),
        ]);
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $data = $request->validate([
            'eskul_id' => ['nullable', 'exists:eskuls,id'],
            'nama' => ['required', 'string', 'max:100'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'kondisi' => ['required', 'in:BAIK,RUSAK_RINGAN,RUSAK_BERAT,HILANG'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $inventory->update($data);

        AdminLog::record('inventory.update', 'InventoryItem', (string) $inventory->id, "Ubah barang {$inventory->nama}.");

        return redirect()->route('admin.inventory.index')->with('status', 'Barang inventaris diperbarui.');
    }

    public function destroy(InventoryItem $inventory)
    {
        $nama = $inventory->nama;
        $inventory->delete();

        AdminLog::record('inventory.destroy', 'InventoryItem', (string) $inventory->id, "Hapus barang {$nama}.");

        return redirect()->route('admin.inventory.index')->with('status', 'Barang inventaris dihapus.');
    }
}
