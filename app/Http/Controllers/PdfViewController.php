<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PdfViewController extends Controller
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

    public function pdfviewindex(Request $request) {
		$nrp =  $request->uri->segment('3');
        // $query = $this->db->query('SELECT file_upload FROM tb_izin WHERE nim = "'.$nim.'"')->result();
        $query = DB::table('izin')->select('file_upload')->where('nrp', $nrp)->get();
		$file_name = $query[0]->file_upload;
		$file_path = './upload_izin/';
		
		$file = $file_path.$file_name;
		header('Content-type:application/pdf');
		header('Content-Description:inline;;filename="'.$file.'"');
		header('Content-Transfer-Encoding:binary');
		header('Accept-Range:bytes');
		@readfile($file);
	}

	public function changestatus(Request $request) {
		$nrp	    = $request->put('nrp');
		$status 	= array(
			'status' => $request->put('status')
		);
		// $this->db->where('nim', $nim);
        // $change_status = $this->db->update('tb_izin', $status);
        $change_status = DB::table('izin')->update($status)->where('nrp', $nrp);

		if( $change_status ) {
            return response()->json(['status'=>200, 'message'=>'Success'], 200);
		} else {
            return response()->json(['status'=>502, 'message'=>'Update Failed'], 502);
		}
	}
}
