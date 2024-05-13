<?php 
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Library\Hash;

use App\Models\User;
use App\Models\Settings;
use App\Models\Collector;
use App\Models\Nasabah;
use App\Models\Penagihan;

use App\Library\Token;

class NasabahController extends Controller
{
    function update_status(Request $request) {
        try{
            Log::error($request->getContent());
            $user = Token::getToken($request->header('Authorization'));
            $json = json_decode( $request->getContent() );
            
            // dd($json);

            $url = Settings::where('name','=','url_update_status')->first();
            $urlPost = $url->value;
            
            $sum_amcoll = Penagihan::select(
                DB::raw("SUM(AMCOLL) as total_amcoll"))
                ->where('ID_NASABAH','=',$json->id_nasabah)
                ->first();              

            $nasabah = Nasabah::where('ID','=',$json->id_nasabah)->first();

            $sisa_tagihan = 0;
            $total_amcoll = 0;

            if (empty($sum_amcoll->total_amcoll) || $sum_amcoll->total_amcoll == 0){
                $sisa_tagihan = $nasabah->TOTAL_TAGIHAN - $json->amcoll;
                $total_amcoll = $json->amcoll;
            } else {
                $total_amcoll = $json->amcoll + $sum_amcoll->total_amcoll;
                $sisa_tagihan = $nasabah->TOTAL_TAGIHAN - $total_amcoll;
            }
            if(isset($json->foto_kwitansi)) {
                $foto_kwitansi = $json->foto_kwitansi;
            }else{
                $foto_kwitansi = '';
            }

            if(isset($json->foto_jalan)) {
                    $foto_jalan = $json->foto_jalan;
                }else{
                    $foto_jalan = '';
                }
        
                if(isset($json->foto)) {
                    $foto = $json->foto;
                }else{
                    $foto = '';
                }
            
            if ($json->status_visit == '') {
                $res = ['status' => false, 'message' => 'Status tidak boleh kosong!'];
                return response($res,422);
            }

            if (($json->status_visit == 2)||($json->status_bayar == 2)) {
                if($json->tanggal_ptp == ''){
                    $res = ['status' => false, 'message' => 'Tanggal PTP tidak boleh kosong!'];
                    return response($res,422);
                }
            }
            if(($json->status_visit == 2)&&($json->status_bayar != 2)) {
                $res = ['status' => false, 'message' => 'Metode Pembayaran harus diisi Janji Bayar/PTP'];
                return response($res,422);
            }
            if(($json->status_visit != 2)&&($json->status_bayar == 2)) {
                $res = ['status' => false, 'message' => 'Status Kunjungan harus diisi Janji Bayar/PTP'];
                return response($res,422);
            }

            if(($json->status_visit == 1)&&($json->status_bayar == 2)) {
                $res = ['status' => false, 'message' => 'Metode Pembayaran harus diisi Bayar Full/Bayar Sebagian'];
                return response($res,422);
            }
            if(($json->status_visit == 1)&&($json->amcoll == '')) {
                $res = ['status' => false, 'message' => 'Amcoll tidak boleh kosong!'];
                return response($res,422);
            }
        
            if(($json->status_visit == 7)&&($json->foto_jalan == '')) {
                $res = ['status' => false, 'message' => 'Foto Rumah atau Jalan tidak boleh kosong!'];
                return response($res,422);
            }
            if(($json->status_visit == 6)&&($json->foto_jalan == '')) {
                $res = ['status' => false, 'message' => 'Foto Rumah atau Jalan tidak boleh kosong!'];
                return response($res,422);
            }
            if(($json->status_visit == 5)&&($json->foto_jalan == '')) {
                $res = ['status' => false, 'message' => 'Foto Rumah atau Jalan tidak boleh kosong!'];
                return response($res,422);
            }
            if(($json->status_visit == 4)&&($json->foto_jalan == '')) {
                $res = ['status' => false, 'message' => 'Foto Rumah atau Jalan tidak boleh kosong!'];
                return response($res,422);
            }
            if(($json->status_visit == 3)&&($json->foto_jalan == '')) {
                $res = ['status' => false, 'message' => 'Foto Rumah atau Jalan tidak boleh kosong!'];
                return response($res,422);
            }

            if(($json->status_visit == 7)&&($json->foto == '')) {
                $res = ['status' => false, 'message' => 'Foto Rumah atau Jalan tidak boleh kosong!'];
                return response($res,422);
            }
            if(($json->status_visit == 6)&&($json->foto == '')) {
                $res = ['status' => false, 'message' => 'Foto Rumah atau Jalan tidak boleh kosong!'];
                return response($res,422);
            }
            if(($json->status_visit == 5)&&($json->foto == '')) {
                $res = ['status' => false, 'message' => 'Foto Rumah atau Jalan tidak boleh kosong!'];
                return response($res,422);
            }
            if(($json->status_visit == 4)&&($json->foto == '')) {
                $res = ['status' => false, 'message' => 'Foto Rumah atau Jalan tidak boleh kosong!'];
                return response($res,422);
            }
            if(($json->status_visit == 3)&&($json->foto == '')) {
                $res = ['status' => false, 'message' => 'Foto Rumah atau Jalan tidak boleh kosong!'];
                return response($res,422);
            }

            if ($sisa_tagihan < 0) {
                $kelebihan = $sisa_tagihan;
                $res = [
                    'status' => false, 
                    'message' => 'Nilai Amcoll yang di isi berlebih Rp. '.number_format(substr($sisa_tagihan,1),2,'.',',')
                ];
                return response($res,422);
            
            } else if ($sisa_tagihan > 0) {
                if($json->amcoll > round($sisa_tagihan) || $sum_amcoll->total_amcoll > round($sisa_tagihan)) {
                    $res = ['status' => false, 'message' => 'Nilai Amcoll yang di isi melebihi sisa tunggakan.'];
                    return response($res,422);
                }
            }
            /** if $sisa_tagihan == 0 , auto lunas **/
            
            if((in_array($json->status_visit, [3,4,5,6,7]))){
                if($json->catatan == '') {
                    $res = ['status' => false, 'message' => 'Catatan tidak boleh kosong'];
                    return response($res,422);
                }
            }
            
            $data_json = [
                'id_collection' => $user->id_collection,
                'id_nasabah'    => $json->id_nasabah,
                'id_mitra'      => $json->id_mitra,
                'status_visit'  => $json->status_visit,
                'status_bayar'  => $json->status_bayar,
                'amcoll'        => $json->amcoll,
                'total_amcoll'  => $total_amcoll,
                'tanggal_ptp'   => $json->tanggal_ptp,
                // 'type_credit' => $json->type_credit,
                // 'keluarga_dekat' => $json->keluarga_dekat,
                // 'alamat_keluarga_dekat' => $json->alamat_keluarga_dekat,
                // 'kontak_darurat' => $json->kontak_darurat,
                // 'tanggal_wo' => $json->tanggal_wo,
                'catatan'       => $json->catatan,
                'foto_rumah'    => $foto,
                'foto_jalan'    => $foto_jalan,
                'foto_kwitansi' => $foto_kwitansi,
                'sisa_tagihan'  => round($sisa_tagihan),  //watch this, important!
                'lat'           => $json->lat,
                'lang'          => $json->lang
            ];
            
            
            // dd($data_json);

            $client = new Client();
            $respon = $client->post(
                $urlPost, ['body' => json_encode($data_json)]
            );
            $response = json_decode($respon->getBody()->getContents());
            
            if($response->status == true)
            {
                $res['success'] = true;
                $res['message'] = $response->message;
                return response($res);
            }else{
                $res['success'] = false;
                $res['message'] = 'Data tidak tersimpan';
                return response($res,422);
            }   
        }catch ( \Exception $e ){
            Log::error($e->getMessage());
            $res['success'] = false;
            $res['message'] = $e->getMessage();
            return response($res,500);
        }
    }

    // function update_image(Request $request) {
    //     try{
    //         Log::error($request->getContent());
    //         $user = Token::getToken($request->header('Authorization'));
    //         $json = json_decode( $request->getContent() );
            
    //         // dd($json);

    //         $url = Settings::where('name','=','url_update_image')->first();
    //         $urlPost = $url->value;
            
    //         $sum_amcoll = Penagihan::select(DB::raw("SUM(AMCOLL) as total_amcoll"))
    //             ->where('ID_NASABAH','=',$json->id_naupdate_imagesabah)
    //             ->first();              

    //         $nasabah = Nasabah::where('ID','=',$json->id_nasabah)->first();

    //         $sisa_tagihan = 0;
    //         $total_amcoll = 0;
            
    //         $data_json = [
    //             'id_collection' => $user->id_collection,
    //             'id_nasabah'    => $json->id_nasabah,
    //             'id_mitra'      => $json->id_mitra,
    //             'status_visit'  => $json->status_visit,
    //             'status_bayar'  => $json->status_bayar,
    //             'amcoll'        => $json->amcoll,
    //             'total_amcoll'  => $total_amcoll,
    //             'tanggal_ptp'   => $json->tanggal_ptp,
    //             // 'type_credit' => $json->type_credit,
    //             // 'keluarga_dekat' => $json->keluarga_dekat,
    //             // 'alamat_keluarga_dekat' => $json->alamat_keluarga_dekat,
    //             // 'kontak_darurat' => $json->kontak_darurat,
    //             // 'tanggal_wo' => $json->tanggal_wo,
    //             'catatan'       => $json->catatan,
    //             'foto_rumah'    => $foto,
    //          'foto_jalan'    => $foto_jalan,
    //             'foto_kwitansi' => $foto_kwitansi,
    //             'sisa_tagihan'  => round($sisa_tagihan),  //watch this, important!
    //             'lat'           => $json->lat,
    //             'lang'          => $json->lang
    //         ];
            
            
    //         // dd($data_json);

    //         $client = new Client();
    //         $respon = $client->post(
    //             $urlPost, ['body' => json_encode($data_json)]
    //         );
    //         $response = json_decode($respon->getBody()->getContents());
            
    //         if($response->status == true)
    //         {
    //             $res['success'] = true;
    //             $res['message'] = $response->message;
    //             return response($res);
    //         }else{
    //             $res['success'] = false;
    //             $res['message'] = 'Data tidak tersimpan';
    //             return response($res,422);
    //         }   
    //     }catch ( \Exception $e ){
    //         Log::error($e->getMessage());
    //         $res['success'] = false;
    //         $res['message'] = $e->getMessage();
    //         return response($res,500);
    //     }
    // }
}
