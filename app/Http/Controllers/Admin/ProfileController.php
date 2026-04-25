<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

public function index()
{
    $user = Auth::user();
    return view('admin.profile.index', compact('user'));
}

public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required',
        'password' => 'nullable|min:5',
        'foto' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
    ]);

    $user->name = $request->name;

    if($request->password){
        $user->password = Hash::make($request->password);
    }

    if($request->hasFile('foto')){

        $file = $request->file('foto');
        $namaFile = time().'.'.$file->getClientOriginalExtension();
        $file->storeAs('public/foto',$namaFile);

        $user->foto = $namaFile;
    }

    $user->save();

    return redirect()->back()->with('success','Profil berhasil diperbarui');
}

}