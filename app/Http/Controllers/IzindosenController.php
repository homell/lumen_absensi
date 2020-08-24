<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IzindosenController extends Controller
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

    public function izindosen(Request $request)
    {
        $username = $request->get('username');

		// $data_izin = $this->db->query('
		// 	SELECT DISTINCT
		// 	tb_mahasiswa.nama,
		// 	tb_izin.nim, tb_kelas.kelas_nama as kelas,
		// 	concat(date_format(tb_izin.tanggal, "%d-%m-%Y"), " - " , 
		// 	date_format(tb_izin.jam, "%H:%i")) as tanggal,
		// 	tb_matakuliah.matakuliah, tb_jadwal.minggu, tb_izin.file_upload,
		// 	tb_izin.alasan,
		// 	tb_izin.longitude, tb_izin.latitude, tb_izin.status
		// 	FROM 
		// 	tb_izin, tb_absen, tb_jadwal, tb_matakuliah, tb_kelas, tb_mahasiswa, tb_dosen
		// 	WHERE
		// 	tb_mahasiswa.nim   = tb_absen.nim AND
		// 	tb_izin.absen_id   = tb_absen.absen_id AND
		// 	tb_absen.jadwal_id = tb_jadwal.jdwl_id AND
		// 	tb_jadwal.kode_mk  = tb_matakuliah.kode_mk AND
		// 	tb_jadwal.kelas_id = tb_kelas.kelas_id AND
		// 	tb_dosen.username = 201136079
		// 	')->result();
		$data_izin = DB::table('izin', 'kuliah')
				->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
				->join('ruang', 'ruang.nomor', 'kuliah.ruang')
				->join('kelas', 'kelas.nomor', 'kuliah.kelas')
				->join('datamahasiswa', 'datamahasiswa.kelas', 'kelas.nomor')
				->join('absensi_mahasiswa', 'absensi_mahasiswa.nomor', 'izin.absen_id')
				->join('pegawai', 'pegawai.nomor', 'kuliah.dosen')
				->select('datamahasiswa.nama', 'izin.nrp', 'kelas.kelas', 
					DB::raw("date_format(izin.tanggal, '%d-%m-%Y') as tanggal"),
					'matakuliah.matakuliah', 'izin.file_upload', 'izin.alasan', 
					'izin.status')  //KURANG MINGGU PERKULIAHAN
				->where('pegawai.nip', $username)
				->get();

		if( $data_izin->isNotEmpty()) {
            return response()->json(['status'=> 200, 'result' => $data_izin], 200);
		} else {
            return response()->json(['status'=> 500, 'message' => 'Tidak Ada Mahasiswa Izin'], 500);
		}
	}
	
	public function deleteizin(Request $request)
	{
		$izin_id = $request->post('izin_id');
		// $absen_id = $this->db->query('SELECT absen_id FROM `tb_izin` WHERE izin_id = '.$izin_id.'')->result()[0]->absen_id;
		$absen_id = DB::table('izin')->select('absen_id')->where('izin_id', $izin_id)->get()[0]->absen_id;

		if( $absen_id ) {
			// $this->db->query('DELETE FROM tb_izin WHERE izin_id = '.$izin_id.'');
			// $this->db->query('DELETE FROM tb_absen WHERE absen_id = '.$absen_id.'');
			DB::table('izin')->where('izin_id', $izin_id);
			DB::table('absensi_mahasiswa')->where('nomor', $absen_id);

			return response()->json(['status'=>200, 'message'=>'Data Berhasil Dihapus'], 200);
		} else {
			return response()->json(['status'=>500,'message'=>'Data Gagal Dihapus'], 500);
		}
		print_r($absen_id);
	}
}
