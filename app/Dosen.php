<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    // protected $fillable = [
    //     'dsn_id', 'nama', 'telp', 'username','password', 'token'
    // ];

    // protected $hidden = [
    //     'password', 'token'
    // ];

    protected $guarded = [];
    protected $table = 'dosen';



    public function jadwal(){
        return $this->hasMany(Jadwal::class);
    }
}
