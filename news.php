<?php
require 'wxsend.php';
include("LIB_http.php");
include("LIB_parse.php");
include("LIB_rss.php");
$target = "http://www.ecnu.edu.cn/_wp3services/rssoffer?siteId=2&templateId=111&columnId=1952";
$mysqlname="localhost";
$filter_array[]="";
$rss_array = download_parse_rss($target, $filter_array);
$con=mysql_connect($mysqlname,"wx","root");
mysql_select_db("wx", $con);
$result = mysql_query("SELECT time FROM cron WHERE name='news' limit 1");
$row = mysql_fetch_array($result);
$last=$row['time'];
if($last==0) exit;
$now=$last;
for($i=0;$i<count($rss_array["ILINK"]);$i++){
	$time=strtotime($rss_array["IPUBDATE"][$i]);
	if($time>$last){
		$url=$rss_array["ILINK"][$i];
		$title=$rss_array["ITITLE"][$i];
		$description=$rss_array["IDESCRIPTION"][$i];
		$weixinsend = new weixin("wxa5ff24073b976f78","vahQDHWRWPE8nm7oXuOvna1NIibRk_RcGzIM5TwZ6btRBTd2HnSLakCZVtvee-B5");
		$weixinsend->send_news("","","15","2",$title,$description,$url);
		if($time>$now)$now=$time;
	}
}
mysql_query("UPDATE cron SET time=".$now." WHERE name ='news'");
mysql_close($con);