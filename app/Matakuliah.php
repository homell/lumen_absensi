<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mahasiswa extends Model
{

    protected $guarded = [];
    
    protected $table = 'matakuliah';

    public function jadwal(){
        return $this->hasMany(Jadwal::class);
    }

}
