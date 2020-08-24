<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
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

    public function jadwalMhs(Request $request) {

		$nrp = $request->get('nim');

		date_default_timezone_set('Asia/Jakarta');
		$tanggal_skrg = date("Y-m-d");

		/**
		AND tb_jadwal.tanggal BETWEEN DATE_SUB($tanggal_skrg, INTERVAL 3) AND 
		DATE_ADD($tanggal_skrg, INTERVAL 7);

		BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)

		Work : AND tb_jadwal.tanggal  BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 7 DAY)
		**/
		// $data_jdwl = $this->db->query('
		// 	SELECT 
		// 	tb_dosen.nama as dosen, tb_matakuliah.matakuliah, tb_ruang.nama as ruangan, 
		// 	concat(tb_jadwal.hari, ", " ,date_format(tb_jadwal.tanggal,"%d-%m-%Y")) as hari,
		// 	tb_jadwal.tanggal as today,
		// 	tb_jadwal.status as status,
		// 	concat(date_format(tb_jam.jam_masuk, "%H:%i"), " - " , date_format(tb_jam.jam_keluar, "%H:%i")) as jam

		// 	from 
		// 	tb_mahasiswa, tb_jdwl_mhs, tb_jadwal, tb_matakuliah, tb_ruang, tb_jam, tb_dosen

		// 	where 
		// 	tb_jdwl_mhs.nim 		= tb_mahasiswa.nim and 
		// 	tb_jdwl_mhs.jadwal_id 	= tb_jadwal.jdwl_id and 
		// 	tb_jadwal.kode_mk 		= tb_matakuliah.kode_mk and 
		// 	tb_jadwal.dosen_id 		= tb_dosen.dsn_id and
		// 	tb_jadwal.jam_id 		= tb_jam.jam_id and 
		// 	tb_jadwal.ruang_id 		= tb_ruang.rg_id and 
		// 	tb_mahasiswa.nim 		= "'.$nim.'" 
		// 	order by tb_jadwal.tanggal asc')->result();

		$data_jdwl = DB::table('kuliah')
			->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
			->join('ruang', 'ruang.nomor', 'kuliah.ruang')
			->join('kelas', 'kelas.nomor', 'kuliah.kelas')
			->join('datamahasiswa', 'datamahasiswa.kelas', 'kelas.nomor')
			->join('pegawai', 'pegawai.nomor', 'kuliah.dosen')
			->select('pegawai.nama as dosen', 'matakuliah.matakuliah',DB::raw("concat(kelas.kelas, kelas.paralel) as kelas"), 
			        'ruang.keterangan as ruangan',
					DB::raw("date_format(kuliah.tglnilai,'%d-%m-%Y') as hari"),
					'kuliah.kehadiran as status')
			->where('datamahasiswa.nrp', $nrp)
			->orderBy('datamahasiswa.nrp', 'ASC')
			->get();

		// return $data_jdwl;
		if( $data_jdwl->isNotEmpty() ) {
			return response()->json([
				"status" => 200,
				'result' => $data_jdwl,
			], 200);
		} else {
			return response()->json([
				'status' => 500,
				'result' => 'Jadwal perkuliahan kosong',
			], 500);
		}
	}

	public function jadwalDsn(Request $request) 
	{
		$username = $request->get('username');

		$data_jadwal = DB::table('kuliah')
			->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
			->join('ruang', 'ruang.nomor', 'kuliah.ruang')
			->join('kelas', 'kelas.nomor', 'kuliah.kelas')
			->join('datamahasiswa', 'datamahasiswa.kelas', 'kelas.nomor')
			->join('pegawai', 'pegawai.nomor', 'kuliah.dosen')
			->select('pegawai.nama as dosen','matakuliah.matakuliah', 
					DB::raw("concat(kelas.kelas, kelas.paralel) as kelas"), 'ruang.keterangan as ruangan',
					DB::raw("date_format(kuliah.tglnilai,'%d-%m-%Y') as hari"),
					'kuliah.kehadiran as status', 'matakuliah.kode')
			->where('pegawai.nip', $username)
			->get();
		// $data_jadwal = $this->db->query('
		// 	SELECT
		// 	tb_dosen.nama AS dosen,
		// 	tb_matakuliah.matakuliah,
		// 	tb_ruang.nama AS ruangan,
		// 	tb_kelas.kelas_nama AS kelas,
		// 	tb_jadwal.status as status,
		// 	concat( tb_jadwal.hari, ", ", date_format( tb_jadwal.tanggal, "%d-%m-%Y" ) ) AS hari,
		// 	tb_jadwal.tanggal AS today,
		// 	concat( date_format( tb_jam.jam_masuk, "%H:%i" ), " - ", date_format( tb_jam.jam_keluar, "%H:%i" ) ) AS jam
		// 	FROM
		// 	tb_jadwal,tb_matakuliah,tb_jam,tb_ruang,tb_dosen, tb_kelas
		// 	WHERE
		// 	tb_jadwal.dosen_id = tb_dosen.dsn_id 
		// 	AND tb_jadwal.kode_mk = tb_matakuliah.kode_mk 
		// 	AND tb_jadwal.jam_id = tb_jam.jam_id
		// 	AND tb_jadwal.kelas_id = tb_kelas.kelas_id
		// 	AND tb_jadwal.ruang_id = tb_ruang.rg_id
		// 	and tb_dosen.username = "'.$username.'"
		// 	')->result();
		if( $data_jadwal->isNotEmpty()) {
			return response()->json([
				'status' => 200,
				'result' => $data_jadwal
			], 200);
		} else {
			return response()->json([
				'status' => 500,
				'message' => 'Jadwal perkuliahan kosong'
			], 500);
		}
	}

}