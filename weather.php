<?php  
  require 'leancloud/src/autoload.php';

use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\Exception;
// 参数依次为 AppId, AppKey, MasterKey
Client::initialize("EqNiLWx6KJ9O7XgvTcUNHFbo-gzGzoHsz", "ppaibnjf30sLOgrC1iMTmX21", "8yzIjkrTK4wmf8I527k1hKJg");
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
	$str=$str.$weather["text_day"]."转".$weather["text_night"]."，温度".$weather["low"]."℃-".$weather["high"]."℃"."，风力".$weather["wind_scale"]."级。";
	$query = new Query("WeatherFollow");
	$query->equalTo("follow",true);
	if($query->count()>0){
		$todos = $query->find();
		forEach($todos as $todo2) {
			$UserId = $todo2->get("UserId");
			if($UserId!=null) $UserId_sum=$UserId."|".$UserId_sum;
		}
		$UserId_sum=rtrim($UserId_sum,"|");
	}
	$todo = new Object("Weather");
	$todo->set("date", $weather["date"]."-".$day);
	$weixinsend = new weixin("wxa5ff24073b976f78","vahQDHWRWPE8nm7oXuOvna1NIibRk_RcGzIM5TwZ6btRBTd2HnSLakCZVtvee-B5");//实例化
	var_dump($weixinsend->send_text($UserId_sum,"","","23",$str));
	$todo->set("weather",true);
	try {
		$todo->save();
	} catch (CloudException $ex) {}
}
}
		
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
