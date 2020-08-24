<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoridosenController extends Controller
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
	 * GAGAL UJI COBA. PERBAIKI ERROR UNKNOWN TABLE TB_ABSEN.NIM.
	 * 
	 * CODE BERHASIL NAMUN BELUM DI UJI COBA.
	 */
    public function historidosen(Request $request)
    {
        $username 	= $request->get('username');
		$matakuliah = $request->get('matakuliah');
		$kelas 		= $request->get('kelas');
		// $minggu 	= $request->get('minggu');

		$username = $username;

		$q_matakuliah = '';
		if( isset($matakuliah) ) {
            // $q_matakuliah = " and tb_matakuliah.matakuliah = '$matakuliah' ";
			$q_matakuliah = DB::table('matakuliah')->select('matakuliah')
					->where('matakuliah', $matakuliah)->get();
		}

		$q_kelas = '';
		if( isset($kelas) ) {
            // $q_kelas = " and tb_kelas.kelas_nama = '$kelas' ";
			$q_kelas = DB::table('kelas')->select(DB::raw("concat(kelas, paralel) as kelas"))
					->where('kelas', $kelas)->get();
		}

		// $q_minggu = '';
		// if( isset($minggu) ) {
        //     // $q_minggu = " and tb_jadwal.minggu = '$minggu' ";
        //     $q_minggu = DB::table('tb_jadwal')->get('minggu', $minggu);
		// }

		// $data_histori = $this->db->query('
		// 	SELECT
			// tb_absen.nim,
			// tb_absen.absen_id,
			// tb_jadwal.minggu as pertemuan,
			// tb_kelas.kelas_nama as kelas,
			// tb_mahasiswa.nama, CONCAT(date_format
			// (tb_absen.tanggal, "%d-%m-%Y")," ",date_format(tb_absen.jam,"%H:%i")) AS waktu,
			// tb_absen.status_absen AS status
		// 	FROM
		// 	tb_mahasiswa, tb_absen, tb_jadwal, tb_dosen, tb_matakuliah, tb_kelas
		// 	WHERE
		// 	tb_mahasiswa.nim 		= tb_absen.nim AND
		// 	tb_absen.jadwal_id 		= tb_jadwal.jdwl_id AND
		// 	tb_jadwal.dosen_id 		= tb_dosen.dsn_id AND
		// 	tb_matakuliah.kode_mk 	= tb_jadwal.kode_mk AND
		// 	tb_kelas.kelas_id 		= tb_jadwal.kelas_id AND

		// 	tb_dosen.username = '.$username.' '.$q_matakuliah.' '.$q_kelas.' '.$q_minggu.'
		// 	')->result();
		$data_histori = DB::table('absensi_mahasiswa')
			->join('kuliah', 'kuliah.nomor', 'absensi_mahasiswa.kuliah')
			->join('datamahasiswa', 'datamahasiswa.nomor', 'absensi_mahasiswa.mahasiswa')
			->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
			->join('kelas', 'kelas.nomor', 'kuliah.kelas')
			->join('pegawai', 'pegawai.nomor', 'kuliah.dosen')
			->select('pegawai.nip', 'absensi_mahasiswa.mahasiswa', 'datamahasiswa.nrp', 
					'absensi_mahasiswa.nomor as absen_id',DB::raw("concat(kelas.kelas, kelas.paralel) as kelas"),
					'datamahasiswa.nama', DB::raw("date_format(absensi_mahasiswa.tanggal, '%d-%m-%Y') as waktu"),
					'absensi_mahasiswa.status')  //KURANG MINGGU PERKULIAHAN
			->where('pegawai.nip', [$username, "'$q_matakuliah'", "'$q_kelas'"])
			->orderBy('absensi_mahasiswa.tanggal', 'DESC')
			->get();
        // $this->response( array( 'status'=>200, 'result'=>$data_histori ) );
        return response()->json([
            'status' => true,
            'result' => $data_histori,
        ], 200);
    }

    public function ubahkehadiran_mhs(Request $request)
    {
        $absen_id 		= $request->put('absen_id');
		$status_absen 	= $request->put('status_absen');
		$catatan		= $request->put('catatan');

		if( strlen($catatan) == null || '' ) {
			$catatan = null;
		}

		if( $status_absen == "Alfa" ) {

			// $this->db->where('absen_id', $absen_id);
			// $data_izin = $this->db->get('tb_izin');
			$data_izin = DB::table('izin')->where('absen_id', $absen_id);
			if( $data_izin->count() > 0 ) {

				// $this->db->where('absen_id', $absen_id);
				// $this->db->delete('tb_izin');
				DB::table('izin')->where('absen_id', $absen_id)->delete();

			}

			// $this->db->where('absen_id', $absen_id);
			// $this->db->delete('tb_absen');
			DB::table('absensi_mahasiswa')->where('nomor', $absen_id)->delete();
			// DB::where('absen_id', $absen_id);
			// $this->response( array('status' => 200, 'message' => 'Data kehadiran berhasil dihapus') );
			return response()->json([
				'status' => 200,
				'message' => 'Data kehadiran berhasil dihapus',
			], 200);

		} else {

			$data_update = array(
				'status'	 	=> $status_absen,
				'keterangan'	=> $catatan
			);

			// $this->db->where('absen_id', $absen_id);
			// $this->db->update('tb_absen', $data_update);
			DB::table('absensi_mahasiswa')->update($data_update)->where('nomor', $absen_id);
			// DB::where('absen_id', $absen_id);
			// DB::update('tb_absen', $data_update);
			// $this->response( array('status' => 200, 'message' => 'Berhasil ubah data kehadiran ke ' . $status_absen) );
			return response()->json([
				'status' => 200,
				'message' => 'Berhasil ubah data kehadiran ke ' . $status_absen,
			], 200);
        }
    }

    public function matkul(Request $request)
        {
        $username = $request->get('username');

		// $username = "'$username'";
		$username = $username;

		// $data_matkul = $this->db->query('
		// 	SELECT DISTINCT
		// 	tb_matakuliah.matakuliah as matakuliah
		// 	FROM
		// 	tb_matakuliah, tb_jadwal, tb_dosen
		// 	WHERE
		// 	tb_matakuliah.kode_mk 	= tb_jadwal.kode_mk AND
		// 	tb_jadwal.dosen_id 		= tb_dosen.dsn_id AND
		// 	tb_dosen.username		= '.$username.'
        // 	')->result();
        $data_matkul = DB::table('matakuliah')
			->join('kuliah', 'kuliah.matakuliah', 'matakuliah.nomor')
			->join('pegawai', 'pegawai.nomor', 'kuliah.dosen')
            ->select('matakuliah.matakuliah')
            ->where('pegawai.nip', $username)
            ->distinct()
            ->get();

        // $this->response( array('status' => 200, 'result'=>$data_matkul ) );	
        return response()->json([
            'status' => 200,
            'result' => $data_matkul,
        ], 200);
        }

    public function kelas(Request $request)
    {
        $username = $request->get('username');

		// $username = "'$username'";
		$username = $username;

		// $data_kelas = $this->db->query('
		// 	SELECT DISTINCT
		// 	tb_kelas.kelas_nama as kelas
		// 	FROM
		// 	tb_kelas, tb_jadwal, tb_dosen
		// 	WHERE
		// 	tb_kelas.kelas_id 	= tb_jadwal.kelas_id AND
		// 	tb_jadwal.dosen_id 	= tb_dosen.dsn_id AND
		// 	tb_dosen.username	= '.$username.'
        // 	')->result();
        $data_kelas = DB::table('kuliah')
			->join('kelas', 'kelas.nomor', 'kuliah.kelas')
			->join('pegawai', 'pegawai.nomor', 'kuliah.dosen')
            ->select(DB::raw("concat(kelas.kelas, kelas.paralel) as kelas"))
            ->where('pegawai.nip', $username)
            ->distinct()
            ->get();

        // $this->response( array('status'=>200, 'result'=>$data_kelas) );
        return response()->json([
            'status' => 200,
            'result' => $data_kelas,
        ], 200);
    }

    public function minggu(Request $request)
    {
        $username = $request->get('username');

		// $username = "'$username'";
		$username = $username;

		// $data_minggu = $this->db->query('
		// 	SELECT DISTINCT
		// 	tb_jadwal.minggu as minggu
		// 	FROM
		// 	tb_jadwal, tb_dosen
		// 	WHERE
		// 	tb_jadwal.dosen_id = tb_dosen.dsn_id AND
		// 	tb_dosen.username = '.$username.'
        // 	')->result();
        $data_minggu = DB::table('kuliah')
			->join('kelas', 'kelas.nomor', 'kuliah.kelas')
			->join('pegawai', 'pegawai.nomor', 'kuliah.dosen')
			->join('absensi_mhs_minggu', 'absensi_mhs_minggu.kelas', 'kelas.nomor')
            ->select('absensi_mhs_minggu.minggu')
            ->where('pegawai.nip', $username)
            ->distinct()
            ->get();

        // $this->response( array('status'=>200, 'result'=>$data_minggu) );
        return response()->json([
            'status' => 200,
            'result' => $data_minggu,
        ], 200);
    }
}
