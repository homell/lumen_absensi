<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthUserController extends Controller
{
    public function __construct()
    {
		//
    }

	public function loginMahasiswa(Request $request) {

		$nrp 		= $request->post('nim');
		$password 	= $request->post('password');
		// $imei 		= $request->post('imei');

		if ( $nrp != null && $password != null ) {	

				/** CEK LOGIN DI SINI **/
				$data_user = DB::table('datamahasiswa')
					->where('nrp', $nrp)
					->where('password', $password)
					->get();

				if($data_user->count() > 0) {
					$data = $data_user->all();
                    return response()->json([
						'status' => 200,
						'message' => 'Login Berhasil',
						'result' => $data,
                    ], 200);

				} else {
                    return response()->json([
						'status' => 500,
                        'message' => 'Nim atau Password salah',
                    ], 500);
				}	
			
		} else {
            return response()->json([
                'message' => 'Data tidak boleh kosong',
            ], 500);
        }  
	}

	public function loginDosen(Request $request) {

		$username = $request->post('username');
		$password = $request->post('password');

		if( isset( $username ) && isset( $password ) ) {
			$data_dosen = DB::table('pegawai')
				->where([
				['nip', '=', $username],
				['password', '=', $password],
			])->get();

			if( $data_dosen->count() > 0 ) {
				$data = $data_dosen->all();
				return response()->json([
					'status' => 200,
					'message' => 'Berhasil Login',
					'result' => $data
				], 200);
			} else {
				return response()->json([
					'status' => 400,
					'message' => 'Gagal Login'
				], 400);
			}
		} else {
			return response()->json([
				'status' => 400,
				'message' => 'Data tidak boleh kosong',
			], 400);
		}
	}
}
    
