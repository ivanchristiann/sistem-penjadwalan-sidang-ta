<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Sidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class RuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ruanganAktif = Ruangan::select('id', 'nama')->where('status', '1')->get();
        $ruanganNonaktif = Ruangan::select('id', 'nama')->where('status', '0')->get();

        return view('paj.ruangan.index', compact('ruanganAktif', 'ruanganNonaktif'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('paj.ruangan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(['nama' => 'required|max:45'], ['nama.required' => 'Nama ruangan tidak boleh kosong!']);
        $cekruangan = Ruangan::where('nama', $request->get('nama'))->get();
        if (count($cekruangan) == 0) {
            $data = new Ruangan();
            $data->nama = $request->get('nama');
            $data->save();
            return redirect()->route('ruangan.index')->with('status', 'Berhasil menambahkan ruang Sidang Baru!');
        }
        return redirect()->route('ruangan.index')->with('status', 'Gagal menambahkan ruang Sidang Baru! Nama ruangan telah digunakan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ruangan  $ruangan
     * @return \Illuminate\Http\Response
     */
    public function show(Ruangan $ruangan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ruangan  $ruangan
     * @return \Illuminate\Http\Response
     */
    public function edit(Ruangan $ruangan)
    {
        return view('paj.ruangan.edit', compact('ruangan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ruangan  $ruangan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ruangan $ruangan)
    {
        if ($ruangan->nama != $request->get('nama')) {
            $cekruangan = Ruangan::where('nama', $request->get('nama'))->get();
            if (count($cekruangan) == 0) {
                $ruangan->nama = $request->get('nama');
                $ruangan->save();

                return redirect()->route('ruangan.index')->with('status', 'Berhasil mengedit ruangan ' . $request->get('nama') . '!');
            } else {
                return redirect()->route('ruangan.index')->with('status', 'Gagal mengedit ruangan ' . $request->get('nama') . '! Nama ruangan telah digunakan!');
            }
        } else {
            $ruangan->nama = $request->get('nama');
            $ruangan->save();

            return redirect()->route('ruangan.index')->with('status', 'Berhasil mengedit ruangan ' . $request->get('nama') . '!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ruangan  $ruangan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ruangan $ruangan)
    {
        
    }

    public function nonaktifkan(Request $request)
    {
        $cekruangan = Sidang::where('ruangan_id', $request->get('id'))->get();
        if (count($cekruangan) == 0) {
            $data = Ruangan::find($request->get('id'));
            $data->status = '0';
            $data->save();
            return response()->json(array('status' => 'success'), 200);
        } else {
            return response()->json(array('status' => 'danger'), 200);
        }
    }

    public function aktifkan(Request $request)
    {
        $data = Ruangan::find($request->get('id'));
        $data->status = '1';
        $data->save();

        return response()->json(array('status' => 'success'), 200);
    }
}
