<?php
//参数：$name, $tagid
//$name="订阅天气预报";
//$tagid=13;
if(isset($_COOKIE['UserId'])&&isset($_COOKIE['access_token'])){
	$UserId=$_COOKIE['UserId'];
	$access_token=$_COOKIE['access_token'];
}else {
	header("Location:login.php?url=".urlencode($_SERVER['REQUEST_URI']));
	exit;
}
if($_GET['state']!=""){
	$follow=true;
	$post_text=array(
		"tagid"=>$tagid,
		"userlist"=>array($UserId)
	);
	$output= json_encode($post_text,JSON_UNESCAPED_UNICODE);
	$headers = array("Content-Type: text/xml; charset=utf-8");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "$output");
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	if($_GET['state']=="follow"){
		$url="https://qyapi.weixin.qq.com/cgi-bin/tag/addtagusers?access_token=";
		$follow=true;
	}else{
		$url="https://qyapi.weixin.qq.com/cgi-bin/tag/deltagusers?access_token=";
		$follow=false;
	}
	curl_setopt($ch, CURLOPT_URL, $url.$access_token);
	$output = curl_exec($ch);
	curl_close($ch);
	$err=json_decode($output,TRUE);
	if ($err['errcode']){
		echo "err";
	}
}else{
	$token_url="https://qyapi.weixin.qq.com/cgi-bin/tag/get?access_token=".$access_token."&tagid=".$tagid;
	$response = file_get_contents($token_url);
	if (strpos($response, "callback") !== false){
		$lpos = strpos($response, "(");
		$rpos = strrpos($response, ")");
		$response  = substr($response, $lpos + 1, $rpos - $lpos -1);
	}
	$msg = json_decode($response);
	if (isset($msg->userlist)){
		$userlist=$msg->userlist;
		foreach($userlist as $user){
			if($user->userid==$UserId){
				$follow=true;
			}
		}
	}else exit;
}
if($follow){
	$button="取消";
	$state="unfollow";
}else{
	$button="";
	$state="follow";
}
?>
<html lang="zh-cn"><head><meta http-equiv="content-type" content="text/html; charset=utf-8" />
<? include "head.php";?>
<title><?=$name;?></title>
</head><body><section class="main-content">
<div class="weui-cells weui-cells_form"><form method='post' action='?state=<?=$state?>'>
<div class="weui-cell"><div class="weui-cell__bd"><table><tr><td><input type="submit" value="<?=$button;?><?=$name;?>" class="weui-btn weui-btn_primary"></td></tr></table></form></div></div></div>
</section>
</body></html>