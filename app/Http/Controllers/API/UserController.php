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
    
        // function data_collector(Request $request) {
        //     try {
        //         $keyword = $request->get('search');
        //         $user = Token::getToken($request->header('Authorization'));
                
        //         $url = Settings::where('name','=','url')->first();
        //         $collector = Collector::where('ID','=',$user->id_collection)->first();

        //         // $nasabah = SignNasabah::select('nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.KELURAHAN','nasabah.CREATED_AT')
        //         //                       ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
        //         //                       ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
        //         //                       ->where('sign_nasabah.IS_AKTIF','=',0)
        //         //                       ->where('nasabah.IS_AKTIF','=',0)
        //         //                       ->where('nasabah.IS_CLOSE','=',0)
        //         //                       ->where('nasabah.IS_VISIT','=',0)
        //         //                       ->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection);

        //         $nasabah = SignNasabah::select('nasabah.ID_MITRA','nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.TYPE_CREDIT','nasabah.COLL','nasabah.KELUARGA_DEKAT','nasabah.ALAMAT_KELUARGA_DEKAT','nasabah.KONTAK_DARURAT','nasabah.TANGGAL_WO','nasabah.KELURAHAN','nasabah.CREATED_AT')
        //                               ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
        //                               ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
        //                               ->where('sign_nasabah.IS_AKTIF','=',0)
        //                               ->where('nasabah.IS_AKTIF','=',0)
        //                               ->where('nasabah.IS_CLOSE','=',0)
        //                               ->where('nasabah.IS_VISIT','=',0)
        //                               ->whereNotIn('nasabah.ID_MITRA', [41])
        //                               ->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection)
        //                               ->orderBy('sign_nasabah.TANGGAL', 'desc');

        //         $nasabah_client = SignNasabah::select('nasabah.ID_MITRA','nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.TYPE_CREDIT','nasabah.COLL','nasabah.KELUARGA_DEKAT','nasabah.ALAMAT_KELUARGA_DEKAT','nasabah.KONTAK_DARURAT','nasabah.TANGGAL_WO','nasabah.KELURAHAN','nasabah.CREATED_AT')
        //                               ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
        //                               ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
        //                               ->where([
        //                                 ['sign_nasabah.IS_AKTIF', '=', 0],
        //                                 ['nasabah.IS_AKTIF', '=', 0],
        //                                 ['nasabah.IS_CLOSE', '=', 0],
        //                                 ['nasabah.IS_VISIT', '=', 0],
        //                                 ['sign_nasabah.ID_COLLECTOR', '=', $user->id_collection],
        //                                 ['nasabah.ID_MITRA', '=', 41],
        //                             ])
        //                             ->orderBy('sign_nasabah.TANGGAL', 'desc');

        //         $hasil_visit = SignNasabah::select('nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','penagihan.STATUS_VISIT as STATUS_VISIT','penagihan.STATUS_BAYAR as STATUS_BAYAR','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.KELURAHAN','penagihan.CREATED_AT')
        //                               ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
        //                               ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
        //                               ->join('penagihan','penagihan.ID_NASABAH','=','nasabah.ID')
        //                               //->join('nasabah.ID_NASABAH','=','penagihan','ID_NASABAH')
        //                               ->where('sign_nasabah.IS_AKTIF','=',0)
        //                               ->where('nasabah.IS_AKTIF','=',0)
        //                               //->where('nasabah.IS_CLOSE','=',1)
        //                               ->where('nasabah.IS_VISIT','=',1)
        //                               ->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection)
        //                               ->orderBy('CREATED_AT','DESC');

        //         $hasil_visit_bayar_full = SignNasabah::select('nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','penagihan.STATUS_VISIT as STATUS_VISIT','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.KELURAHAN','penagihan.CREATED_AT')
        //                               ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
        //                               ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
        //                               ->join('penagihan','penagihan.ID_NASABAH','=','nasabah.ID')
        //                               //->join('nasabah.ID_NASABAH','=','penagihan','ID_NASABAH')
        //                               ->where('sign_nasabah.IS_AKTIF','=',0)
        //                               ->where('nasabah.IS_CLOSE','=',0)
        //                               ->where('nasabah.IS_VISIT','=',1)
        //                               ->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection)
        //                               ->orderBy('CREATED_AT','DESC');
                                    

        //         $today  = Penagihan::where('ID_COLLECTOR',$user->id_collection)
        //                     ->whereBetween('CREATED_AT', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59']);

        //                     $jum_visit  = Penagihan::where('ID_COLLECTOR',$user->id_collection);
                        
        //         // $total_mitra  = Nasabah::select('nasabah.*')->join('mitra','mitra.ID','=','nasabah.ID_MITRA')->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection);

        //         if (!empty($keyword)) { // for Search result
        //             $result = [];
        //             foreach ($nasabah->get() as $cust) {                    
        //                 if (
        //                     stripos($cust->NAMA, $keyword) !== false ||
        //                     stripos($cust->USER_ID, $keyword) !== false ||
        //                  stripos($cust->KECAMATAN, $keyword) !== false ||
        //                  stripos($cust->KELURAHAN, $keyword) !== false ||
        //                  stripos($cust->MITRA, $keyword) !== false  ) {
        //                     $status = 'found';                        
        //                     array_push($result, $cust);
        //                 }
        //             }

        //             $hasil_visit_result = [];
        //                 foreach ($hasil_visit->get() as $cust) {
        //                     if (
        //                         stripos($cust->NAMA, $keyword) !== false ||
        //                         stripos($cust->USER_ID, $keyword) !== false ||
        //                         stripos($cust->KECAMATAN, $keyword) !== false ||
        //                         stripos($cust->KELURAHAN, $keyword) !== false ||
        //                         stripos($cust->MITRA, $keyword) !== false  
        //                     ) {
        //                         $status = 'found';                        
        //                         array_push($hasil_visit_result, $cust);
        //                     }
        //                 }

        //             if (!empty($result)) {
        //                 $res = [
        //                     'success'   => true,
        //                     'data'      => $result ,
        //                     'data_hasil_visit'      => $hasil_visit_result ,
        //                 ];
        //             } else {
        //                 $res = [
        //                     'failed'    => true,
        //                     'message'   => 'Maaf: Data tidak di temukan.'
        //                 ];
        //             }

        //             return response($res);
                
        //         } else {  // for display all records related to collectors
        //             $res = [
        //                 'success'               => true,
        //                 'nama'                  => $collector->NAMA,
        //                 'leader'                => $collector->kordinator->NAMA,
        //                 'total'                 => $nasabah->count(),
        //                 'total_borrowers'       => $nasabah->count(),
        //                 'total_borrowers_client'=> $nasabah_client->count(),
        //                 'borrowers'             => $today->count(),
        //                 'borrowers_visit'       => $jum_visit->count(),
        //                 'amcoll'                => $today->selectRaw('SUM(AMCOLL) as "AMCOLL"')->pluck('AMCOLL')->first(),
        //                 'amcoll_visit'          => $jum_visit->selectRaw('SUM(AMCOLL) as "AMCOLL"')->pluck('AMCOLL')->first(),
        //                 'data'                  => $nasabah->get(),
        //                 'data_client'                  => $nasabah_client->get(),
        //                 'total_borrowers_hasil_visit'  => $hasil_visit->count(),
        //                 //'total_mitra'        => $total_mitra->selectRaw('SUM(ID_MITRA) as "ID_MITRA"')->pluck('ID_MITRA')->first(),
        //                 // 'status_visit' => $list_penagihan->STATUS_VISIT,
        //                 'data_hasil_visit' => $hasil_visit->get()
        //             ];
        //             return response($res);
        //         }

        //     }catch ( \Exception $e ){
        //         Log::error($e->getMessage());
        //         $res['success'] = false;
        //         $res['message'] = 'Error: '.$e->getMessage();
        //         return response($res,500);
        //     }
        // }

    function data_collector(Request $request) {
        try {
            $keyword = $request->get('search');
            $user = Token::getToken($request->header('Authorization'));
            
            $url = Settings::where('name','=','url')->first();
            $collector = Collector::where('ID','=',$user->id_collection)->first();

            // $nasabah = SignNasabah::select('nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.KELURAHAN','nasabah.CREATED_AT')
            //                       ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
            //                       ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
            //                       ->where('sign_nasabah.IS_AKTIF','=',0)
            //                       ->where('nasabah.IS_AKTIF','=',0)
            //                       ->where('nasabah.IS_CLOSE','=',0)
            //                       ->where('nasabah.IS_VISIT','=',0)
            //                       ->whereNotIn('nasabah.ID_MITRA', [41])
            //                       ->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection);

            $nasabah = SignNasabah::select('nasabah.TYPE_MOTOR','nasabah.NO_POL','nasabah.LATITUDE','nasabah.LONGITUDE','nasabah.ID_MITRA','nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.TYPE_CREDIT','nasabah.COLL','nasabah.KELUARGA_DEKAT','nasabah.ALAMAT_KELUARGA_DEKAT','nasabah.KONTAK_DARURAT','nasabah.TANGGAL_WO','nasabah.KELURAHAN','nasabah.CREATED_AT')
                                  ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
                                  ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
                                  ->where('sign_nasabah.IS_AKTIF','=',0)
                                  ->where('nasabah.IS_AKTIF','=',0)
                                  ->where('nasabah.IS_CLOSE','=',0)
                                  ->where('nasabah.IS_VISIT','=',0)
                                  ->whereNotIn('nasabah.ID_MITRA', [41,42,45])
                                  ->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection)
                                  ;

            // $nasabah_client = SignNasabah::select('nasabah.ID_MITRA','nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.TYPE_CREDIT','nasabah.COLL','nasabah.KELUARGA_DEKAT','nasabah.ALAMAT_KELUARGA_DEKAT','nasabah.KONTAK_DARURAT','nasabah.TANGGAL_WO','nasabah.KELURAHAN','nasabah.CREATED_AT')
            //                       ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
            //                       ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
            //                       ->where([
            //                         ['sign_nasabah.IS_AKTIF', '=', 0],
            //                         ['nasabah.IS_AKTIF', '=', 0],
            //                         ['nasabah.IS_CLOSE', '=', 0],
            //                         ['nasabah.IS_VISIT', '=', 0],
            //                         ['sign_nasabah.ID_COLLECTOR', '=', $user->id_collection],
            //                     ])
            //                     ->where('nasabah.ID_MITRA', [41,42])
                                ;
            $nasabah_client = SignNasabah::select('nasabah.TYPE_MOTOR','nasabah.NO_POL','nasabah.LATITUDE','nasabah.LONGITUDE','nasabah.ID_MITRA','nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.TYPE_CREDIT','nasabah.COLL','nasabah.KELUARGA_DEKAT','nasabah.ALAMAT_KELUARGA_DEKAT','nasabah.KONTAK_DARURAT','nasabah.TANGGAL_WO','nasabah.KELURAHAN','nasabah.CREATED_AT')
                                  ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
                                  ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
                                  ->where('sign_nasabah.IS_AKTIF','=',0)
                                  ->where('nasabah.IS_AKTIF','=',0)
                                  ->where('nasabah.IS_CLOSE','=',0)
                                  ->where('nasabah.IS_VISIT','=',0)
                                  ->where(function ($query) {
                                        $query->orWhere('nasabah.ID_MITRA', 42)
                                            ->orWhere('nasabah.ID_MITRA', 41)
                                            ->orWhere('nasabah.ID_MITRA', 45);
                                    })
                                  ->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection)
                                  ;

            $hasil_visit = SignNasabah::select('nasabah.TYPE_MOTOR','nasabah.NO_POL','nasabah.LATITUDE','nasabah.LONGITUDE','nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','penagihan.STATUS_VISIT as STATUS_VISIT','penagihan.STATUS_BAYAR as STATUS_BAYAR','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.KELURAHAN','penagihan.CREATED_AT')
                                  ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
                                  ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
                                  ->join('penagihan','penagihan.ID_NASABAH','=','nasabah.ID')
                                  //->join('nasabah.ID_NASABAH','=','penagihan','ID_NASABAH')
                                  ->where('sign_nasabah.IS_AKTIF','=',0)
                                  ->where('nasabah.IS_AKTIF','=',0)
                                  //->where('nasabah.IS_CLOSE','=',1)
                                  ->where('nasabah.IS_VISIT','=',1)
                                  ->whereNotIn('nasabah.ID_MITRA', [41,42])
                                  ->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection)
                                  ->orderBy('CREATED_AT','DESC');

            $hasil_visit_client = SignNasabah::select('nasabah.TYPE_MOTOR','nasabah.NO_POL','nasabah.LATITUDE','nasabah.LONGITUDE','nasabah.ID_MITRA','nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','penagihan.STATUS_VISIT as STATUS_VISIT','penagihan.STATUS_BAYAR as STATUS_BAYAR','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.KELURAHAN','penagihan.CREATED_AT')
                                  ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
                                  ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
                                  ->join('penagihan','penagihan.ID_NASABAH','=','nasabah.ID')
                                  //->join('nasabah.ID_NASABAH','=','penagihan','ID_NASABAH')
                                  ->where('sign_nasabah.IS_AKTIF','=',0)
                                  ->where('nasabah.IS_AKTIF','=',0)
                                  //->where('nasabah.IS_CLOSE','=',1)
                                  ->where('nasabah.IS_VISIT','=',1)
                                  ->where(function ($query) {
                                    $query->orWhere('nasabah.ID_MITRA', 42)
                                        ->orWhere('nasabah.ID_MITRA', 41)
                                        ->orWhere('nasabah.ID_MITRA', 45);
                                  })
                                  ->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection)
                                  ->orderBy('CREATED_AT','DESC');

            $hasil_visit_bayar_full = SignNasabah::select('nasabah.TYPE_MOTOR','nasabah.NO_POL','nasabah.LATITUDE','nasabah.LONGITUDE','nasabah.NAMA','nasabah.USER_ID','nasabah.TELEPHONE','nasabah.TELEPHONE','nasabah.ID','penagihan.STATUS_VISIT as STATUS_VISIT','mitra.NAMA as MITRA','nasabah.COMPANY','nasabah.KECAMATAN','nasabah.KELURAHAN','penagihan.CREATED_AT')
                                  ->join('nasabah','nasabah.ID','=','sign_nasabah.ID_NASABAH')
                                  ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
                                  ->join('penagihan','penagihan.ID_NASABAH','=','nasabah.ID')
                                  //->join('nasabah.ID_NASABAH','=','penagihan','ID_NASABAH')
                                  ->where('sign_nasabah.IS_AKTIF','=',0)
                                  ->where('nasabah.IS_CLOSE','=',0)
                                  ->where('nasabah.IS_VISIT','=',1)
                                  ->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection)
                                  ->orderBy('CREATED_AT','DESC');
                                  

            $today  = Penagihan::where('ID_COLLECTOR',$user->id_collection)
            ->whereNotIn('ID_MITRA', [41,42])
                        ->whereBetween('CREATED_AT', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59']);

                        $today_client  = Penagihan::where('ID_COLLECTOR',$user->id_collection)
                        ->whereBetween('CREATED_AT', [date('Y-m-d').' 00:00:00',date('Y-m-d').' 23:59:59'])
                        ->where(function ($query) {
                            $query->orWhere('ID_MITRA', 42)
                                ->orWhere('ID_MITRA', 41);
                        });

                        $jum_visit  = Penagihan::where('ID_COLLECTOR',$user->id_collection)
                        ->whereNotIn('ID_MITRA', [41,42]);
                        $jum_visit_client  = Penagihan::where('ID_COLLECTOR',$user->id_collection)
                         ->where(function ($query) {
                            $query->orWhere('ID_MITRA', 42)
                                ->orWhere('ID_MITRA', 41)
                                ->orWhere('ID_MITRA', 45);

                        });
                      
            // $total_mitra  = Nasabah::select('nasabah.*')->join('mitra','mitra.ID','=','nasabah.ID_MITRA')->where('sign_nasabah.ID_COLLECTOR','=',$user->id_collection);

            if (!empty($keyword)) { // for Search result
                $result = [];
                foreach ($nasabah->get() as $cust) {                    
                    if (
                        stripos($cust->NAMA, $keyword) !== false ||
                        stripos($cust->USER_ID, $keyword) !== false ||
                        stripos($cust->KECAMATAN, $keyword) !== false ||
                        stripos($cust->TYPE_CREDIT, $keyword) !== false ||
                        stripos($cust->COLL, $keyword) !== false ||
                        stripos($cust->KELURAHAN, $keyword) !== false ||
                        stripos($cust->TYPE_MOTOR, $keyword) !== false ||
                        stripos($cust->NO_POL, $keyword) !== false ||
                        stripos($cust->LATITUDE, $keyword) !== false ||
                        stripos($cust->LONGITUDE, $keyword) !== false ||
                        stripos($cust->MITRA, $keyword) !== false  ) {
                        $status = 'found';                        
                        array_push($result, $cust);
                    }
                }

                $result_client = [];
                foreach ($nasabah_client->get() as $cust) {                    
                    if (
                        stripos($cust->NAMA, $keyword) !== false ||
                        stripos($cust->USER_ID, $keyword) !== false ||
                        stripos($cust->KECAMATAN, $keyword) !== false ||
                        stripos($cust->TYPE_CREDIT, $keyword) !== false ||
                        stripos($cust->COLL, $keyword) !== false ||
                        stripos($cust->KELURAHAN, $keyword) !== false ||
                        stripos($cust->TYPE_MOTOR, $keyword) !== false ||
                        stripos($cust->NO_POL, $keyword) !== false ||
                        stripos($cust->LATITUDE, $keyword) !== false ||
                        stripos($cust->LONGITUDE, $keyword) !== false ||
                        stripos($cust->MITRA, $keyword) !== false  ) {
                        $status = 'found';                        
                        array_push($result_client, $cust);
                    }
                }
                

                $hasil_visit_result = [];
                    foreach ($hasil_visit->get() as $cust) {
                        if (
                            stripos($cust->NAMA, $keyword) !== false ||
                            stripos($cust->USER_ID, $keyword) !== false ||
                            stripos($cust->KECAMATAN, $keyword) !== false ||
                            stripos($cust->TYPE_CREDIT, $keyword) !== false ||
                            stripos($cust->COLL, $keyword) !== false ||
                            stripos($cust->KELURAHAN, $keyword) !== false ||
                            stripos($cust->MITRA, $keyword) !== false  ) {
                            $status = 'found';                        
                            array_push($hasil_visit_result, $cust);
                        }

                    }

                    $hasil_visit_result_client = [];
                    foreach ($hasil_visit_client->get() as $cust) {
                        if (
                            stripos($cust->NAMA, $keyword) !== false ||
                            stripos($cust->USER_ID, $keyword) !== false ||
                            stripos($cust->KECAMATAN, $keyword) !== false ||
                            stripos($cust->TYPE_CREDIT, $keyword) !== false ||
                            stripos($cust->COLL, $keyword) !== false ||
                            stripos($cust->KELURAHAN, $keyword) !== false ||
                            stripos($cust->MITRA, $keyword) !== false  
                        ) {
                            $status = 'found';                        
                            array_push($hasil_visit_result_client, $cust);
                        }
                    }


                if (!empty($result)) {
                    $res = [
                        'success'   => true,
                        'data'      => $result,
                    ];
                } elseif (!empty($hasil_visit_result)) {
                    $res = [
                        'success'   => true,
                        'data_hasil_visit' => $hasil_visit_result
                    ];
                } elseif (!empty($result_client)) { 
                    $res = [
                        'success'   => true,
                        'data_client'      => $result_client 
                    ];
                } elseif (!empty($hasil_visit_result_client)) { 
                    $res = [
                        'success'   => true,
                        'data_hasil_visit_client'=> $hasil_visit_result_client
                    ];
                } else {
                    $res = [
                        'failed'    => true,
                        'message'   => 'Maaf: Data tidak di temukan.'
                    ];
                }

                // if (!empty($result)) {
                //     $res = [
                //         'success'   => true,
                //         'data'      => $result,
                //     ];
                // } elseif (!empty($hasil_visit_result)) {
                //     $res = [
                //         'success'   => true,
                //         'data_hasil_visit' => $hasil_visit_result
                //     ];
                // } elseif (!empty($result_client)) { 
                //     $res = [
                //         'success'   => true,
                //         'data_client'      => $result_client 
                //     ];
                // } elseif (!empty($hasil_visit_result_client)) { 
                //     $res = [
                //         'success'   => true,
                //         'data_hasil_visit_client'=> $hasil_visit_result_client
                //     ];
                // } elseif ((empty($result['koinworks']))){
                //     $res = [
                //         'failed'    => true,
                //         'message'   => 'Maaf: Data Koinworks atau SeaBank tidak ditemukan.'
                //     ];
                // }else {
                //     $res = [
                //         'failed'    => true,
                //         'message'   => 'Maaf: Data tidak di temukan.'
                //     ];
                // }
                

                return response($res);
            
            } else {  // for display all records related to collectors
                $res = [
                    'success'               => true,
                    'nama'                  => $collector->NAMA,
                    'leader'                => $collector->kordinator->NAMA,
                    'total'                 => $nasabah->count(),
                    'total_borrowers'                 => $nasabah->count(),
                    'total_borrowers_client'       => $nasabah_client->count(),
                    'borrowers'             => $today->count(),
                    'borrowers_client'             => $today_client->count(),
                    'borrowers_visit'       => $jum_visit->count(),
                    'borrowers_visit_client'       => $jum_visit_client->count(),
                    'amcoll'                => $today->selectRaw('SUM(AMCOLL) as "AMCOLL"')->pluck('AMCOLL')->first(),
                    'amcoll_client'                => $today_client->selectRaw('SUM(AMCOLL) as "AMCOLL"')->pluck('AMCOLL')->first(),
                    'amcoll_visit'          => $jum_visit->selectRaw('SUM(AMCOLL) as "AMCOLL"')->pluck('AMCOLL')->first(),
                    'amcoll_visit_client'          => $jum_visit_client->selectRaw('SUM(AMCOLL) as "AMCOLL"')->pluck('AMCOLL')->first(),
                    'data'                  => $nasabah->get(),
                    'data_client'                  => $nasabah_client->get(),
                    'total_borrowers_hasil_visit'  => $hasil_visit->count(),
                    //'total_mitra'        => $total_mitra->selectRaw('SUM(ID_MITRA) as "ID_MITRA"')->pluck('ID_MITRA')->first(),
                    // 'status_visit' => $list_penagihan->STATUS_VISIT,
                    'data_hasil_visit' => $hasil_visit->get(),
                    'data_hasil_visit_client' => $hasil_visit_client->get()
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

            //08-12-2023

            

            $nasabah_hasil_visit = Nasabah::select('nasabah.*','mitra.NAMA as MITRA')
                              ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
                              ->where('nasabah.ID','=',$request->get('id_nasabah'))
                              //->join('nasabah','nasabah.ID','=','penagihan.ID_NASABAH')
                              //->join('status','status.ID','=','penagihan.STATUS_VISIT')
                              ->where('IS_CLOSE','=',1)
                              ->where('nasabah.IS_AKTIF','=',0)
                              ->first();

            $cek_jum = Nasabah::select('nasabah.*','mitra.NAMA as MITRA')
                              ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
                              ->where('nasabah.ID','=',$request->get('id_nasabah'))
                              ->where('IS_CLOSE','=',0)
                              ->where('nasabah.IS_AKTIF','=',0)
                              ->count();

            // $cek_jum_hasil_visit = Nasabah::select('nasabah.*','mitra.NAMA as MITRA')
            //                   ->join('mitra','mitra.ID','=','nasabah.ID_MITRA')
            //                   ->where('nasabah.ID','=',$request->get('id_nasabah'))
            //                   ->where('IS_CLOSE','=',1)
            //                   ->where('nasabah.IS_AKTIF','=',0)
            //                   ->count();
            
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
            // PEMANGGILAN DATA
            if($cek_jum > 0){
                $data_nasabah = [
                        "ID"=> $nasabah->ID,
                        "ID_COLLECTOR"=> $user->id_collection,
                        "ID_MITRA"=> $nasabah->ID_MITRA,
                        "USER_ID"=>  $nasabah->USER_ID,
                        "NAMA"=> $nasabah->NAMA,
                        "NAMA_ALTERNATIF"=> $nasabah->NAMA_ALTERNATIF,
                        "TENOR"=> $nasabah->TENOR,
                        "TELEPHONE"=> $nasabah->TELEPHONE,
                        "TELEPHONE_RUMAH"=> $nasabah->tanggal_lahir,
                        "TELEPHONE_KANTOR"=> $nasabah->nama_kantor,
                        "ALAMAT"=> $nasabah->ALAMAT_RUMAH,
                        "ALAMAT_KTP"=> $nasabah->alamat_ktp,
                        "ALAMAT_KANTOR"=> $nasabah->ALAMAT_KANTOR,
                        "TYPE_CREDIT"=> $nasabah->TYPE_CREDIT,
                        "COLL"=> $nasabah->COLL,
                        "KELUARGA_DEKAT" => $nasabah->KELUARGA_DEKAT,
                        "ALAMAT_KELUARGA_DEKAT" => $nasabah->ALAMAT_KELUARGA_DEKAT,
                        "KONTAK_DARURAT" => $nasabah->KONTAK_DARURAT,
                        "TANGGAL_WO" => $nasabah->TANGGAL_WO,
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
                        "KECAMATAN"=> $nasabah->KECAMATAN,
                        "KELURAHAN"=> $nasabah->KELURAHAN,
                        "VA_BCA" => $nasabah->VA_BCA,
                        "TYPE_MOTOR" => $nasabah->TYPE_MOTOR,
                        "NO_POL" => $nasabah->NO_POL,
                        "LATITUDE" => $nasabah->LATITUDE,
                        "LONGITUDE" => $nasabah->LONGITUDE,
                        "VA_MANDIRI" => $nasabah->VA_MANDIRI,
                        "VA_PERMATA" => $nasabah->VA_PERMATA,
                        "CREATED_BY"=> $nasabah->CREATED_BY,
                        "UPDATED_BY"=> $nasabah->UPDATED_BY,
                        "CREATED_AT"=> date('Y-m-d H:i:s',strtotime($nasabah->CREATED_AT)),
                        "UPDATED_AT"=> date('Y-m-d H:i:s',strtotime($nasabah->UPDATED_AT))
                    ];
            }
            // else if($cek_jum_hasil_visit > 0){
            //     $data_nasabah_hasil_visit = [
            //         "ID"=> $nasabah_hasil_visit->ID,
            //         "ID_COLLECTOR"=> $user->id_collection,
            //         "ID_MITRA"=> $nasabah_hasil_visit->ID_MITRA,
            //         "USER_ID"=>  $nasabah_hasil_visit->USER_ID,
            //         "NAMA"=> $nasabah_hasil_visit->NAMA,
            //         "TELEPHONE"=> $nasabah_hasil_visit->TELEPHONE,
            //         "TELEPHONE_RUMAH"=> $nasabah_hasil_visit->tanggal_lahir,
            //         "TELEPHONE_KANTOR"=> $nasabah_hasil_visit->nama_kantor,
            //         "ALAMAT"=> $nasabah_hasil_visit->ALAMAT_RUMAH,
            //         "ALAMAT_KTP"=> $nasabah_hasil_visit->alamat_ktp,
            //         "ALAMAT_KANTOR"=> $nasabah_hasil_visit->ALAMAT_KANTOR,
            //         "TOTAL_TAGIHAN"=> round($nasabah_hasil_visit->TOTAL_TAGIHAN-$tot_amcoll),
            //         "STATUS_KUNJUNGAN"=>$status_visit,
            //         "STATUS_PEMBAYARAN" => $status_bayar,
            //         "CATATAN" => $catatan,
            //         "FOTO" => $link,
            //         "AMCOLL" => $amcoll,
            //         "TANGGAL_PTP" => $tanggal_ptp,
            //         "IS_CLOSE"=> $nasabah_hasil_visit->IS_CLOSE,
            //         "IS_AKTIF"=> $nasabah_hasil_visit->IS_AKTIF,
            //         "MITRA"=> $nasabah_hasil_visit->MITRA,
            //         "DPD"=> $nasabah_hasil_visit->DPD,
            //         "KECAMATAN"=> $nasabah_hasil_visit->KECAMATAN,
            //         "KELURAHAN"=> $nasabah_hasil_visit->KELURAHAN,
            //         "VA_BCA" => $nasabah_hasil_visit->VA_BCA,
            //         "VA_MANDIRI" => $nasabah_hasil_visit->VA_MANDIRI,
            //         "VA_PERMATA" => $nasabah_hasil_visit->VA_PERMATA,
            //         "CREATED_BY"=> $nasabah_hasil_visit->CREATED_BY,
            //         "UPDATED_BY"=> $nasabah_hasil_visit->UPDATED_BY,
            //         "CREATED_AT"=> date('Y-m-d H:i:s',strtotime($nasabah_hasil_visit->CREATED_AT)),
            //         "UPDATED_AT"=> date('Y-m-d H:i:s',strtotime($nasabah_hasil_visit->UPDATED_AT))
            //     ];
            // }
             else
            {
                $data_nasabah = array();
            }
            
            $res['success'] = true;
            $res['bayar'] = $bayar;
            $res['status'] = $status;
            $res['data'] = $data_nasabah;
            $res['data_client'] = $data_nasabah;
            $res['data_hasil_visit'] = $data_nasabah;
           // $res['data_hasil_visit'] = $data_nasabah_hasil_visit;
            return response($res);
        }catch ( \Exception $e ){
            Log::error($e->getMessage());
            $res['success'] = false;
            $res['message'] = 'Terdapat Kesalahan';
            return response($res,500);
        }
    }

}
