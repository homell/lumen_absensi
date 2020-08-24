<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsenController extends Controller
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

    public function absensi(Request $request) {
        $nrp 	   	= $request->post('nim');
		$dataqr 	= $request->post('dataqr');
		
		date_default_timezone_set('Asia/Jakarta');
		
		$tanggal	=  date("Y-m-d"); 
		// $jam		=  date("H:i:s");

		$data_jadwal = DB::table('kuliah')
			->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
			->join('ruang', 'ruang.nomor', 'kuliah.ruang')
			->join('kelas', 'kelas.nomor', 'kuliah.kelas')
			->join('datamahasiswa', 'datamahasiswa.kelas', 'kelas.nomor')
			->select('datamahasiswa.nomor', 'datamahasiswa.nrp','matakuliah.matakuliah', 
					DB::raw("concat(kelas.kelas, kelas.paralel) as kelas"), 'ruang.kode',
					'ruang.keterangan', 'matakuliah.kode', 'kuliah.nomor as kuliah_id')
			->where('ruang.keterangan', 'like', ["'$dataqr'". "'$tanggal'"]) //Menggunakan Kode Ruang Dan Tanggal Untuk Absen
			->where('datamahasiswa.nrp', $nrp)
			// ->where('matakuliah.kode',  $dataqr) //Menggunakan Kode Matkul (Kurang Efektif)
			// ->where('tglujian', $tanggal)
			// ->whereNull('matakuliah.kode', $dataqr)
			->get();

			// return $data_jadwal;

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
							
							// DB::table('absensi_mahasiswa')->insert($data);
							$message = 'Absensi berhasil';
							$last_id = DB::table('absensi_mahasiswa')
									->insertGetId($data);
							//Notif Ke APP DOSEN
							// $dsnNip = $this->db->query('SELECT tb_jadwal.dosen_id, tb_dosen.username FROM `tb_jadwal` 
							// JOIN tb_dosen ON tb_jadwal.dosen_id = tb_dosen.dsn_id WHERE jdwl_id = '.$value->jadwal_id.'')->result();
							$dsnNip = DB::table('kuliah')->select('kuliah.dosen', 'pegawai.nip')
									->join('pegawai', 'pegawai.nomor', 'kuliah.dosen')
									->where('kuliah.nomor', $value->kuliah_id)->get();
							// $token = $this->DsnToken($dsnNip[0]->username);
							$token = $request->DsnToken($dsnNip[0]->username);
							$request->notif($token);
							//Response Json
							return response()->json([
								'message'	=> $message,
								'nrp'		=> $value->nrp,
								'kelas'		=> $value->kelas .$value->paralel,
								'matakuliah'=> $value->matakuliah,
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

	    // if ($err) {
	    //   echo "cURL Error #:" . $err;
	    // } else {
	    //   print_r($result) ;
	    // }
	  }
	
}
