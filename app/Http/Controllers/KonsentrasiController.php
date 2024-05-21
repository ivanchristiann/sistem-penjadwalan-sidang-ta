<?php

namespace App\Http\Controllers;

use App\Models\Konsentrasi;
use App\Models\Sidang;
use Illuminate\Http\Request;

class KonsentrasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $konsentrasiAktif = Konsentrasi::select('id', 'nama')->where('status', '1')->get();
        $konsentrasiNonaktif = Konsentrasi::select('id', 'nama')->where('status', '0')->get();

        return view('paj.konsentrasi.index', compact('konsentrasiAktif', 'konsentrasiNonaktif'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('paj.konsentrasi.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(['nama' => 'required|max:45|unique:konsentrasis'], ['nama.required' => 'Nama konsentrasi tidak boleh kosong!', 'nama.unique' => 'Gagal menambahkan konsentrasi Baru! Konsentrasi dengan nama ' . $request->get('nama') . ' telah digunakan!']);
        $cekkonsentrasi = Konsentrasi::where('nama', $request->get('nama'))->get();
        if (count($cekkonsentrasi) == 0) {
            $data = new Konsentrasi();
            $data->nama = $request->get('nama');

            $data->save();
            return redirect()->route('konsentrasi.index')->with('status', 'Berhasil menambahkan konsentrasi Baru!');
        } else {
            return redirect()->route('konsentrasi.index')->with('status', 'Gagal menambahkan konsentrasi Baru!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Konsentrasi  $konsentrasi
     * @return \Illuminate\Http\Response
     */
    public function show(Konsentrasi $konsentrasi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Konsentrasi  $konsentrasi
     * @return \Illuminate\Http\Response
     */
    public function edit(Konsentrasi $konsentrasi)
    {
        return view('paj.konsentrasi.edit', compact('konsentrasi'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Konsentrasi  $konsentrasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Konsentrasi $konsentrasi)
    {
        if ($konsentrasi->nama != $request->get('nama')) {
            $cekkonsentrasi = Konsentrasi::where('nama', $request->get('nama'))->get();
            if (count($cekkonsentrasi) == 0) {
                $konsentrasi->nama = $request->get('nama');
                $konsentrasi->save();

                return redirect()->route('konsentrasi.index')->with('status', 'Berhasil mengedit konsentrasi ' . $request->get('nama') . '!');
            } else {
                return redirect()->route('konsentrasi.index')->with('status', 'Gagal mengedit konsentrasi ' . $request->get('nama') . '! Nama konsentrasi telah digunakan!');
            }
        } else {
            $konsentrasi->nama = $request->get('nama');
            $konsentrasi->save();

            return redirect()->route('konsentrasi.index')->with('status', 'Berhasil mengedit konsentrasi ' . $request->get('nama') . '!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Konsentrasi  $konsentrasi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Konsentrasi $konsentrasi)
    {
        //
    }

    public function nonaktifkan(Request $request)
    {
        $konsentrasiId = $request->get('id');
        $cekSidang = Sidang::select('id')->where('konsentrasi_id', $konsentrasiId)->count();
        if ($cekSidang == 0) {
            $data = Konsentrasi::find($konsentrasiId);
            $data->status = '0';
            $data->save();

            return response()->json(array('status' => 'success'), 200);
        } else {
            return response()->json(array('status' => 'fail'), 200);
        }
    }

    public function aktifkan(Request $request)
    {
        $data = Konsentrasi::find($request->get('id'));
        $data->status = '1';
        $data->save();

        return response()->json(array('status' => 'success'), 200);
    }
}
