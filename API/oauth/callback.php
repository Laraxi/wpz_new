<?php
require_once("../qqConnectAPI.php");

$qc = new QC();
$acs = $qc->qq_callback();
$oid = $qc->get_openid();
$qc = new QC($acs,$oid);
//$uinfo = $qc->get_user_info();

$arr = $qc->get_user_info();

if ($arr) {
	$arr['openid'] = $oid;
	session_start();
	$_SESSION['qq_info'] = $arr;
	
	header("Location:/index.php?action=member&do=qqloginok");
} else {
	echo '登录失败，<a href="/">返回首页</a>';
}
/*echo '<meta charset="UTF-8">';
echo "<p>";
echo "Gender:".$arr["gender"];
echo "</p>";
echo "<p>";
echo "NickName:".$arr["nickname"];
echo "</p>";
echo "<p>";
echo "<img src=\"".$arr['figureurl']."\">";
echo "<p>";
echo "<p>";
echo "<img src=\"".$arr['figureurl_1']."\">";
echo "<p>";
echo "<p>";
echo "<img src=\"".$arr['figureurl_2']."\">";
echo "<p>";
echo "vip:".$arr["vip"];
echo "</p>";
echo "level:".$arr["level"];
echo "</p>";
echo "is_yellow_year_vip:".$arr["is_yellow_year_vip"];
echo "</p>";*/


