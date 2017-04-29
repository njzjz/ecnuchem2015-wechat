<?php
if($_GET['code']!=""){
	$cropid="wxa5ff24073b976f78";
	$secrect="t3iDlzFHtqcslE1M-AfWbjoNapbShGjUdWaEzE779r8nj1GC4lZsntzrZvOQHEip";
	$token_url="https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$cropid."&corpsecret=".$secrect;
	$response = file_get_contents($token_url);
    if (strpos($response, "callback") !== false){
		$lpos = strpos($response, "(");
		$rpos = strrpos($response, ")");
        $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
    }
	$msg = json_decode($response);
    if (isset($msg->access_token)){
        $access_token=$msg->access_token;
		setcookie('access_token',$access_token,time() + 259200);
    }else{
		echo "您没有权限登陆！";
		exit;
	}
	$token_url="https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=".$access_token."&code=".$_GET['code'];
	$response = file_get_contents($token_url);
    if (strpos($response, "callback") !== false){
       $lpos = strpos($response, "(");
       $rpos = strrpos($response, ")");
       $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
    }
	$msg = json_decode($response);
    if (isset($msg->UserId)){
		$UserId=$msg->UserId;
	  	setcookie('UserId',$UserId,time() + 259200);
    }else{
		echo "您没有权限登陆！";
		exit;
	}
	if(isset($_GET["url"])){
		header('Location: '.$_GET["url"]);
	}else{
		echo "错误！";
	}
	exit;
}else {
	$urlinfo = parse_url($url);
if(!empty($urlinfo))
	{
		$scheme = '';
		if (isset($urlinfo['scheme']) && $urlinfo['scheme']=='http')
		{
			$scheme = 'http://';
		}
		elseif (isset($urlinfo['scheme']) && $urlinfo['scheme']=='https')
		{
			$scheme = 'https://';
		}
		$urlname = $scheme.$urlinfo['host'];
		$urlport = $urlinfo['port'];
	}

	//注意 URL 一定要动态获取，不能hardcode
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

	$url = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	header("Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa5ff24073b976f78&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_base#wechat_redirect");
	exit;
}