<?php
namespace App\Library;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class Token 
{
	public static function getToken($token) 
	{
		$key = explode(' ',$token);
		$user = User::where('api_token', $key[1])->first();
		return $user;
    } 
}