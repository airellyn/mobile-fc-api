<?php
namespace App\Http\Controllers;
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
use App\Models\SignNasabah;
use App\Models\Bayar;
use App\Models\Status;

use App\Library\Token;

class UserController extends Controller
{
	
	function login(Request $request) {			
        $username = strtolower($request->input('username'));
        $password = $request->input('password');
        $login = User::where('username', $username)
                     ->whereNotNull('id_collection')
                     ->first();

		$password = Hash::encrypt($password);
			
        if (!$login) {
            $res['success'] = false;
            $res['message'] = 'Username atau password salah!';
            return response($res,401);
        }else{
            if ($password == $login->pass) {
                $api_token = sha1(time());
				$create_token = User::where('id', $login->id)->update(['api_token' => $api_token]);
                
                $url = Settings::where('name','=','url')->first();

                $login = User::where('username', $username)->whereNotNull('id_collection')->first();
                $collector = Collector::where('ID','=',$login->id_collection)->first();

                $data_login = [
                    "id"=> $login->id,
                    "id_mitra"=> $login->id_mitra,
                    "id_collection" => $login->id_collection,
                    "id_kordinator" => $login->id_kordinator,
                    "username"=> $login->username,
                    "type"=> $login->type,
                    "is_web"=> $login->is_web,
                    "nama" => $collector->NAMA,
                    "foto" => $url->value.''.$collector->FOTO
                ];
                
                $res['success'] = true;
                $res['api_token'] = $api_token;
                $res['data'] = $data_login;
                return response($res);
            }else{
                Log::error($e->getMessage());
                $res['success'] = false;
                $res['message'] = 'Username atau password salah!';
                return response($res,401);
            }
        }
    }
    
    function data_collector(Request $request) {
        try {
            $keyword = $request->get('search');
            $user = Token::getToken($request->header('Authorization'));
            
            $url = Settings::where('name','=','url')->first();
            $collector = Collector::where('ID','=',$user->id_collection)->first();
            $nasabah = SignNasabah::select('nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','mitra.NAMA as MITRA','nasabah.COMPANY')
                                  ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
                                  ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
                                  ->where('sign_nasabah.IS_AKTIF','=',0)
                                  ->where('nasabah.IS_AKTIF','=',0)
                                  ->where('nasabah.IS_CLOSE','=',0)
                                  ->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection);

            $today  = Penagihan::where('ID_COLLECTOR',$user->id_collection)
                        ->whereBetween('CREATED_AT', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59']);
                      

            if (!empty($keyword)) { // for Search result
                $result = [];
                foreach ($nasabah->get() as $cust) {                    
                    if (
                        stripos($cust->NAMA, $keyword) !== false ||
                        stripos($cust->USER_ID, $keyword) !== false ) {
                        $status = 'found';                        
                        array_push($result, $cust);
                    }
                }

                if (!empty($result)) {
                    $res = [
                        'success'   => true,
                        'data'      => $result 
                    ];
                } else {
                    $res = [
                        'failed'    => true,
                        'message'   => '404: Data not found.'
                    ];
                }

                return response($res);
            
            } else {  // for display all records related to collectors
                $res = [
                    'success'   => true,
                    'nama'      => $collector->NAMA,
                    'leader'    => $collector->kordinator->NAMA,
                    'total'     => $nasabah->count(),
                    'borrowers' => $today->count(),
                    'amcoll'    => $today->selectRaw('SUM(AMCOLL) as "AMCOLL"')->pluck('AMCOLL')->first(),
                    'data'      => $nasabah->get()
                ];
                return response($res);
            }

        }catch ( \Exception $e ){
            Log::error($e->getMessage());
            $res['success'] = false;
            $res['message'] = 'Error: '.$e->getMessage();
            return response($res,500);
        }
    }

    function data_nasabah(Request $request) {
        try{
            $data_nasabah = array();
            $user = Token::getToken($request->header('Authorization'));
            $nasabah = Nasabah::select('nasabah.*','mitra.NAMA as MITRA')
                              ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
                              ->where('nasabah.ID','=',$request->get('id_nasabah'))
                              ->where('IS_CLOSE','=',0)
                              ->where('nasabah.IS_AKTIF','=',0)
                              ->first();

            $cek_jum = Nasabah::select('nasabah.*','mitra.NAMA as MITRA')
                              ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
                              ->where('nasabah.ID','=',$request->get('id_nasabah'))
                              ->where('IS_CLOSE','=',0)
                              ->where('nasabah.IS_AKTIF','=',0)
                              ->count();
            
            $status = Status::orderBy('NAMA','ASC')->get();
            $bayar = Bayar::orderBy('NAMA','ASC')->get();

            $url = Settings::where('name','=','url')->first();

            $sum_amcoll = Penagihan::select(DB::raw("SUM(AMCOLL) as sum_amcoll"))->where('ID_NASABAH','=',$request->get('id_nasabah'))->first();
            $tot_amcoll = (float) $sum_amcoll->sum_amcoll;
            
            /*$update_status = Penagihan::where('ID_COLLECTOR','=',$user->id_collection)->where('ID_NASABAH','=',$request->get('id_nasabah'))->orderBy('CREATED_AT','DESC')->first();
            $update_cek = Penagihan::where('ID_COLLECTOR','=',$user->id_collection)->where('ID_NASABAH','=',$request->get('id_nasabah'))->orderBy('CREATED_AT','DESC')->count();
            if($update_cek > 0 )
            {
                $status_visit =  $update_status->STATUS;
                $link = $url->value.''.$update_status->FOTO_RUMAH;
                $catatan = $update_status->KETERANGAN;
                $amcoll = $update_status->AMCOLL;
                $tanggal_ptp = date('Y-m-d',strtotime($update_status->TANGGAL_PTP));
                $status_bayar = $update_status->KET_BAYAR;
            }else{
                $status_visit =  null;
                $link = null;
                $catatan = null;
                $amcoll = 0;
                $tanggal_ptp = null;
                $status_bayar = null;
            }*/
            
            $status_visit =  null;
            $link = null;
            $catatan = null;
            $amcoll = 0;
            $tanggal_ptp = null;
            $status_bayar = null;
            
            if($cek_jum > 0){
                $data_nasabah = [
                        "ID"=> $nasabah->ID,
                        "ID_COLLECTOR"=> $user->id_collection,
                        "ID_MITRA"=> $nasabah->ID_MITRA,
                        "USER_ID"=>  $nasabah->USER_ID,
                        "NAMA"=> $nasabah->NAMA,
                        "TELEPHONE"=> $nasabah->TELEPHONE,
                        "TELEPHONE_RUMAH"=> $nasabah->TELEPHONE_RUMAH,
                        "TELEPHONE_KANTOR"=> $nasabah->TELEPHONE_KANTOR,
                        "ALAMAT"=> $nasabah->ALAMAT_RUMAH,
                        "ALAMAT_KANTOR"=> $nasabah->ALAMAT_KANTOR,
                        "TOTAL_TAGIHAN"=> round($nasabah->TOTAL_TAGIHAN-$tot_amcoll),
                        "STATUS_KUNJUNGAN"=>$status_visit,
                        "STATUS_PEMBAYARAN" => $status_bayar,
                        "CATATAN" => $catatan,
                        "FOTO" => $link,
                        "AMCOLL" => $amcoll,
                        "TANGGAL_PTP" => $tanggal_ptp,
                        "IS_CLOSE"=> $nasabah->IS_CLOSE,
                        "IS_AKTIF"=> $nasabah->IS_AKTIF,
                        "MITRA"=> $nasabah->MITRA,
                        "DPD"=> $nasabah->DPD,
                        "VA_BCA" => $nasabah->VA_BCA,
                        "VA_MANDIRI" => $nasabah->VA_MANDIRI,
                        "VA_PERMATA" => $nasabah->VA_PERMATA,
                        "CREATED_BY"=> $nasabah->CREATED_BY,
                        "UPDATED_BY"=> $nasabah->UPDATED_BY,
                        "CREATED_AT"=> date('Y-m-d H:i:s',strtotime($nasabah->CREATED_AT)),
                        "UPDATED_AT"=> date('Y-m-d H:i:s',strtotime($nasabah->UPDATED_AT))
                    ];
            }else{
                $data_nasabah = array();
            }
            
            $res['success'] = true;
            $res['bayar'] = $bayar;
            $res['status'] = $status;
            $res['data'] = $data_nasabah;
            return response($res);
        }catch ( \Exception $e ){
            Log::error($e->getMessage());
            $res['success'] = false;
            $res['message'] = 'Terdapat Kesalahan';
            return response($res,500);
        }
    }

}