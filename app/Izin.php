<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mahasiswa extends Model
{

    protected $guarded = [];

    protected $table = 'izin';

    public function absen(){
        return $this->belongsTo(Absen::class);
    }

    public function mahasiswa(){
        return $this->belongsTo(Mahasiswa::class);
    }
}
