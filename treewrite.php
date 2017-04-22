<?php
require 'leancloud/src/autoload.php';
require 'wxsend.php';

use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\Exception;
// 参数依次为 AppId, AppKey, MasterKey
Client::initialize("4ac583jtEBobLFrc8fkLhoqt-MdYXbMMI", "CUgkcPtQWH5oUjp6JM2w8Y1q", "mrci1ns6xGTfJ6NbARyTrGQg");
Client::useRegion("US");
if(isset($_COOKIE['UserId'])&&isset($_COOKIE['access_token'])){
	$UserId=$_COOKIE['UserId'];
	$access_token=$_COOKIE['access_token'];
}else {
	header("Location:login.php?url=".urlencode($_SERVER['REQUEST_URI']));
	exit;
}
?>
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
			var_dump($weixinsend->send_text("","","14","7","树洞又有新内容了：".$content));
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