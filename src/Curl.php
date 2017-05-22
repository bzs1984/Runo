<?php
namespace Runo; 

class Curl
{

	public static function  curl_get($url,$header=[])
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

	public static function  curl_post($url,$data=[],$header=[])
	{
		$ch = curl_init();
	    $timeout = 10;
	    curl_setopt($ch, CURLOPT_URL, $url);
	    if($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);	
	    curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);	  
		
	    $contents = curl_exec($ch);
	    curl_close($ch);
	    return  $contents;
	}

	public static function curl_https($url,$header=[])
	{
		$curl = curl_init();
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		// 当请求https的数据时，会要求证书，这时候，加上下面这两个参数，规避ssl的证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$contents = curl_exec($ch);
	    curl_close($ch);
	    return  $contents;
	}

	public static function  curl_multi($url,$https=false)
	{

	}
}
	
?>