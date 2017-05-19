# Runo
鲁诺代码包

使用方法

1.composer require bzs1984/runo:"dev-master"

2.代码中使用
use Runo\Curl;
$url = 'http://xxxx.com";
$header = ['refer'=>'local'];//为空不用填

#get 方式
$response  = Curl::curl_get($url,$header);

#post 方式
$data = ['user'=>'zhangsan','passwrod'=>'1111'];
$header = ['refer'=>'local'];//为空不用填
$response  = Curl::curl_get($url,$data,$header);
