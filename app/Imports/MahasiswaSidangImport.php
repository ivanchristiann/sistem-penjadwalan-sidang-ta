<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\Konsentrasi;
use App\Models\Mahasiswa;
use App\Models\Periode;
use App\Models\Sidang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MahasiswaSidangImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach($collection as $row){
            $mahasiswa = new Mahasiswa();
            $mahasiswa->nrp = $row['nrp'];
            $mahasiswa->nama = $row['nama'];
            $mahasiswa->save();

            $mhsId = $mahasiswa->id;

            $dosen1 = Dosen::select('id')->where('npk', $row['npk_pembimbing_1'])->get();
            $dosen2 = Dosen::select('id')->where('npk', $row['npk_pembimbing_2'])->get();
            $konsentrasi = Konsentrasi::select('id')->where('nama', $row['peminatan'])->get();
            $periode = Periode::select('id')->where('status', 'aktif')->get();
            $sidang = new Sidang();
            $sidang->mahasiswa_id = $mhsId;
            $sidang->judul = $row['judul'];
            $sidang->pembimbing_1 = $dosen1[0]['id'];
            $sidang->pembimbing_2 = $dosen2[0]['id'];
            $sidang->konsentrasi_id = $konsentrasi[0]['id'];
            $sidang->periode_id = $periode[0]['id'];
            $sidang->save();
        }
    }
}
