<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\PenggunaExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        return view('admin.data_pengguna.index', compact('users'));
    }

    public function create()
    {
        return view('admin.data_pengguna.create');
    }
    
    public function update(Request $request, $id)
{

    $user = User::findOrFail($id);

    $user->update([
        'name'=>$request->name,
        'email'=>$request->email,
        'role'=>$request->role
    ]);

    return redirect()->back()->with('success','Data pengguna berhasil diupdate');

}

    public function store(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('pengguna.index')
        ->with('success','Data pengguna berhasil ditambahkan');
    }

    public function excel()
{
    return Excel::download(new PenggunaExport, 'data_pengguna.xlsx');
}

public function pdf()
{
    $users = User::all();

    $pdf = Pdf::loadView('admin.data_pengguna.pdf', compact('users'));

    return $pdf->download('data_pengguna.pdf');
}

}