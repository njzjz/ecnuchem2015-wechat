<?php  
require 'leancloud/src/autoload.php';
require 'wxsend.php';
use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\Exception;
Client::initialize("4ac583jtEBobLFrc8fkLhoqt-MdYXbMMI", "CUgkcPtQWH5oUjp6JM2w8Y1q", "mrci1ns6xGTfJ6NbARyTrGQg");
Client::useRegion("US");
date_default_timezone_set('Asia/Shanghai');
$h=(int)date('H');
if($h==7)$day="0";
if($h==20)$day="1";
if($h==7||$h==20){
	$str = file_get_contents("https://api.thinkpage.cn/v3/weather/daily.json?key=TZ0L1V14QX&location=31.033:121.449&language=zh-Hans&unit=c&start=0&days=5");  
	$arr = json_decode($str,TRUE);         
	$weather=$arr["results"][0]["daily"][(int)$day];
	if($day=="0")$str="今日";else $str="明日";
	$weatherquery = new Query("Weather");
	$weatherquery->equalTo("date",$weather["date"]."-".$day);
	if($weatherquery->count()==0){
		if($weather["text_day"]==$weather["text_night"]){
			$text=$weather["text_night"];
		}else{
			$text=$weather["text_day"]."转".$weather["text_night"];
		}
		$str=$str.$text."，温度".$weather["low"]."℃-".$weather["high"]."℃"."，风力".$weather["wind_scale"]."级。";
		$todo = new Object("Weather");
		$todo->set("date", $weather["date"]."-".$day);
		$weixinsend = new weixin("wxa5ff24073b976f78","vahQDHWRWPE8nm7oXuOvna1NIibRk_RcGzIM5TwZ6btRBTd2HnSLakCZVtvee-B5");//实例化
		var_dump($weixinsend->send_text("","","13","23",$str));
		$todo->set("weather",true);
		try {
			$todo->save();
		} catch (CloudException $ex) {}
	}
}