<?php
require 'load.php';
require 'wxsend.php';
use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\Exception;
include("LIB_http.php");
include("LIB_parse.php");
include("LIB_rss.php");
$target = "http://www.ecnu.edu.cn/_wp3services/rssoffer?siteId=2&templateId=111&columnId=1952";
$filter_array[]="";
$rss_array = download_parse_rss($target, $filter_array);
$Newsquery = new Query("News");
for($i=0;$i<count($rss_array["ILINK"]);$i++){
	$url=$rss_array["ILINK"][$i];
	$Newsquery->equalTo("url",$url);
	if($Newsquery->count()==0){
		$title=$rss_array["ITITLE"][$i];
		$description=$rss_array["IDESCRIPTION"][$i];
		$weixinsend = new weixin("wxa5ff24073b976f78","vahQDHWRWPE8nm7oXuOvna1NIibRk_RcGzIM5TwZ6btRBTd2HnSLakCZVtvee-B5");//实例化
		if($weixinsend->send_news("","","15","2",$title,$description,$url)==0){
			$todo = new Object("News");
			$todo->set("url", $url);
			try {
				$todo->save();
			} catch (CloudException $ex) {}
		}
	}else{
		if($i>10)break;
	}
}