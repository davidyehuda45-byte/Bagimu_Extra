<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\InventoryItem;
use App\Models\InventoryLoan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function store(Request $request, InventoryItem $item)
    {
        $data = $request->validate([
            'peminjam' => ['required', 'string', 'max:100'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tgl_pinjam' => ['required', 'date'],
        ]);

        $terpinjam = $item->loans()->where('status', 'DIPINJAM')->sum('jumlah');
        $tersedia = max(0, (int) $item->jumlah - (int) $terpinjam);

        if ($data['jumlah'] > $tersedia) {
            return back()->withErrors(['jumlah' => "Stok tersedia hanya {$tersedia}."]);
        }

        $loan = InventoryLoan::create([
            'item_id' => $item->id,
            'peminjam' => $data['peminjam'],
            'jumlah' => $data['jumlah'],
            'tgl_pinjam' => $data['tgl_pinjam'],
            'status' => 'DIPINJAM',
        ]);

        AdminLog::record('loans.store', 'InventoryLoan', (string) $loan->id, "Pinjam {$item->nama} x{$loan->jumlah} oleh {$loan->peminjam}.");

        return back()->with('status', 'Peminjaman dicatat.');
    }

    public function kembali(InventoryLoan $loan)
    {
        $loan->update([
            'tgl_kembali' => today(),
            'status' => 'KEMBALI',
        ]);

        AdminLog::record('loans.kembali', 'InventoryLoan', (string) $loan->id, "Pengembalian pinjaman #{$loan->id}.");

        return back()->with('status', 'Barang ditandai sudah kembali.');
    }
}
