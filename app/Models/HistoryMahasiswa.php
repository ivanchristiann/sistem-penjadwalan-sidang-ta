<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryMahasiswa extends Model
{
    use HasFactory;
    protected $table = 'history_mahasiswas';

    public function historySidang()
    {
        return $this->hasOne(HistorySidang::class, 'history_mahasiswa_id', 'id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}
