<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorySidang extends Model
{
    use HasFactory;
    protected $table = 'history_sidangs';

    public function historyMahasiswa()
    {
        return $this->belongsTo(HistoryMahasiswa::class, 'history_mahasiswa_id');
    }
    public function pembimbingsatu()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_1');
    }

    public function pembimbingdua()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_2');
    }

    public function pengujisatu()
    {
        return $this->belongsTo(Dosen::class, 'penguji_1');
    }

    public function pengujidua()
    {
        return $this->belongsTo(Dosen::class, 'penguji_2');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function konsentrasi()
    {
        return $this->belongsTo(Konsentrasi::class, 'konsentrasi_id');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }
}
