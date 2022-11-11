<?php
namespace App\Library;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Library\ApiSettingsData;

class ChatApiWA
{
    public static function sendWA($phone, $message)
    {
        try{
            $apiURL = 'https://eu106.chat-api.com/instance121091/';
            $token = '6llqswgtmhqm0raf';

            $url = $apiURL.'message?token='.$token;
          
            $data = [
                'phone' => $phone,
                'body' => $message,
            ];

            $options = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => json_encode($data)
                ]
            ]);
            // Send a request
            $result = file_get_contents($url, false, $options);                     
            $response = json_decode($result);
            dd($response);
        }catch (Exception $e) {
            return ["status"=>false,"data"=>$e->getMessage()];
        }
    }

    public static function SendWa2($phone, $message)
    {
        try{
            $curl = curl_init();
            $token = "TI3kDnyWK3Zus2Fx3klFxkjWrbHwD7xgoZh6NPXMWzven9nS5gVbCIntsgBKNg9k";
            $data = [
                'phone' => $phone,
                'message' => $message,
            ];

            curl_setopt($curl, CURLOPT_HTTPHEADER,
                array(
                    "Authorization: $token",
                )
            );
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_URL, "https://sambi.wablas.com/api/send-message");
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($curl);
            curl_close($curl);
            $pesan = json_decode($result, TRUE);

            
            if($pesan['status'] == 'true')
                return ["success"=>true,"message"=>$pesan['message']];
            else
                return ["success"=>false,"message"=>"Terdapat kesalahan"];
        }catch (Exception $e) {
            return ["success"=>false,"message"=>$e->getMessage()];
        }
    }

    public static function CheckSender($phone)
    {
        try{
            $curl = curl_init();
            $token = "TI3kDnyWK3Zus2Fx3klFxkjWrbHwD7xgoZh6NPXMWzven9nS5gVbCIntsgBKNg9k";
            $data = [
                'phone' => $phone,
            ];

            curl_setopt($curl, CURLOPT_HTTPHEADER,
                array(
                    "Authorization: $token",
                )
            );
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_URL, "https://sambi.wablas.com/api/device/change-sender");
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($curl);
            curl_close($curl);
            $pesan = json_decode($result, TRUE);

            return ["success"=>true,"message"=>$pesan['message']];
        }catch (Exception $e) {
            return ["success"=>false,"message"=>$e->getMessage()];
        }
    }
}