<?php
namespace Runo;  
/**
 * 百度坐标（BD09）、国测局坐标（火星坐标，GCJ02）、和WGS84坐标系之间的转换的工具
 * 
 * 参考 https://github.com/wandergis/coordtransform 实现的php版本
 * @author runo
 */
class CoordinateTransform
{
	static  $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
	// π
	static  $pi = 3.1415926535897932384626;
	// 长半轴
	static  $a = 6378245.0;
	// 扁率
	static  $ee = 0.00669342162296594323;

	/**
	 * 百度坐标系(BD-09)转WGS坐标
	 * 
	 * @param lng 百度坐标纬度
	 * @param lat 百度坐标经度
	 * @return WGS84坐标数组
	 */
	public static function bd09towgs84($lng, $lat) {
		$gcj   = self::bd09togcj02($lng, $lat);
		$wgs84 = self::gcj02towgs84($gcj[0], $gcj[1]);
		return $wgs84;
	}

	/**
	 * WGS坐标转百度坐标系(BD-09)
	 * 
	 * @param lng WGS84坐标系的经度
	 * @param lat WGS84坐标系的纬度
	 * @return 百度坐标数组
	 */
	public static function wgs84tobd09($lng, $lat) {
		$gcj  = self::wgs84togcj02($lng, $lat);
		$bd09 = self::gcj02tobd09($gcj[0], $gcj[1]);
		return $bd09;
	}

	/**
	 * 火星坐标系(GCJ-02)转百度坐标系(BD-09)
	 * 
	 * 谷歌、高德——>百度
	 * @param lng 火星坐标经度
	 * @param lat 火星坐标纬度
	 * @return 百度坐标数组
	 */
	public static function gcj02tobd09($lng, $lat) {
		$z      = sqrt($lng * $lng + $lat * $lat) + 0.00002 * sin($lat * self::$x_pi);
		$theta  = atan2($lat, $lng) + 0.000003 * cos($lng * self::$x_pi);
		$bd_lng = z * cos($theta) + 0.0065;
		$bd_lat = z * sin($theta) + 0.006;
		return [$bd_lng, $bd_lat];
	}

	/**
	 * 百度坐标系(BD-09)转火星坐标系(GCJ-02)
	 * 
	 * 百度——>谷歌、高德
	 * @param bd_lon 百度坐标纬度
	 * @param bd_lat 百度坐标经度
	 * @return 火星坐标数组
	 */
	public static function bd09togcj02($bd_lon, $bd_lat) {
		$x = $bd_lon - 0.0065;
		$y = $bd_lat - 0.006;
		$z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * self::$x_pi);
		$theta = atan2($y, $x) - 0.000003 * cos($x * self::$x_pi);
		$gg_lng = z * cos($theta);
		$gg_lat = z * sin($theta);
		return [$gg_lng, $gg_lat];
	}

	/**
	 * WGS84转GCJ02(火星坐标系)
	 * 
	 * @param lng WGS84坐标系的经度
	 * @param lat WGS84坐标系的纬度
	 * @return 火星坐标数组
	 */
	public static function wgs84togcj02($lng, $lat) {
		if (self::out_of_china($lng, $lat)) {
			return [$lng, $lat];
		}
		$dlat = self::transformlat($lng - 105.0, $lat - 35.0);
		$dlng = self::transformlng($lng - 105.0, $lat - 35.0);
		$radlat = $lat / 180.0 * self::$pi;
		$magic = sin($radlat);
		$magic = 1 - self::$ee * $magic * $magic;
		$sqrtmagic = sqrt($magic);
		$dlat = ($dlat * 180.0) / ((self::$a * (1 - self::$ee)) / ($magic * $sqrtmagic) * self::$pi);
		$dlng = ($dlng * 180.0) / (self::$a / $sqrtmagic * cos($radlat) * self::$pi);
		$mglat = $lat + $dlat;
		$mglng = $lng + $dlng;
		return [$mglng, $mglat];
	}

	/**
	 * GCJ02(火星坐标系)转GPS84
	 * 
	 * @param lng 火星坐标系的经度
	 * @param lat 火星坐标系纬度
	 * @return WGS84坐标数组
	 */
	public static function gcj02towgs84($lng, $lat) {
		if (self::out_of_china($lng, $lat)) {
			return [$lng, $lat];
		}
		$dlat = self::transformlat($lng - 105.0, $lat - 35.0);
		$dlng = self::transformlng($lng - 105.0, $lat - 35.0);
		$radlat = $lat / 180.0 * self::$pi;
		$magic = sin($radlat);
		$magic = 1 - self::$ee * $magic * $magic;
		$sqrtmagic = sqrt($magic);
		$dlat = ($dlat * 180.0) / (($a * (1 - $ee)) / ($magic * $sqrtmagic) * self::$pi);
		$dlng = ($dlng * 180.0) / ($a / $sqrtmagic * cos($radlat) * self::$pi);
		$mglat = $lat + $dlat;
		$mglng = $lng + $dlng;
		return [$lng * 2 - $mglng, $lat * 2 - $mglat];
	}

	/**
	 * 批量转换
	 *
	 * @param points 数组：多个gps经纬度坐标转成火星坐标
	 */
	public static function batch_wgs84togcj02($points)
	{
		$transfromPoints =  array_map("self::wgs84togcj02", $points);
		return $transfromPoints;
	}

	/**
	 * 纬度转换
	 * 
	 * @param lng
	 * @param lat
	 * @return
	 */
	public static function transformlat($lng, $lat) {
		$ret = -100.0 + 2.0 * $lng + 3.0 * $lat + 0.2 * $lat * $lat + 0.1 * $lng * $lat + 0.2 * sqrt(abs($lng));
		$ret += (20.0 * sin(6.0 * $lng * self::$pi) + 20.0 * sin(2.0 * $lng * self::$pi)) * 2.0 / 3.0;
		$ret += (20.0 * sin($lat * self::$pi) + 40.0 * sin($lat / 3.0 * self::$pi)) * 2.0 / 3.0;
		$ret += (160.0 * sin($lat / 12.0 * self::$pi) + 320 * sin($lat * self::$pi / 30.0)) * 2.0 / 3.0;
		return $ret;
	}

	/**
	 * 经度转换
	 * 
	 * @param lng
	 * @param lat
	 * @return
	 */
	public static function transformlng($lng, $lat) {
		$ret = 300.0 + $lng + 2.0 * $lat + 0.1 * $lng * $lng + 0.1 * $lng * $lat + 0.1 * sqrt(abs($lng));
		$ret += (20.0 * sin(6.0 * $lng * self::$pi) + 20.0 * sin(2.0 * $lng * self::$pi)) * 2.0 / 3.0;
		$ret += (20.0 * sin($lng * self::$pi) + 40.0 * sin($lng / 3.0 * self::$pi)) * 2.0 / 3.0;
		$ret += (150.0 * sin($lng / 12.0 * self::$pi) + 300.0 * sin($lng / 30.0 * self::$pi)) * 2.0 / 3.0;
		return $ret;
	}

	/**
	 * 判断是否在国内，不在国内不做偏移
	 * 
	 * @param lng
	 * @param lat
	 * @return
	 */
	public static function out_of_china($lng, $lat) {
		if ($lng < 72.004 || $lng > 137.8347) {
			return true;
		} else if ($lat < 0.8293 || $lat > 55.8271) {
			return true;
		}
		return false;
	}
}



		
	
?>