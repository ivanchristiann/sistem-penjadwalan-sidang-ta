<?php

namespace App\Http\Controllers;

use App\Models\HistoryMahasiswa;
use App\Models\HistorySidang;
use App\Models\Periode;
use App\Models\Sidang;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HistoryMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($idPeriode)
    {
        $historySidangs = HistorySidang::where('periode_id', $idPeriode)->get();
        $periode = Periode::where('id', $idPeriode)->first();
        $bulan = Carbon::parse($periode->tanggal_mulai)->isoFormat('MMMM Y');
        $tanggalMulai = Carbon::parse($periode->tanggal_mulai)->isoFormat('dddd, D MMMM Y');
        $tanggalBerakhir = Carbon::parse($periode->tanggal_berakhir)->isoFormat('dddd, D MMMM Y');

        return view('paj.periode.historymahasiswa', compact('historySidangs', 'periode', 'bulan', 'tanggalMulai', 'tanggalBerakhir'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HistoryMahasiswa  $historyMahasiswa
     * @return \Illuminate\Http\Response
     */
    public function show(HistoryMahasiswa $historyMahasiswa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HistoryMahasiswa  $historyMahasiswa
     * @return \Illuminate\Http\Response
     */
    public function edit(HistoryMahasiswa $historyMahasiswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HistoryMahasiswa  $historyMahasiswa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HistoryMahasiswa $historyMahasiswa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HistoryMahasiswa  $historyMahasiswa
     * @return \Illuminate\Http\Response
     */
    public function destroy(HistoryMahasiswa $historyMahasiswa)
    {
        //
    }

    
}
