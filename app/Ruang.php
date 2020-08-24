<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mahasiswa extends Model
{

    protected $guarded = [];
    // protected $fillable = [
    //      'ruang_id', 'kode_ruang', 'nama'
    // ];

    protected $table = 'ruang';



    public function jadwal(){
        return $this->hasMany(Jadwal::class);
    }
}
