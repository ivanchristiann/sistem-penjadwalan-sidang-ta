<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    public function sidangs(){
        return $this->hasMany(Sidang::class, 'periode_id', 'id');
    }

    public function historysidang(){
        return $this->hasMany(HistorySidang::class, 'periode_id', 'id');
    }

    public function tanggalmerah(){
        return $this->hasMany(TanggalMerah::class, 'periode_id', 'id');
    }
}
