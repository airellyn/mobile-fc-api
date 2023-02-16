<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function FileUpload(Request $request){
        $uploaded_files = $request->file->store('public/uploads/');
        return ["result"=>$uploaded_files];

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
    }
}
