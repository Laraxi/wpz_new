<?php
if(!defined('CORE'))exit("error!"); 

//首页	
if($do==""){
	if(!isLogin()){exit($lang_cn_member['rabc_is_login']);} //判断是否登录
	$smt->assign('menus',getActions(1));
	$smt->display($tpl_pre.'main.htm');
	exit;
}


?>
