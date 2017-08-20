<?php
/**
 * 消息处理类
 */
namespace Runo; 

class Message
{

	/**
	 * 把laravel的异常发送到bug管理系统 
	 * @param string $url bug系统url
	 * @param array $data 数据信息
	 * @return mongodb id 
	 */	
	public static function  send_exception($url,$data=[])
	{
		$exception        = $data['exception'];
		$data['msg']      = $exception->getMessage();
        $data['file'] 	  = $exception->getFile();
        $data['line'] 	  = $exception->getLine();
        $data['trace']	  = $exception->getTraceAsString();
        $data['platform'] = $data['app_name'];
		$rs = Curl::curl_post($url,$data);
	}

	
}
	
?>