<?php  
require 'wxsend.php';
$mysqlname="localhost";
date_default_timezone_set('Asia/Shanghai');
$h=(int)date('H');
if($h==7||$h==20){
	$now=time();
	$con=mysql_connect($mysqlname,"wx","root");
	mysql_select_db("wx", $con);
	$result = mysql_query("SELECT time FROM cron WHERE name='weather' limit 1");
	$row = mysql_fetch_array($result);
	$last=$row['time'];
	if($now-$last>3600){
		if($h==7)$day="0";else $day="1";
		$str = file_get_contents("https://api.thinkpage.cn/v3/weather/daily.json?key=TZ0L1V14QX&location=31.033:121.449&language=zh-Hans&unit=c&start=0&days=5");  
		$arr = json_decode($str,TRUE);         
		$weather=$arr["results"][0]["daily"][(int)$day];
		if($day=="0")$str="今日";else $str="明日";
		if($weather["text_day"]==$weather["text_night"]){
			$text=$weather["text_night"];
		}else{
			$text=$weather["text_day"]."转".$weather["text_night"];
		}
		$str=$str.$text."，温度".$weather["low"]."℃-".$weather["high"]."℃"."，风力".$weather["wind_scale"]."级。";
		$weixinsend = new weixin("wxa5ff24073b976f78","vahQDHWRWPE8nm7oXuOvna1NIibRk_RcGzIM5TwZ6btRBTd2HnSLakCZVtvee-B5");
		$weixinsend->send_text("","","13","23",$str);
		mysql_query("UPDATE cron SET time=".$now." WHERE name ='weather'");
	}
	mysql_close($con);
}