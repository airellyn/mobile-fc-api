<?php
namespace App\Library;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class Hash 
{
	public static function encrypt($value)
	{
		$value = md5($value); 
		$value = base64_encode($value);
		
		return $value;
	}
}