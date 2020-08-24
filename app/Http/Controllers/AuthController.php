<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\User;
use App\Mahasiswa;

class AuthController extends Controller
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

    public function generatekey()
    {
        return Str::random(32);
    }

    public function faca(){
        $mhs = DB::table('mahasiswa')->get();
        return response()->json([
            "status" => true,
            "result" => $mhs
        ], 200);
    }

    public function register(Request $request )
    {
        $nim = $request->input('nim');
        $nama = $request->input('nama');
        $telp = $request->input('telp');
        $password = Hash::make($request->input('password')) ;
  
        $register = Mahasiswa::create([
              'nim' => $nim,
			  'nama' => $nama,
			  'telp' => $telp,
              'password' => $password
        ]);
  
        if($register){
            return response()->json([
                'success' => true,
                'message' => 'Register Success!',
                'data' => $register
            ], 200);
        } else {
          return response()->json([
              'success' => false,
              'message' => 'Register Fail!',
              'data' => ''
          ], 400);
        }
    }
  
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
  
        $user = User::where('email', $email)->first();
  
        if (Hash::check($password, $user->password)) {
            $apiToken = base64_encode(Str::random(40));
  
            $user->update([
                'api_token' => $apiToken
            ]);
  
            return response()->json([
                'success' => true,
                'message' => 'Login Success!',
                'data' => [
                    'user' =>$user,
                    'api_token' => $apiToken
                ]
                ], 201);
        } else{
          return response()->json([
              'success' => false,
              'message' => 'Login Fail!',
              'data' => ''
              ], 400);
        } 
    }
}