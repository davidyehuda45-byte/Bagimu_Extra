<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class KartuController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->hasRole(Role::KEPALA_SEKOLAH)) {
            return redirect()->route('admin.dashboard')->with('error', 'Kepala sekolah tidak memiliki akses Kartu Login.');
        }

        $q = trim($request->query('q', ''));

        $kartu = Student::with('user')
            ->whereHas('user', fn ($u) => $u->where('role', Role::STUDENT))
            ->when($q !== '', fn ($qq) => $qq->where(fn ($w) => $w
                ->where('nama', 'like', "%{$q}%")
                ->orWhere('nis', 'like', "%{$q}%")))
            ->orderBy('nis')
            ->paginate(50)
            ->withQueryString();

        return view('admin.kartu.index', compact('kartu', 'q'));
    }
}
