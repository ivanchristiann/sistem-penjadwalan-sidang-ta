<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use App\Models\TanggalMerah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TanggalMerahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
     * @param  \App\Models\Jadwalkosong  $jadwalkosong
     * @return \Illuminate\Http\Response
     */
    public function show(TanggalMerah $tanggalMerah)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Jadwalkosong  $jadwalkosong
     * @return \Illuminate\Http\Response
     */
    public function edit(TanggalMerah $tanggalMerah)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Jadwalkosong  $jadwalkosong
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TanggalMerah $tanggalMerah)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Jadwalkosong  $jadwalkosong
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // try {
        //     $tanggalMerah = TanggalMerah::find($request->get('idTanggal'));
        //     $tanggalMerah->delete();
        //     return response()->json(array('status' => 'success'), 200);
        // } catch (\PDOException $e) {
        //     return response()->json(array('status' => 'danger'), 200);
        // }
    }

    public static function getHoliday($periode)
    {
        $tanggalMerah = DB::table('tanggal_merahs')->select(DB::raw('DAY(tanggal) AS tanggal'))->where('periode_id', '=', $periode)->get()->pluck('tanggal')->toArray();
        return $tanggalMerah;
    }

    public static function getDay($periode)
    {
        $tanggal = Periode::select('tanggal_mulai', 'tanggal_berakhir')->where('id', $periode)->first();
        $tanggalMulai = strtotime($tanggal->tanggal_mulai);
        $tanggalBerakhir = strtotime($tanggal->tanggal_berakhir);

        $arrayTanggal = [];
        while ($tanggalMulai <= $tanggalBerakhir) {
            $arrayTanggal[] = date('l, j-F-Y', $tanggalMulai);
            $tanggalMulai = strtotime('+1 day', $tanggalMulai);
        }
        return $arrayTanggal;
    }

    public function hapusTanggalMerah(Request $request){
        try {
            $tanggalMerah = TanggalMerah::find($request->get('id'));
            $tanggalMerah->delete();
            return response()->json(array('status' => 'success'), 200);
        } catch (\PDOException $e) {
            return response()->json(array('status' => 'danger'), 200);
        }
    }
}
