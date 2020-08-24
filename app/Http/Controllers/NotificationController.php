<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
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

    public function notification(Request $request) {
        $message    = $request->post('message');
        $keyMHS     = "key=AAAACvL1XDA:APA91bHNPPTSFNQPqkVe2B_rbCxrUNRZdzf6Q3Y09USQ-y59I9sIsKaYxcTrnZuvE1RGv9K-dJ8B9STIBai7x_XndGXS51GzR2a50Y-hu0l2NlLabGzU-GpuVO9Ut1FL0PXVsDZEEQot";
        $keyDSN     = "key=AAAAiMCqAc4:APA91bFbp43J1ivSpRJuYTBOK7wkOcKb60Q-9qE1CPmYOfZZ5QNDyWs035p5Nsnt1PNDdymMJIdEqMLkO-Zl1fBggTgM2YyaQ0PBGdQKDuJs0elp8W_BryrTJKfdXEKVpXcMeDV5wgyc";
    
        $curl = curl_init();
        $registration_ids = '["ekZPzbo34Mo:APA91bFyG1_km4aYDjSCdKomEWpgK6GvHgkf94xMYJ_HAUbls3dR7w1MK34-VaT6z4pZ9iLyWhbEG9RdHNpnuNcFOhMRIfuZ0FmvgQMRWgsDhouogmdsXlDVOv6005wC1zBK72_M9iRi", "f1CDw2m1z5w:APA91bEyRPp1chinJ0-IA8BgeabeE4PvDiOZAtkUc7HJAsRKBAL9cZVPMCfaHRUSq61_QLv5V3FThCqjGjVtoDar0fmOMxrIboj5YNB1DfxiPv93vk83IqIs6s90P3sPdDJ6NfTIxKp1"]';
        $auth_key = $keyDSN;
        $message = $request->post('message');
    
    
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => '{
                                    "registration_ids":' .$registration_ids. ',
                                    "notification": {
                                        "title":"Surat Izin Diterima",
                                        "body":' . json_encode($message). ',
                                    }
                                  }',
          CURLOPT_HTTPHEADER => array(
            "authorization: ".$auth_key,
            "content-type: application/json",
          ),
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        curl_close($curl);
    
        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
      } 
}
