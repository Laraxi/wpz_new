<?php
if(!defined('CORE'))exit("error!"); 

//首页	
if($do==""){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	$smt->assign('menus',getActions(1));
	$smt->display('Index.html');
	exit;
}

else if($do=="top"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	$smt->assign('menus',getActions(1));
	$smt->display('top.html');
	exit;
}

else if($do=="menu"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	$smt->assign('menus',getActions(1));
	$smt->display('menu.html');
	exit;
}

else if($do=="main"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	$smt->assign('menus',getActions(1));
	$smt->display('main.html');
	exit;
}
?>
