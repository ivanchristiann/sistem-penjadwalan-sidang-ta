<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    public function sidang(){
        return $this->hasOne(Sidang::class, 'mahasiswa_id', 'id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function historymahasiswa(){
        return $this->hasMany(HistoryMahasiswa::class, 'mahasiswa_id', 'id');
    }
}
