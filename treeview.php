<?php
header("Access-Control-Allow-Origin: *");
require 'load.php';
use \LeanCloud\Query;
if($_GET["ajax"]==1||$_POST["ajax"]==1){
	$query = new Query("TreeObject");
	$query->descend("createdAt");
	if($_POST["mode"]!="1"){
		$query->limit(15);
	}
	$todos = $query->find();
	echo "<table>";
	forEach($todos as $todo) {
		$content = $todo->get("content");
		$date=$todo->getCreatedAt();
		date_timezone_set($date, timezone_open('Asia/Shanghai'));
		$dateformat=date_format($date, 'Y-m-d H:i:s');
		if($content!=null)echo "<tr><td><section style='margin: 0.8em 0; padding: 0.6em; border: 1px solid #c0c8d1; border-radius: 0.3em; box-shadow: #aaa 0 0 0.6em; background-color: #fafaef;' class='ng-scope'><section style='padding: 0px;width:100%; margin: 0px; border: none; color: rgb(51, 51, 51); font-size: 1em; line-height: 1.4em; word-break: break-all; word-wrap: break-word; background-image: none; font-family: inherit; ' class='tn-page-ed-type-text ng-scope ng-valid tn-page-editable ng-dirty'><strong><span style='color: rgb(112, 48, 160); '>".$content."<br/>--".$dateformat."</span></section></section></td><tr>";
	}
	echo "</table>";
}else{
?>
<html lang="zh-cn"><head><? include "head.php";?>
<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/treeview.js"></script>
<title>树洞</title>
</head><body><section class="main-content"><table>
<input type="hidden" id="url" name="url" value="treeview.php"/>
<input type="hidden" id="mode" name="mode" value="<?=$_POST["mode"];?>"/>
<div id="main">加载中，请稍后……</div></section>
<div class="weui-cells weui-cells_form">
<form method='post' action='?' id="form">
<table><tr><td><input type=hidden name="mode" value="1">
<input type="submit" value="查看更多" class="weui-btn weui-btn_primary" id="more">
</td></tr></table></form></div>
</table></section>
</body></html>
<?php
}