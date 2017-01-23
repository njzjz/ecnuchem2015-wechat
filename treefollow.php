<?php
require 'leancloud/src/autoload.php';
use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\Exception;
Client::initialize("4ac583jtEBobLFrc8fkLhoqt-MdYXbMMI", "CUgkcPtQWH5oUjp6JM2w8Y1q", "mrci1ns6xGTfJ6NbARyTrGQg");
Client::useRegion("US");
if(isset($_COOKIE['UserId'])){
	$UserId=$_COOKIE['UserId'];
}elseif($_GET['code']!=""){
	$cropid="wxa5ff24073b976f78";
	$secrect="t3iDlzFHtqcslE1M-AfWbjoNapbShGjUdWaEzE779r8nj1GC4lZsntzrZvOQHEip";
	$token_url="https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$cropid."&corpsecret=".$secrect;
	$response = file_get_contents($token_url);
     if (strpos($response, "callback") !== false)
     {
        $lpos = strpos($response, "(");
        $rpos = strrpos($response, ")");
        $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
     }
	 $msg = json_decode($response);
     if (isset($msg->access_token))
     {
        $access_token=$msg->access_token;
     }else exit;

	$token_url="https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=".$access_token."&code=".$_GET['code'];
	$response = file_get_contents($token_url);

     if (strpos($response, "callback") !== false)
     {
        $lpos = strpos($response, "(");
        $rpos = strrpos($response, ")");
        $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
     }
	 $msg = json_decode($response);
     if (isset($msg->UserId))
     {
       $UserId=$msg->UserId;
	   	setcookie('UserId',$UserId,time() + 259200);
     }else exit;
}else {
	header("Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa5ff24073b976f78&redirect_uri=https%3a%2f%2fchemapp.njzjz.win%2fwx%2ftest.php&response_type=code&scope=snsapi_base#wechat_redirect");
	exit;
}
	$query = new Query("Follow");
	$query->equalTo("UserId",$UserId);
	if($_GET['state']!=""){
		if($query->count()>0){
			$todo = $query->first();
		}else{
			$todo = new Object("Follow");
			$todo->set("UserId", $UserId);
		}
		if($_GET['state']=="follow"){
			$follow=true;
			$todo->set("follow", true);
		}
		else{
			$follow=false;
			$todo->set("follow", false);
		}
		try {
			$todo->save();
		} catch (CloudException $ex) {}
	}else{
		if($query->count()>0){
			$todo = $query->first();
			$follow=$todo->get("follow");
		}else $follow=false;	
	}

	if($follow==true){
		$button="取消";
		$state="unfollow";
	}else{
		$button="";
		$state="follow";
	}
	?>
	<html lang="zh-cn"><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><title>关注树洞新消息</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="/stylesheets/normalize.css" media="screen">
<link href='https://fonts.gmirror.org/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="/stylesheets/stylesheet.css" media="screen">
<link rel="stylesheet" type="text/css" href="/stylesheets/github-light.css" media="screen">
<link rel="stylesheet" type="text/css" href="https://res.wx.qq.com/open/libs/weui/1.0.2/weui.min.css" media="screen">
</head><body><section class="main-content">
<div class="weui-cells weui-cells_form"><form method='post' action='treefollow.php?state=<?=$state?>'>
<div class="weui-cell"><div class="weui-cell__bd"><table><tr><td><input type="submit" value="<?=$button?>订阅树洞新消息" class="weui-btn weui-btn_primary"></td></tr></table></form></div></div></div>
</section>
</body></html>