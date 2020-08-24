<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IzinController extends Controller
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
	 * BELUM DIBERIKAN FUNGSI MENYIMPAN DATA KE LOKASI YANG DITENTUKAN.
	 */
	public function izin(Request $request) {
		
		// $config = array(
		// 	'upload_path' 	=> "./upload_izin/",
		// 	'allowed_types' => "pdf|csv",
		// 	'overwrite' 	=> TRUE,
		// 	'max_size' 		=> "512",
		// );
		$this->validate($request, [
			'file_upload' => 'required|file|mimes:pdf|max:512',
			'keterangan'  => 'required',
		]);

		// $this->load->library('upload', $config);

		$nrp 				= $request->post('nim');
		$alasan 			= $request->post('alasan');
		$keterangan 		= $request->post('keterangan');
		$tgl_izin 			= $request->post('tglizin');
		$tgl_izinsampai 	= $request->post('tglizinSampai');
		$unggah_file		= $request->file('userfile'); 

		date_default_timezone_set('Asia/Jakarta');
		$tanggal 	= date("Y-m-d");
		// $jam 		= '13:00:00'; //date("H:i:s");

		$nama_file = $unggah_file->getClientOriginalName();
		$unggah_file->storeAs('/upload_izin', $nama_file);
		
		//Get Data Jadwal
		// $data_jadwal = $this->db->query(
			// 'SELECT tb_jdwl_mhs.jadwal_id
		// 	from tb_jdwl_mhs, tb_jadwal, tb_mahasiswa, tb_jam	
				// where
		// 	tb_jadwal.jdwl_id       = tb_jdwl_mhs.jadwal_id and
		// 	tb_jam.jam_id 			= tb_jadwal.jam_id and
		// 	tb_mahasiswa.nim 		= tb_jdwl_mhs.nim and
		// 	tb_mahasiswa.nim 		= "' . $nim . '" and
		// 	tanggal 				= "' . $tanggal . '" and
		// 	"' . $jam . '" between tb_jam.jam_masuk and tb_jam.jam_keluar')->result();
		$data_jadwal = DB::table('kuliah')
				->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
				// ->join('ruang', 'ruang.nomor', 'kuliah.ruang')
				->join('kelas', 'kelas.nomor', 'kuliah.kelas')
				->join('datamahasiswa', 'datamahasiswa.kelas', 'kelas.nomor')
				->select('kuliah.nomor as kuliah_id', 'datamahasiswa.nomor as mahasiswa')
				->where('datamahasiswa.nrp', $nrp) //KURANG $TANGGAL UNTUK WHERE CLAUSE
				->get();


		if ($data_jadwal->isEmpty()) {
			return response()->json(['status' => 400, 'message' => 'Data jadwal tidak ditemukan'], 400);
		}

		foreach ($data_jadwal as $value) {
		
		if ($data_jadwal->isNotEmpty()) {
			if ($nrp != NULL && $alasan != NULL && $keterangan != NULL) { //$this->upload->do_upload() == TRUE &&
				
				// $file_upload = $request->upload->data();
				// $nama_file = $file_upload->getOriginalName;
				// $file_upload->move(public_path(). '/upload_izin');
				// $this->db->where('nim', $nim);
				// // $this->db->where('tanggal', $tgl_izin); GUNAKAN INI UTK IZIN PER HARI
				// $this->db->where('jadwal_id', (int) $value->jadwal_id);

				// $data_absen = $this->db->get('tb_absen');
				$data_absen = DB::table('absensi_mahasiswa')
					->join('datamahasiswa', 'datamahasiswa.nomor', 'absensi_mahasiswa.mahasiswa')
					->where('datamahasiswa.nrp', $nrp)
					// ->where('tanggal', $tgl_izin); //GUNAKAN INI UTK IZIN PER HARI
					->where('absensi_mahasiswa.kuliah', (int)$value->kuliah_id)  //MENGAMBIL DATA NOMOR KULIAH PADA TABLE ABSENSI_MAHASISWA DARI HASIL VARIABLE $DATA_JADWAL
					->get();

					if ($data_absen->count() > 0) {  // num_rows() diganti count()
						return response()->json(['status' => 201, 'message' => 'Anda sudah absen'], 201);
					} else {
						$insert_absen = array(
							'mahasiswa'		=> $value->mahasiswa,
							'kuliah'	 	=> (int)$value->kuliah_id,
							'tanggal' 		=> $tanggal,
							// 'jam' 			=> $jam,
							'status' 		=> $alasan,
						);
						// $this->db->insert('tb_absen', $insert_absen);
						// $last_id = $this->db->insert_id();
						// DB::table('tb_absen')->insert($insert_absen);
						$last_id = DB::table('absensi_mahasiswa')->insertGetId($insert_absen);

						$insert_izin = array(
							'nrp ' 			=> $nrp,
							'absen_id' 		=> $last_id,
							// 'file_upload' 	=> $file_upload['file_name'],
							'file_upload' 	=> $nama_file,
							'alasan' 		=> $alasan,
							'keterangan' 	=> $keterangan,
							'tanggal' 		=> $tgl_izin,
							// 'jam' 			=> $jam,
							// 'longitude' 	=> $longitude,
							// 'latitude' 		=> $latitude,
						);
						DB::table('izin')->insert($insert_izin);

						//Fungsi Notif
						// $dsnNip = $this->db->query('SELECT tb_jadwal.dosen_id, tb_dosen.username FROM `tb_jadwal` JOIN tb_dosen ON tb_jadwal.dosen_id = tb_dosen.dsn_id WHERE jdwl_id = '.$value->jadwal_id.'')->result();
						$dsnNip = DB::table('kuliah')->select('kuliah.dosen', 'pegawai.nip')
								->join('pegawai', 'pegawai.nomor', 'kuliah.dosen')
								->where('kuliah.nomor', $value->kuliah_id)->get();
						$token = $this->DsnToken($dsnNip[0]->username);
						return response()->json(['status' => 201, 'message' => 'Berhasil menambahkan Izin'], 201);
						$this->notif($token);
					}
			} else {
				// $message = 'Gagal menambahkan izin' . ' - ' . $this->upload->display_errors();
				return response()->json(['status' => 400, 'message' => 'Gagal Menambahkan Izin'], 400);
			}
		}
		}
	}

	public function index(Request $request) 
	{
		$username = $request->input->get('username');

		// $data_izin = $this->db->query('
		// 	SELECT tb_izin.*, tb_mahasiswa.nama, tb_mahasiswa.nim, tb_kelas.kelas_nama as kelas, tb_jadwal.minggu, tb_matakuliah.matakuliah 
		// FROM `tb_izin` 
		// JOIN tb_absen ON tb_izin.absen_id = tb_absen.absen_id JOIN tb_jadwal ON tb_absen.jadwal_id = tb_jadwal.jdwl_id JOIN tb_dosen ON tb_jadwal.dosen_id = tb_dosen.dsn_id JOIN tb_mahasiswa ON tb_izin.nim = tb_mahasiswa.nim JOIN tb_kelas ON tb_jadwal.kelas_id = tb_kelas.kelas_id JOIN tb_matakuliah ON tb_jadwal.kode_mk = tb_matakuliah.kode_mk WHERE tb_dosen.username = '.$username.'
		// 	')->result();
		$data_izin = DB::table('izin')
					->select('izin.*', 'datamahasiswa.nama', 'datamahasiswa.nrp', DB::raw("concat(kelas.kelas, kelas.paralel) as kelas")
							,'matakuliah.matakuliah')
					->join('absensi_mahasiswa', 'absensi_mahasiswa.nomor', 'izin.absen_id')
					->join('kuliah', 'kuliah.nomor', 'absensi_mahasiswa.kuliah')
					->join('pegawai', 'pegawai.nomor', 'kuliah.dosen')
					->join('izin', 'izin.nrp', 'datamahasiswa.nrp')
					->join('kelas', 'kelas.nomor', 'kuliah.kelas')
					->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
					->where('pegawai.nip', $username)->get();

		if( $data_izin->isNotEmpty() ) {
			return response()->json(['status'=>200, 'result'=>$data_izin], 200);
		} else {
			return response()->json(['status'=>500, 'message'=>'Tidak Ada Mahasiswa Izin'], 500);
		}

	}

	public function delete(Request $request)
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
			return response()->json(['status'=>500, 'message'=>'Data Gagal Dihapus'], 500);
		}
		print_r($absen_id);
	}

	
	public function DsnToken($nip)
	{
		include 'firebase/dbconfig.php';

		$ref = "tokens/dosen/".$nip;
		$getToken = $database->getReference($ref)
    						 ->getValue();

		return $getToken['token'];
	}
	public function notif($ids, Request $request) {
		 define( 'API_ACCESS_KEY', 'mykey');
		 $nrp = $request->post('nrp');
		//  $token = $this->db->query("SELECT token FROM tb_token WHERE app='dosen'")->result_array();
		//  $ids = [];
		//  for ($i=0; $i <count($token)  ; $i++) { 
		// 	array_push($ids, $token[$i]['token']);
		// }
	     $msg = array
	          (
	            'body'  => $nrp.' mengirim surat izin',
	            'title' => 'Surat izin Diterima ',

	          );
	    $fields = array
	            (
	                'registration_ids'        => [$ids], 
	                'notification'  => $msg
	            );

	    $headers = array
	            (
	                'Authorization: key=AAAAiMCqAc4:APA91bFbp43J1ivSpRJuYTBOK7wkOcKb60Q-9qE1CPmYOfZZ5QNDyWs035p5Nsnt1PNDdymMJIdEqMLkO-Zl1fBggTgM2YyaQ0PBGdQKDuJs0elp8W_BryrTJKfdXEKVpXcMeDV5wgyc',
	                'Content-Type: application/json'
	            );
	        $ch = curl_init();
	        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
	        curl_setopt( $ch,CURLOPT_POST, true );
	        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	        $result = curl_exec($ch );
	        // echo $result;
	        $err = curl_error($ch);
	  }

	  public function SuratIzin(Request $request)
	  {
		$nrp = $request->post('nim');

		// $surat_izin = $this->db->query('SELECT * FROM `tb_izin` WHERE nim = '.$nim.'')->result();
		$surat_izin = DB::table('izin')->select('*')->where('nrp', $nrp)->get();

		if( $surat_izin->isNotEmpty()) {
			return response()->json(['status'=>200, 'result'=>$surat_izin], 200);
		} else {
			return response()->json(['status'=>500, 'message'=>'Tidak Ada Surat Izin'], 500);	
		}

	  }

	// public function cobaizin_post() {

	// 	$tanggal = $this->post('tanggal_izin');
	// 	$nim = $this->post('nim');
	// 	$jam = '13:00:00';
	// 	$status = $this->post('status');

	// 	$data_izin = $this->db->query(
	// 		'SELECT
	// 		tb_jdwl_mhs.jadwal_id, tb_jadwal.tanggal
	// 		FROM
	// 		tb_jadwal,
	// 		tb_jdwl_mhs,
	// 		tb_mahasiswa
	// 		WHERE
	// 		tb_jadwal.jdwl_id = tb_jdwl_mhs.jadwal_id
	// 		AND tb_mahasiswa.nim = tb_jdwl_mhs.nim
	// 		AND tb_jadwal.tanggal = "' . $tanggal . '"
	// 		AND tb_mahasiswa.nim = "' . $nim . '"'
	// 	);

	// 	if ($data_izin->num_rows() > 0) {

	// 		$this->db->where('tb_absen.tanggal', $tanggal);
	// 		$data_sudah_absen = $this->db->get('tb_absen');

	// 		if ($data_sudah_absen->num_rows() > 0) {
	// 			$this->response(array('status' => 403, 'message' => 'Anda Sudah Absen'));
	// 		} else {

	// 			$data_insert = array();
	// 			foreach ($data_izin->result() as $value) {
	// 				$data_insert[] = array(
	// 					'nim' => $nim,
	// 					'jadwal_id' => $value->jadwal_id,
	// 					'tanggal' => $tanggal,
	// 					'jam' => $jam,
	// 					'status_absen' => $status,
	// 				);
	// 			}

	// 			$this->db->insert_batch('tb_absen', $data_insert);

	// 			$insert_id = array();
	// 			$nilai_insert = $this->db->insert_id();
	// 			for ($i = 0; $i < count($data_insert); $i++) {

	// 				$insert_id[] = array(
	// 					'id_inserted' => $nilai_insert,
	// 				);
	// 				$nilai_insert++;

	// 			}

	// 			$this->response(array('status' => 200, 'message' => $insert_id));

	// 		}

	// 	} else {

	// 		$this->response(array('status' => 403, 'message' => 'Data jadwal tidak ditemukan'));

	// 	}

	// 	// $data_hapus = array();
	// 	// foreach ($data_izin->result() as $value) {
	// 	// 	$this->db->where('jadwal_id', $value->jadwal_id);
	// 	// 	$this->db->delete('tb_absen');
	// 	// }

	// }
}