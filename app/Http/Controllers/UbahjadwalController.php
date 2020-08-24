<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UbahjadwalController extends Controller
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
     * BELUM DIPASTIKAN. TENTUKAN PENENTUAN METHOD DATE_FORMAT MENGGUNAKAN TANDA " " ATAU TANPA TANDA " ".
     * 
     * KURANG SELECT UNTUK MEMILIH MINGGU PERKULIAHAN PADA JADWAL.
     */
    public function matakuliah(Request $request)
    {
        $username 	= $request->get('username');
		$matakuliah = $request->get('matakuliah');
		$kelas 		= $request->get('kelas');
		// $minggu 	= $request->get('minggu');

		$username = "'$username'";

		$q_matakuliah = '';
		if( isset($matakuliah) ) {
            // $q_matakuliah = " and tb_matakuliah.matakuliah = '$matakuliah'";
            $q_matakuliah = DB::table('matakuliah')->select('matakuliah')
                          ->where('matakuliah', $matakuliah)->get();
		}

		$q_kelas = '';
		if( isset($kelas) ) {
            // $q_kelas = " and tb_kelas.kelas_nama = '$kelas'";
            $q_kelas = DB::table('kelas')->select(DB::raw("concat(kelas, paralel) as kelas"))
                        ->where('kelas', $kelas)->get();
		}

		// $q_minggu = '';
		// if( isset($minggu) ) {
        //     // $q_minggu = " and tb_jadwal.minggu = '$minggu'";
        //     $q_minggu = DB::table('tb_jadwal.minggu', '=', $minggu);
		// }

        // $data_matakuliah = DB::table('tb_jadwal')
        //     ->join('tb_matakuliah', 'tb_matakuliah.kode_mk', 'tb_jadwal.kode_mk')
        //     ->join('tb_kelas', 'tb_kelas.kelas_id', 'tb_jadwal.kelas_id')
        //     ->join('tb_dosen', 'tb_dosen.dsn_id', 'tb_jadwal.dosen_id')
        //     ->join('tb_ruang', 'tb_ruang.rg_id', 'tb_jadwal.ruang_id')
        //     ->select('tb_jadwal.jdwl_id as id', 'tb_matakuliah.matakuliah', 
        //             DB::raw("date_format('tb_jadwal.tanggal', '%d-%m-%Y') as tanggal"),
        //             'tb_jadwal.minggu as minggu', 'tb_jadwal.status as status',
        //             'tb_ruang.nama as ruangan', 'tb_kelas.kelas_nama as kelas')
        //     ->where('tb_dosen.username', '=', $username, $q_matakuliah, $q_kelas, $q_minggu)
        //     ->orderBy('tb_jadwal.tanggal', 'ASC')
        //     ->get();
        $data_matakuliah = DB::table('kuliah ')
            ->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
            ->join('ruang', 'ruang.nomor', 'kuliah.ruang')
            ->join('kelas', 'kelas.nomor', 'kuliah.kelas')
            ->join('datamahasiswa', 'datamahasiswa.kelas', 'kelas.nomor')
            ->join('pegawai', 'pegawai.nomor', 'kuliah.dosen')
            ->select('kuliah.nomor as id', 'matakuliah.matakuliah', 'ruang.keterangan as ruangan',
                    DB::raw("date_format(kuliah.tglnilai,'%d-%m-%Y') as tanggal"),
                    'kuliah.kehadiran as status', DB::raw("concat(kelas.kelas, kelas.paralel) as kelas"))
            ->where('pegawai.nip', [$username ,"'$q_matakuliah'" ,"'$q_kelas'"])
            ->orderBy('kuliah.nomor', 'ASC')
            ->get();
        return response()->json([
            'status' => 200,
            'result' => $data_matakuliah,
        ], 200);
    }

    public function ubahjadwalpertemuan(Request $request)
    {
        $kuliah_id 	= $request->put('jadwal_id');
		$tanggal 	= $request->put('tanggal');
		$dosen 	    = $request->put('dosen');  // Di isi dengan NIP pada table PEGAWAI

		// $hari = date('D', strtotime($tanggal));

		// $var = '20-04-2012';
		$date = str_replace( '-', '-', $tanggal );
		$hari = date('D', strtotime( $date ) );
		$v_tanggal = date('Y-m-d', strtotime( $date ) );

		switch ($hari) {
			case 'Sun':
			$nama_hari = "Minggu";
			break;
			case 'Mon':			
			$nama_hari = "Senin";
			break;
			case 'Tue':
			$nama_hari = "Selasa";
			break;
			case 'Wed':
			$nama_hari = "Rabu";
			break;
			case 'Thu':
			$nama_hari = "Kamis";
			break;
			case 'Fri':
			$nama_hari = "Jumat";
			break;
			case 'Sat':
			$nama_hari = "Sabtu";
			break;
			default:
			$nama_hari = "";		
			break;
		}

		/** Format tanggal 0000-00-00 set dari Client, dan value btn untuk UI 00-00-0000 **/
		$status_edit = 'Pengganti';
		$data = array(
			'tglujian' => $v_tanggal,
			'hari'	  => $nama_hari,	
			'kehadiran'  => $status_edit
		);

        // $this->db->where('jdwl_id', $id_jadwal);
        // DB::where('jdwl_id', $id_jadwal);
		// $ubah_pertemuan = $this->db->update('tb_jadwal', $data);
        $ubah_pertemuan = DB::table('kuliah')
                        ->update($data)
                        ->where('nomor', '=', $kuliah_id)
                        ->get();

		if( $ubah_pertemuan ) {
            return response()->json([
                'status' => 200,
                'message' => 'Berhasil mengupdate data',
                'result' => $data,
            ], 200);
            // $this->notif($dosen);
            $request->notif($dosen);
		} else {
            return response()->json([
                'status' => 502,
                'message' => 'Gagal update',
            ], 502);
		}
    }

    public function MhsToken($id)
	{
		include 'firebase/dbconfig.php';

        // $getnim = $this->db->query('SELECT nim FROM tb_jdwl_mhs WHERE jadwal_id = '.$id.'')->result();
        $getnim = DB::table('kuliah')->select('datamahasiswa.nrp')
                ->join('kelas', 'kelas.nomor', 'kuliah.kelas')
                ->join('datamahasiswa', 'datamahasiswa.kelas', 'kelas.nomor')
                ->where('kuliah.nomor', $id)->get();
  		$nrp = [];
  		$token = [];

  		foreach ($getnim as $data) {
  			array_push($nrp, $data->nrp);
  		}

  		foreach ($nrp as $data) {
  			// echo $data;
  			$ref = "tokens/mahasiswa/".$data;
			$getToken = $database->getReference($ref)
    						 ->getValue();
    		array_push($token, $getToken['token']);
  		}


		return $token;
	}
	public function notif($ids, $dosen) {
		 define( 'API_ACCESS_KEY', 'mykey');
		 
	     $msg = array
	          (
	            'body'  => 'jadwal pertemuan diubah',
	            'title' => 'Dosen '.$dosen.'',
	            'badge' => '1'

	          );
	    $fields = array
	            (
	                'registration_ids'        => $ids,
	                'priority' => 'high',
	                'notification'  => $msg,
	            'badge' => '1'
	            );
	          
	    $headers = array
	            (
	                'Authorization: key=AAAACvL1XDA:APA91bEAnqsNEDB-YLC38EfvYQS7vyuZlxJpo7rgMUHokJzjVo7wYVaXPbtnlJdz1KsXJSI47VOxehslCytkx9R9yKBl_Uz59Ko-Gbx2_bzjA78p6QJRiFOWCsgzDYCwZOFRrAk62H04',
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
      
    // public function notif(Request $request)
    // {
    //     define( 'API_ACCESS_KEY', 'mykey');
    //     // $token = $this->db->query("SELECT token FROM tb_token WHERE app='mahasiswa'")->result_array();
    //     $token = DB::table('tb_token')->select('token')->where('app', '=', 'mahasiswa')->get();
    //     $ids = [];
    //     for ($i=0; $i <count($token)  ; $i++) { 
    //        array_push($ids, $token[$i]['token']);
    //    }
    //     $msg = array
    //          (
    //            'body'  => 'jadwal pertemuan diubah',
    //            'title' => 'Dosen '.$dosen.'',

    //          );
    //    $fields = array
    //            (
    //                'registration_ids'   => $ids, 
    //                'notification'       => $msg
    //            );

    //    $headers = array
    //            (
    //                'Authorization: key=AAAACvL1XDA:APA91bEAnqsNEDB-YLC38EfvYQS7vyuZlxJpo7rgMUHokJzjVo7wYVaXPbtnlJdz1KsXJSI47VOxehslCytkx9R9yKBl_Uz59Ko-Gbx2_bzjA78p6QJRiFOWCsgzDYCwZOFRrAk62H04',
    //                'Content-Type: application/json'
    //            );
    //        $ch = curl_init();
    //        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    //        curl_setopt( $ch,CURLOPT_POST, true );
    //        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    //        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    //        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    //        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    //        $result = curl_exec($ch );
    //        // echo $result;
    //        $err = curl_error($ch);

    //    // if ($err) {
    //    //   echo "cURL Error #:" . $err;
    //    // } else {
    //    //   print_r($result) ;
    //    // }
    // }
}
