<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    public function sidangs(){
        return $this->hasMany(Sidang::class, 'ruangan_id', 'id');
    }

    public function historysidang()
    {
        return $this->hasMany(HistorySidang::class, 'ruangan_id', 'id');
    }
}
