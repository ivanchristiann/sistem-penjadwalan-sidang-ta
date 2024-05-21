<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konsentrasi extends Model
{
    use HasFactory;
    public function dosens(){
        return $this->belongsToMany('App\Models\Dosen', 'dosen_konsentrasi', 'konsentrasi_id', 'dosen_id');
    }

    public function sidangs(){
        return $this->hasMany(Sidang::class, 'konsentrasi_id', 'id');
    }

    public function historysidang(){
        return $this->hasMany(HistorySidang::class, 'konsentrasi_id', 'id');
    }
}
