<?php
require 'leancloud/src/autoload.php';

use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\Exception;
// 参数依次为 AppId, AppKey, MasterKey
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
	header("Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxa5ff24073b976f78&redirect_uri=https%3a%2f%2fchemapp.njzjz.win%2fwx%2ftreewrite.php&response_type=code&scope=snsapi_base#wechat_redirect");
	exit;
}?>
<html lang="zh-cn"><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><title>写树洞</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="/stylesheets/normalize.css" media="screen">
<link href='https://fonts.gmirror.org/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="/stylesheets/stylesheet.css" media="screen">
<link rel="stylesheet" type="text/css" href="/stylesheets/github-light.css" media="screen">
<link rel="stylesheet" type="text/css" href="https://res.wx.qq.com/open/libs/weui/1.0.2/weui.min.css" media="screen">
</head><body><section class="main-content">
<?php
if($_POST['input']!=""){
	$content=$_POST['input'];
	if($content!=null){
		$query = new Query("TreeObject");
		$query->equalTo("UserName",$UserId);
		$query->descend("createdAt");
		if($query->count()>0){
			$todo = $query->first();
			$content_his=$todo->get("content");
		}
		if($content_his!=$content){
		$testObject = new Object("TreeObject");
		$testObject->set("content", $content);
		$testObject->set("UserName",$UserId);
		try {
			$testObject->save();
			echo ' <div class="display" id="weui-mask">
			<div class="weui-mask"></div>
			<div class="weui-dialog">
			<div class="weui-dialog__hd"><strong class="weui-dialog__title">提交成功</strong></div>
			<div class="weui-dialog__bd">提交成功!</div>
			<div class="weui-dialog__ft">
            <a href="treewrite.php" class="weui-dialog__btn weui-dialog__btn_primary">确定</a>
			</div></div></div>';
			$weixinsend = new weixin("wxa5ff24073b976f78","t3iDlzFHtqcslE1M-AfWbjoNapbShGjUdWaEzE779r8nj1GC4lZsntzrZvOQHEip");//实例化
				$query = new Query("Follow");
				$query->equalTo("follow",true);
				if($query->count()>0){
					$todos = $query->find();
					forEach($todos as $todo) {
						$UserId = $todo->get("UserId");
						if($UserId!=null) $UserId_sum=$UserId."|".$UserId_sum;
					}
					$UserId_sum=rtrim($UserId_sum,"|");
				}
				var_dump($weixinsend->send_text($UserId_sum,"","","7","树洞又有新内容了：".$content));
		} catch (Exception $ex) {
			//echo "Save object fail!";
		}
		}else{
			echo ' <div class="display" id="weui-mask">
			<div class="weui-mask"></div>
			<div class="weui-dialog">
			<div class="weui-dialog__hd"><strong class="weui-dialog__title">提交失败</strong></div>
			<div class="weui-dialog__bd">不要重复发送相同内容!</div>
			<div class="weui-dialog__ft">
            <a href="treewrite.php" class="weui-dialog__btn weui-dialog__btn_primary">确定</a>
			</div></div></div>';
		}
	}
}
?>
<div class="weui-cells weui-cells_form"><form method='post' action='treewrite.php'><div class="weui-cell"><div class="weui-cell__bd"><table><tr><td><textarea rows="10" class="weui-textarea" name="input" placeholder="请输入你想扔进树洞的话~"></textarea></td></tr><tr><td><input type="submit" value="扔进树洞" class="weui-btn weui-btn_primary"></td></tr></table></form></div></div></div>
</section>
</body></html>
<?php
class weixin {
 private $appId;
 private $appSecret;
 public function __construct($appId, $appSecret) {
  $this->appId = $appId;
  $this->appSecret = $appSecret;
 }
  public function send_text($touser,$toparty,$totag,$agentid,$text,$safe="0") {
    /*
    消息类型msgtype text文本发送
    $touser 接收user 可选 |号隔开多个
    $toparty 接收部门 可选 |号隔开多个
    $totag 接收标签 可选 |号隔开多个
    $agentid 应用id 整型
    $text 发送内容 json
    $safe 是否加密 可选 布尔值
    */
    $post_text=array(
      'touser' => $touser, 
      'toparty' => $toparty, 
      'totag' => $totag, 
      'msgtype' => "text", //默认消息类型文本
      'agentid' => $agentid, 
      'text' => array('content'=>$text), 
      'safe' => $safe,       
      );
    $accessToken = $this->getAccessToken();
    $output= json_encode($post_text,JSON_UNESCAPED_UNICODE);
    $output= $this->http_post_get("https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=$accessToken","$output");
    return $this->err_echo($output);
  }
  private function getAccessToken() {
    //文本形式存储token，建议改造成适合自己的
    $data = json_decode(file_get_contents("access_token.json"));//获取口令
    if ($data->expire_time < time()) {//重新获取
      $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $res = json_decode($this->http_get($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $data->expire_time = time() + 7000;//应该是7200秒有效期 这样写容错
        $data->access_token = $access_token;
        $fp = fopen("access_token.json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }
  public function err_echo($errcode){
    $err=json_decode($errcode,TRUE);
    if ($err['errcode']){
      return $err['errmsg'];//错误消息
      //return $err['errcode'];//错误代码
    }
    return false;
  }
  public function http_post_get($url, $rawData)  {
    //post返回
    $headers = array("Content-Type: text/xml; charset=utf-8");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
  }
  private function http_get($url) {
    //获取token
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 20);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
  }
}