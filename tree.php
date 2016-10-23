<?php

include_once "WXBizMsgCrypt.php";
require 'leancloud/src/autoload.php';

use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\Exception;
// 参数依次为 AppId, AppKey, MasterKey
Client::initialize("EqNiLWx6KJ9O7XgvTcUNHFbo-gzGzoHsz", "ppaibnjf30sLOgrC1iMTmX21", "8yzIjkrTK4wmf8I527k1hKJg");

// 假设企业号在公众平台上设置的参数如下
$encodingAesKey = "oCkoXqstzOPTEVc3CLTJytxEc7Pvo7OA8c5hpE4Clsk";
$token = "zengjinzhe";
$corpId = "wxa5ff24073b976f78";

/*
------------使用示例一：验证回调URL---------------
*企业开启回调模式时，企业号会向验证url发送一个get请求 
假设点击验证时，企业收到类似请求：
* GET /cgi-bin/wxpush?msg_signature=5c45ff5e21c57e6ad56bac8758b79b1d9ac89fd3&timestamp=1409659589&nonce=263014780&echostr=P9nAzCzyDtyTWESHep1vC5X9xho%2FqYX3Zpb4yKa9SKld1DsH3Iyt3tP3zNdtp%2B4RPcs8TgAE7OaBO%2BFZXvnaqQ%3D%3D 
* HTTP/1.1 Host: qy.weixin.qq.com

接收到该请求时，企业应
1.解析出Get请求的参数，包括消息体签名(msg_signature)，时间戳(timestamp)，随机数字串(nonce)以及公众平台推送过来的随机加密字符串(echostr),
这一步注意作URL解码。
2.验证消息体签名的正确性 
3. 解密出echostr原文，将原文当作Get请求的response，返回给公众平台
第2，3步可以用公众平台提供的库函数VerifyURL来实现。

*/

$sVerifyMsgSig = $_GET["msg_signature"];     //此处改为_GET方法用于获得数据，下面一样
$sVerifyTimeStamp = $_GET["timestamp"];
$sVerifyNonce = $_GET["nonce"];
$sVerifyEchoStr = $_GET["echostr"];
// $sVerifyMsgSig = HttpUtils.ParseUrl("msg_signature");
//$sVerifyMsgSig = "5c45ff5e21c57e6ad56bac8758b79b1d9ac89fd3";
// $sVerifyTimeStamp = HttpUtils.ParseUrl("timestamp");
//$sVerifyTimeStamp = "1409659589";
// $sVerifyNonce = HttpUtils.ParseUrl("nonce");
//$sVerifyNonce = "263014780";
// $sVerifyEchoStr = HttpUtils.ParseUrl("echostr");
//$sVerifyEchoStr = "P9nAzCzyDtyTWESHep1vC5X9xho/qYX3Zpb4yKa9SKld1DsH3Iyt3tP3zNdtp+4RPcs8TgAE7OaBO+FZXvnaqQ==";

// 需要返回的明文
$sEchoStr = "";

$wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
/*
$errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
if ($errCode == 0) {
	//
	// 验证URL成功，将sEchoStr返回
	// HttpUtils.SetResponce($sEchoStr);
	echo $sEchoStr;
} else {
	print("ERR: " . $errCode . "\n\n");
}
*/
/*
------------使用示例二：对用户回复的消息解密---------------
用户回复消息或者点击事件响应时，企业会收到回调消息，此消息是经过公众平台加密之后的密文以post形式发送给企业，密文格式请参考官方文档
假设企业收到公众平台的回调消息如下：
POST /cgi-bin/wxpush? msg_signature=477715d11cdb4164915debcba66cb864d751f3e6&timestamp=1409659813&nonce=1372623149 HTTP/1.1
Host: qy.weixin.qq.com
Content-Length: 613
<xml>
<ToUserName><![CDATA[wx5823bf96d3bd56c7]]></ToUserName><Encrypt><![CDATA[RypEvHKD8QQKFhvQ6QleEB4J58tiPdvo+rtK1I9qca6aM/wvqnLSV5zEPeusUiX5L5X/0lWfrf0QADHHhGd3QczcdCUpj911L3vg3W/sYYvuJTs3TUUkSUXxaccAS0qhxchrRYt66wiSpGLYL42aM6A8dTT+6k4aSknmPj48kzJs8qLjvd4Xgpue06DOdnLxAUHzM6+kDZ+HMZfJYuR+LtwGc2hgf5gsijff0ekUNXZiqATP7PF5mZxZ3Izoun1s4zG4LUMnvw2r+KqCKIw+3IQH03v+BCA9nMELNqbSf6tiWSrXJB3LAVGUcallcrw8V2t9EL4EhzJWrQUax5wLVMNS0+rUPA3k22Ncx4XXZS9o0MBH27Bo6BpNelZpS+/uh9KsNlY6bHCmJU9p8g7m3fVKn28H3KDYA5Pl/T8Z1ptDAVe0lXdQ2YoyyH2uyPIGHBZZIs2pDBS8R07+qN+E7Q==]]></Encrypt>
<AgentID><![CDATA[218]]></AgentID>
</xml>

企业收到post请求之后应该
1.解析出url上的参数，包括消息体签名(msg_signature)，时间戳(timestamp)以及随机数字串(nonce)
2.验证消息体签名的正确性。
3.将post请求的数据进行xml解析，并将<Encrypt>标签的内容进行解密，解密出来的明文即是用户回复消息的明文，明文格式请参考官方文档
第2，3步可以用公众平台提供的库函数DecryptMsg来实现。
*/

// $sReqMsgSig = HttpUtils.ParseUrl("msg_signature");
//$sReqMsgSig = "477715d11cdb4164915debcba66cb864d751f3e6";
// $sReqTimeStamp = HttpUtils.ParseUrl("timestamp");
//$sReqTimeStamp = "1409659813";
// $sReqNonce = HttpUtils.ParseUrl("nonce");
//$sReqNonce = "1372623149";
$sReqMsgSig = $_GET["msg_signature"];     //此处改为_GET方法用于获得数据，下面一样
$sReqTimeStamp = $_GET["timestamp"];
$sReqNonce = $_GET["nonce"];
// post请求的密文数据
 $sReqData = $GLOBALS["HTTP_RAW_POST_DATA"];
//$sReqData = "<xml><ToUserName><![CDATA[wx5823bf96d3bd56c7]]></ToUserName><Encrypt><![CDATA[RypEvHKD8QQKFhvQ6QleEB4J58tiPdvo+rtK1I9qca6aM/wvqnLSV5zEPeusUiX5L5X/0lWfrf0QADHHhGd3QczcdCUpj911L3vg3W/sYYvuJTs3TUUkSUXxaccAS0qhxchrRYt66wiSpGLYL42aM6A8dTT+6k4aSknmPj48kzJs8qLjvd4Xgpue06DOdnLxAUHzM6+kDZ+HMZfJYuR+LtwGc2hgf5gsijff0ekUNXZiqATP7PF5mZxZ3Izoun1s4zG4LUMnvw2r+KqCKIw+3IQH03v+BCA9nMELNqbSf6tiWSrXJB3LAVGUcallcrw8V2t9EL4EhzJWrQUax5wLVMNS0+rUPA3k22Ncx4XXZS9o0MBH27Bo6BpNelZpS+/uh9KsNlY6bHCmJU9p8g7m3fVKn28H3KDYA5Pl/T8Z1ptDAVe0lXdQ2YoyyH2uyPIGHBZZIs2pDBS8R07+qN+E7Q==]]></Encrypt><AgentID><![CDATA[218]]></AgentID></xml>";
$sMsg = "";  // 解析之后的明文
$errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);
if ($errCode == 0) {
	// 解密成功，sMsg即为xml格式的明文
	// TODO: 对明文的处理
	// For example:
	$xml = new DOMDocument();
	$xml->loadXML($sMsg);
	$content = $xml->getElementsByTagName('Content')->item(0)->nodeValue;
	$UserName= $xml -> getElementsByTagName('FromUserName')->item(0)->nodeValue;
	if($content!=null){
		$query = new Query("TreeObject");
		$query->equalTo("UserName",$UserName);
		$query->descend("createdAt");
		if($query->count()>0){
			$todo = $query->first();
			$content_his=$todo->get("content");
		}
		if($content_his!=$content){
		$testObject = new Object("TreeObject");
		$testObject->set("content", $content);
		$testObject->set("UserName",$UserName);
		try {
			$testObject->save();
			//echo "Save object success!";
			$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						</xml>";        
			$msgType="text";
			$contentStr="成功发送！现在您可以<a href='http://wx.njzjz.win/treeview.php'>进入树洞</a>查看您的悄悄话了！";   
			$sRespData = sprintf($textTpl, $corpId, $UserName, $sReqTimeStamp, $msgType, $contentStr);
			$sEncryptMsg = ""; //xml格式的密文
			$errCode = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);
			if ($errCode == 0) {
				echo $sEncryptMsg;  //回复加密之后的密文
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
			} else {
				print("ERR: " . $errCode . "\n\n");
				// exit(-1);
			}
		} catch (Exception $ex) {
			//echo "Save object fail!";
		}
		}else{
			$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						</xml>";        
			$msgType="text";
			$contentStr="成功发送！现在您可以<a href='http://wx.njzjz.win/treeview.php'>进入树洞</a>查看您的悄悄话了！";   
			$sRespData = sprintf($textTpl, $corpId, $UserName, $sReqTimeStamp, $msgType, $contentStr);
			$sEncryptMsg = ""; //xml格式的密文
			$errCode = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);
			if ($errCode == 0) {
				echo $sEncryptMsg; 
			}else {
				print("ERR: " . $errCode . "\n\n");
				// exit(-1);
			}
		}
	}
} else {
	print("ERR: " . $errCode . "\n\n");
	//exit(-1);
}

/*
------------使用示例三：企业回复用户消息的加密---------------
企业被动回复用户的消息也需要进行加密，并且拼接成密文格式的xml串。
假设企业需要回复用户的明文如下：
<xml>
<ToUserName><![CDATA[mycreate]]></ToUserName>
<FromUserName><![CDATA[wx5823bf96d3bd56c7]]></FromUserName>
<CreateTime>1348831860</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[this is a test]]></Content>
<MsgId>1234567890123456</MsgId>
<AgentID>128</AgentID>
</xml>

为了将此段明文回复给用户，企业应：
1.自己生成时间时间戳(timestamp),随机数字串(nonce)以便生成消息体签名，也可以直接用从公众平台的post url上解析出的对应值。
2.将明文加密得到密文。
3.用密文，步骤1生成的timestamp,nonce和企业在公众平台设定的token生成消息体签名。
4.将密文，消息体签名，时间戳，随机数字串拼接成xml格式的字符串，发送给企业号。
以上2，3，4步可以用公众平台提供的库函数EncryptMsg来实现。
*/

// 需要发送的明文
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
