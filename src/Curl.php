<?php
namespace Runo; 

class Curl{

	public static function  curl_get($url,$header='',$https=false)
	{
		$ch = curl_init();
	    $timeout = 10;
	    curl_setopt($ch, CURLOPT_URL, $url);
	    if($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);	  
		
	    $contents = curl_exec($ch);
	    curl_close($ch);
	    return  $contents;
	}

	public static function  curl_post($url,$data=[],$https=false)
	{
		//6-7uod0BNAxjI_I4SSO1
	}

	public static function  curl_multi($url,$https=false)
	{

	}
}
	
?>