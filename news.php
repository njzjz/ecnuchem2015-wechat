<?php
require 'leancloud/src/autoload.php';
use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\Exception;
Client::initialize("EqNiLWx6KJ9O7XgvTcUNHFbo-gzGzoHsz", "ppaibnjf30sLOgrC1iMTmX21", "8yzIjkrTK4wmf8I527k1hKJg");

	include("LIB_http.php");
	include("LIB_parse.php");
	include("LIB_rss.php");
	$target = "http://www.ecnu.edu.cn/_wp3services/rssoffer?siteId=2&templateId=111&columnId=1952";
	$filter_array[]="";
	$rss_array = download_parse_rss($target, $filter_array);
    //print_r($rss_array);
	
	$Newsquery = new Query("News");
	$weixinsend = new weixin("wxa5ff24073b976f78","vahQDHWRWPE8nm7oXuOvna1NIibRk_RcGzIM5TwZ6btRBTd2HnSLakCZVtvee-B5");//实例化
	$query = new Query("NewsFollow");
	$query->equalTo("follow",true);
	if($query->count()>0){
		$todos = $query->find();
		forEach($todos as $todo) {
			$UserId = $todo->get("UserId");
			if($UserId!=null) $UserId_sum=$UserId."|".$UserId_sum;
		}
	$UserId_sum=rtrim($UserId_sum,"|");
	}
		$i=0;
for($i=0;$i<count($rss_array["ILINK"]);$i++){
	$url=$rss_array["ILINK"][$i];
	$Newsquery->equalTo("url",$url);
	if($Newsquery->count()==0){
		$title=$rss_array["ITITLE"][$i];
		$description=$rss_array["IDESCRIPTION"][$i];
		var_dump($weixinsend->send_text($UserId_sum,"","","2",$title,$description,$url));
		$todo = new Object("News");
		$todo->set("url", $url);
		try {
			$todo->save();
		} catch (CloudException $ex) {}
	}else{
		if($i>5)break;
	}
}
	

	class weixin {
 private $appId;
 private $appSecret;
 public function __construct($appId, $appSecret) {
  $this->appId = $appId;
  $this->appSecret = $appSecret;
 }
  public function send_text($touser,$toparty,$totag,$agentid,$title,$description,$url,$safe="0") {
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
      'msgtype' => "news", //默认消息类型文本
      'agentid' => $agentid, 
      'news' => array( 'articles'=>array(array('title'=>$title,'description'=>$description,'url'=>$url))), 
      );
    $accessToken = $this->getAccessToken();
    $output= json_encode($post_text,JSON_UNESCAPED_UNICODE);
	//echo $output;
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
