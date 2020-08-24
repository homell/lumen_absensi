<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoriController extends Controller
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

	// Tidak memahami kondisi where clause ". $nim ." ". $matakuliah.". Butuh Penjelasan!!
	// Berhasil menghasilkan data yang diinginkan. namun masih butuh penjelasan tentang yang diatas!!
    public function histori(Request $request) {

		$nrp 			= $request->get('nrp');
		$matakuliah		= $request->get('matakuliah');
		$tanggalsatu 	= $request->get('tanggalsatu');
		$tanggaldua		= $request->get('tanggaldua');

		$q_matakuliah = '';
		if( isset($matakuliah) ) {
			$q_matakuliah = DB::table('matakuliah')->select('matakuliah')
					->where('matakuliah', $matakuliah)->get();
		}

		//Tanggal 1
		$q_tanggal = '';
		// Tanggal 2
		if( isset($tanggalsatu)) {
			// $q_tanggal = " and tb_absen.tanggal = '$tanggalsatu' ";
			$q_tanggal = DB::table('absensi_mahasiswa')->select('tanggal')
						->where('tanggal', $tanggalsatu)->get();
		}

		if( isset($tanggalsatu) && isset($tanggaldua) ) {
			// $q_tanggal = " and tb_absen.tanggal between '$tanggalsatu' and '$tanggaldua' ";
			$q_tanggal = DB::table('absensi_mahasiswa')->select('tanggal')
						->whereBetween('tanggal', [$tanggalsatu, $tanggaldua])->get();
		}

		$data_histori = DB::table('absensi_mahasiswa')
			->join('kuliah', 'kuliah.nomor', 'absensi_mahasiswa.kuliah')
			->join('datamahasiswa', 'datamahasiswa.nomor', 'absensi_mahasiswa.mahasiswa')
			->join('kelas', 'kelas.nomor', 'datamahasiswa.kelas')
			// ->join('kuliah', 'kuliah.kelas', 'kelas.nomor')
			->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
			->select('datamahasiswa.nrp', 'datamahasiswa.nama', 'absensi_mahasiswa.status',
					'matakuliah.matakuliah', 
					DB::raw("date_format(absensi_mahasiswa.tanggal, '%d-%m-%Y') as hari"))
			->where('datamahasiswa.nrp', [$nrp , "'$q_matakuliah'" , "'$q_tanggal'"])
			// ->where('matakuliah.matakuliah', $q_matakuliah)
			->orderBy('absensi_mahasiswa.tanggal', 'DESC')
			->get();
		return response()->json([
			'status'	=> 200,
			'result'	=> $data_histori,
		], 200);
	}
}