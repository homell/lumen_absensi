<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CobaController extends Controller
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

    public function coba(Request $request) {
        $nrp 	   	= $request->post('nrp');
		$kodeqr 	= $request->post('kodeqr');
		
		date_default_timezone_set('Asia/Jakarta');
		
		$tanggal	=  date("Y-m-d"); 
		$jam		=  date("H:i:s");
		$data_jadwal = DB::table('kuliah')
			->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
			->join('ruang', 'ruang.nomor', 'kuliah.ruang')
			->join('kelas', 'kelas.nomor', 'kuliah.kelas')
			->join('datamahasiswa', 'datamahasiswa.kelas', 'kelas.nomor')
			->select('datamahasiswa.nomor', 'kuliah.nomor as kuliah_id', 'datamahasiswa.nrp',
					'matakuliah.matakuliah', 'kelas.kelas', 'ruang.keterangan', 'matakuliah.kode')
			->where('datamahasiswa.nrp', $nrp)
			->where('matakuliah.kode',  $kodeqr)
			// ->whereNull('matakuliah.kode', $kodeqr)
			->get();

			// return $data_jadwal;
			// $collect = collect([]);
			if ($data_jadwal->isEmpty()) {
				return response()->json([
					'message' => 'Absensi Gagal'
				], 500);
			} 
			
			if ($data_jadwal->isNotEmpty()) {
				$data_absen = DB::table('absensi_mahasiswa')
					->join('datamahasiswa', 'datamahasiswa.nomor', 'absensi_mahasiswa.mahasiswa')
					->where('datamahasiswa.nrp', $nrp)
					// ->where('kuliah', (int) $value->nomor)
					->get();
					if ($data_absen->isEmpty()) {
						foreach ($data_jadwal as $key => $value) {
							$status = 'Hadir';
							$data = array(
								"mahasiswa" 	=> (int)$value->nomor,
								"kuliah" 		=> (int)$value->kuliah_id,
								"tanggal"		=> $tanggal,
								// "jam"			=> $jam,
								"status"		=> $status,
							);
							
							DB::table('absensi_mahasiswa')->insert($data);
							$message = 'Absensi berhasil';
							$last_id = DB::table('absensi_mahasiswa')->insertGetId($data);
							return response()->json([
								'message'	=> $message,
								'result'	=> $data,
								'id'		=> $last_id,
							], 200);
						}
					} else {
						return response()->json([
							'message' => 'Anda sudah absen'
						], 403);
					}
			} 
			
	}
	
}
