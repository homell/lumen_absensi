<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FiltermatkulController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * BERHASIL UJI COBA, API SUKSES DIBUAT.
     */
    public function filtermatkul(Request $request)
    {
        $nrp = $request->get('nim');
        $data_matkul = DB::table('kuliah')
			->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
			->join('ruang', 'ruang.nomor', 'kuliah.ruang')
			->join('kelas', 'kelas.nomor', 'kuliah.kelas')
			->join('datamahasiswa', 'datamahasiswa.kelas', 'kelas.nomor')
			->select('matakuliah.matakuliah', 'kelas.kelas', 'ruang.keterangan as ruangan',
					'kuliah.kehadiran as status', 'matakuliah.kode as kodemk')
			->where('datamahasiswa.nrp', $nrp)
			->get();
        return response()->json([
            'result' => $data_matkul,
        ], 200);

    }
    
}
