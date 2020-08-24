<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PilihmatkulController extends Controller
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
     * BERHASIL UJI COBA. TAPI PERBAIKI LAGI JOIN NYA UNTUK MENGHASILKAN DATA BENTUK STRING, BUKAN INTEGER.
     */
    public function pilihmatkul(Request $request)
    {
        $nrp 	 = $request->get('nim');
		$tanggal = date('Y-m-d'); //set date manual

        // $data_pilih = DB::table('tb_jadwal')
        //     ->join('tb_matakuliah','tb_matakuliah.kode_mk', 'tb_jadwal.kode_mk')
        //     ->join('tb_jdwl_mhs', 'tb_jdwl_mhs.jadwal_id', 'tb_jadwal.jdwl_id')
        //     ->join('tb_mahasiswa', 'tb_mahasiswa.nim', 'tb_jdwl_mhs.nim')
        //     ->select('tb_matakuliah.matakuliah', 'tb_jadwal.jdwl_id')
        //     ->where('tb_mahasiswa.nim', '=', $nim)
        //     ->where('tb_jadwal.tanggal', '=', $tanggal)
        //     ->get();
        $data_pilih = DB::table('kuliah')
            ->join('matakuliah', 'matakuliah.nomor', 'kuliah.matakuliah')
            ->join('kelas', 'kelas.nomor', 'kuliah.kelas')
            ->join('datamahasiswa', 'datamahasiswa.kelas', 'kelas.nomor')
            ->select('matakuliah.matakuliah', 'kuliah.nomor')
            ->where('datamahasiswa.nrp', $nrp)
            ->get();
        return response()->json([
            'status' => 200,
            'result' => $data_pilih,
        ], 200);
    }
}
