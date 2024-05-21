<?php

namespace App\Models;

use App\Mail\SendMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Dosen extends Model
{
    use HasFactory;
    public function konsentrasis(){
        return $this->belongsToMany('App\Models\Konsentrasi', 'dosen_konsentrasi', 'dosen_id', 'konsentrasi_id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function pembimbingsatus(){
        return $this->hasMany(Sidang::class, 'pembimbing_1', 'id');
    }

    public function pembimbingduas(){
        return $this->hasMany(Sidang::class, 'pembimbing_2', 'id');
    }

    public function pengujisatus(){
        return $this->hasMany(Sidang::class, 'penguji_1', 'id');
    }

    public function pengujiduas(){
        return $this->hasMany(Sidang::class, 'penguji_2', 'id');
    }

    public function pembimbingsatu(){
        return $this->hasMany(HistorySidang::class, 'pembimbing_1', 'id');
    }

    public function pembimbingdua(){
        return $this->hasMany(HistorySidang::class, 'pembimbing_2', 'id');
    }

    public function pengujisatu(){
        return $this->hasMany(HistorySidang::class, 'penguji_1', 'id');
    }

    public function pengujidua(){
        return $this->hasMany(HistorySidang::class, 'penguji_2', 'id');
    }
}
