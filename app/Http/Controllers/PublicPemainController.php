<?php

namespace App\Http\Controllers;

use App\Models\PemainBola;
use App\Models\SekolahBola;
use Illuminate\Http\Request;

class PublicPemainController extends Controller
{
    public function create()
    {
        $sekolahBolas = SekolahBola::all(); 
        return view('pemain-public.create', compact('sekolahBolas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sekolah_bola_id' => 'required|exists:sekolah_bolas,id',
            'nama' => 'required|string|max:255',
            'umur_kategori' => 'required|string',
        ]);

        PemainBola::create([
            'sekolah_bola_id' => $request->sekolah_bola_id,
            'nama' => $request->nama,
            'umur_kategori' => $request->umur_kategori,
        ]);

        return redirect()->back()->with('success', 'Pendaftaran berhasil!');
    }
}
