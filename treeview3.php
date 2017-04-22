<table>
<?php
header("Access-Control-Allow-Origin: *");
require 'leancloud/src/autoload.php';

use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
// 参数依次为 AppId, AppKey, MasterKey
Client::initialize("4ac583jtEBobLFrc8fkLhoqt-MdYXbMMI", "CUgkcPtQWH5oUjp6JM2w8Y1q", "mrci1ns6xGTfJ6NbARyTrGQg");
Client::useRegion("US");
$query = new Query("TreeObject");
$query->descend("createdAt");
$todos = $query->find();
forEach($todos as $todo) {
    $content = $todo->get("content");
	$date=$todo->getCreatedAt();
	date_timezone_set($date, timezone_open('Asia/Shanghai'));
	$dateformat=date_format($date, 'Y-m-d H:i:s');
    if($content!=null)echo "<tr><td><section style='margin: 0.8em 0; padding: 0.6em; border: 1px solid #c0c8d1; border-radius: 0.3em; box-shadow: #aaa 0 0 0.6em; background-color: #fafaef;' class='ng-scope'><section style='padding: 0px;width:100%; margin: 0px; border: none; color: rgb(51, 51, 51); font-size: 1em; line-height: 1.4em; word-break: break-all; word-wrap: break-word; background-image: none; font-family: inherit; ' class='tn-page-ed-type-text ng-scope ng-valid tn-page-editable ng-dirty'><strong><span style='color: rgb(112, 48, 160); '>".$content."<br/>--".$dateformat."</span></section></section></td><tr>";
}
?>
</table>