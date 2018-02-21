<?php
//====================================================
//		FileName: admin.php
//		Summary:  后台入口文件
//====================================================
session_start();
header("Content-type: text/html; charset=utf-8");
error_reporting(0);

@define("CORE",dirname(__FILE__)."/"); 	    //根目录
@define("ADMINPATH",true); 	                //后台标识

require_once("lib/init.php");               //配置文件

//读取用户数组
$sql_user="SELECT id,username FROM `$cfg[dbpre]user`";
$db->query($sql_user);
$user_arr=$db->fetchAll();
foreach($user_arr as $key=>$val){
	$user_list[$user_arr[$key][id]]=$user_arr[$key]['username'];	 //用户数组
}

//读取在线用户
//先删除20分钟未活动的用户
$sql_user="DELETE FROM `$cfg[dbpre]online_user` WHERE last_login<'".(time()-1200)."'";
$db->query($sql_user);

$sql_user="SELECT * FROM `$cfg[dbpre]online_user`";
$db->query($sql_user);
$online_user_arr=array();
$online_user_arr['user_list']=$db->fetchAll();
$online_user_arr['count'] = count ($online_user_arr['user_list']);
$smt->assign('online_user_arr',$online_user_arr);

//判断超时
if ($_SESSION['last_do_time'] && $_SESSION['time_out']) {
	if (time() - $_SESSION['last_do_time'] > $_SESSION['time_out']) {
		//先更新最新登录
		$sql = "UPDATE $cfg[dbpre]user SET last_login = '".time()."' WHERE `id`=" . $_SESSION['userid'];
		$db->query($sql);
		
		//删除在线用户表
		$sql = "DELETE FROM $cfg[dbpre]online_user WHERE `user_id`=" . $_SESSION['userid'];
		$db->query($sql);
		
		//清空session
		$_SESSION = array();
		session_destroy();
		
		$_SESSION = array();
		session_destroy();
		exit($lang_cn['rabc_timeout']);
	} else {
		$_SESSION['last_do_time'] = time();
	}
} else if ($_SESSION['userid']) {
	$sql = "UPDATE $cfg[dbpre]online_user SET last_login = '".time()."' WHERE `id`=" . $_SESSION['userid'];
	$db->query($sql);
}

//执行页面
if ($action!=""){
	if (file_exists('action/action.'.$action.'.php')){
		include('action/action.'.$action.'.php');
	} else {
		exit($lang_cn['notfound']);
	}
} else {
	include('action/action.index.php');     //首页
}

?>