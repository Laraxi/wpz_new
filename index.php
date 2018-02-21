<?php
//====================================================
//		FileName: index.php
//		Summary:  程序入口文件
//====================================================
session_start();
header("Content-type: text/html; charset=utf-8");
error_reporting(0);

@define("CORE",dirname(__FILE__)."/"); 	    //根目录

require_once("lib/init.php");               //配置文件

$user_info = array();
//当前用户信息
if (isset($_SESSION['userid'])) {
	$sql_config="SELECT u.money, u.expire, u.roleid, u.create_datetime, l.level_name FROM `$cfg[dbpre]user` AS u LEFT JOIN `$cfg[dbpre]level` AS l ON u.roleid=l.level_id WHERE u.id=$_SESSION[userid]";
	$db->query($sql_config);
	$row=$db->fetchRow();
	
	//判断VIP期限
	if ($row['expire']<time()) {
		$row['roleid'] = 1;
		$sql = "UPDATE $cfg[dbpre]user SET `roleid`=1 WHERE `id`=" . $_SESSION['userid'];
		$db->query($sql);
	}

	$user_info = array(
		'userid' => $_SESSION['userid'],
		'username' => $_SESSION['username'],
		'level_name' => $row['level_name'],
		'levelid' => $row['roleid'],
		'money' => $row['money'],
		'expire' => date("Y-m-d H:i:s", $row['expire']),
		'expire_date' => date("Y-m-d", $row['expire']),
		'expire_or' => $row['expire'],
		'create_datetime' => $row['create_datetime']
	);
	$smt->assign('user_info',$user_info);
}

$tpl_pre = "";
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
$uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";
if($ua == '' || preg_match($uachar, $ua))
{
	$tpl_pre = "m_";
	$smt->assign('is_mobile',1);
}


//执行页面
if ($action!=""){
	if (file_exists('app/action.'.$action.'.php')){
		include('app/action.'.$action.'.php');
	} else {
		exit($lang_cn['notfound']);
	}
} else {
	include('app/action.index.php');     //首页
}

?>