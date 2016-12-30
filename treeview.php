<html lang="zh-cn"><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><title>树洞</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="/stylesheets/normalize.css" media="screen">
<link href='https://fonts.gmirror.org/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="/stylesheets/stylesheet.css" media="screen">
<link rel="stylesheet" type="text/css" href="/stylesheets/github-light.css" media="screen">
<link rel="stylesheet" type="text/css" href="https://res.wx.qq.com/open/libs/weui/1.0.2/weui.min.css" media="screen">
</head><body><section class="main-content"><table>
<?php
require 'leancloud/src/autoload.php';

use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
// 参数依次为 AppId, AppKey, MasterKey
Client::initialize("EqNiLWx6KJ9O7XgvTcUNHFbo-gzGzoHsz", "ppaibnjf30sLOgrC1iMTmX21", "8yzIjkrTK4wmf8I527k1hKJg");
$query = new Query("TreeObject");
$query->descend("createdAt");
if($_POST["mode"]!="1"){
	$query->limit(15);
}
$todos = $query->find();
forEach($todos as $todo) {
    $content = $todo->get("content");
	$date=$todo->getCreatedAt();
	date_timezone_set($date, timezone_open('Asia/Shanghai'));
	$dateformat=date_format($date, 'Y-m-d H:i:s');
    if($content!=null)echo "<tr><td><section style='margin: 0.8em 0; padding: 0.6em; border: 1px solid #c0c8d1; border-radius: 0.3em; box-shadow: #aaa 0 0 0.6em; background-color: #fafaef;' class='ng-scope'><section style='padding: 0px;width:100%; margin: 0px; border: none; color: rgb(51, 51, 51); font-size: 1em; line-height: 1.4em; word-break: break-all; word-wrap: break-word; background-image: none; font-family: inherit; ' class='tn-page-ed-type-text ng-scope ng-valid tn-page-editable ng-dirty'><strong><span style='color: rgb(112, 48, 160); '>".$content."<br/>--".$dateformat."</span></section></section></td><tr>";
}
if($_POST["mode"]!="1"){
?>
<div class="weui-cells weui-cells_form"><form method='post' action='treeview.php'><div class="weui-cell"><div class="weui-cell__bd"><table><tr><td><input type=hidden name="mode" value="1"><input type="submit" value="查看更多" class="weui-btn weui-btn_primary"></td></tr></table></form></div></div></div>
<?php
}
?>
</table></section>
</body></html>