<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreTokenMhsController extends Controller
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

    public function index(Request $request) 
	{
		include 'firebase/dbconfig.php';

		$token = $request->post("token");
		$nrp   = $request->post("nim");

		$data = array(
						'token' 			=> $token,
					);

		$ref = "tokens/mahasiswa/".$nrp;
		$postdata = $database->getReference($ref)->set($data);


		if ($postdata) {
	        $message = "success";
	    } else {
	        $message = "failed";
	    }
        return response()->json(['status'=>201, 'message'=>$message], 201);
	}

	public function MhsToken($id)
	{
		include 'firebase/dbconfig.php';

		// $kelas = $this->post("kelas");
		// $ref = "tokens/".$kelas;
		// $getToken = $database->getReference($ref)
  //   						 ->getValue();

		// print_r($getToken);
  
        //   $getnim = $this->db->query('SELECT nim FROM tb_jdwl_mhs WHERE jadwal_id = 169')->result();
        $getnim = DB::table('kuliah')->select('datamahasiswa.nrp')
                ->join('kelas', 'kelas.nomor', 'kuliah.kelas')
                ->join('datamahasiswa', 'datamahasiswa.kelas', 'kelas.nomor')
                ->where('kuliah.nomor', $id)->get();
  		$nrp = [];

  		foreach ($getnim as $data) {
  			array_push($nrp, $data->nrp);
  		}

  		$token = [];
  		foreach ($nrp as $data) {
  			// echo $data;
  			$ref = "tokens/mahasiswa/".$data;
			$getToken = $database->getReference($ref)
    						 ->getValue();
    		array_push($token, $getToken['token']);
  		}
		print_r($token);
	}
}
