<?php
if(!defined('CORE'))exit("error!"); 

//首页	
if($do==""){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	$smt->assign('menus',getActions(1));
	$smt->display('main.htm');
	exit;
}


?>
