<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

    public function index()
    {
        $user = Auth::user(); // ambil user yang login

        return view('kasir.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $user->name = $request->name;

        if($request->password){
            $user->password = Hash::make($request->password);
        }

        if($request->hasFile('foto')){

            $file = $request->file('foto');
            $nama = time().'.'.$file->getClientOriginalExtension();

            $file->storeAs('public/foto', $nama);

            $user->foto = $nama;
        }

        $user->save();

        return back()->with('success','Profil berhasil diupdate');
    }

}