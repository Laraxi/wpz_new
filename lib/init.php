<?php
//====================================================
//		FileName: init.php
//		Summary:  初始化
//====================================================

if(!defined('CORE'))exit("error!"); 
//当前时区
date_default_timezone_set('asia/shanghai');

//引入类库及公共方法
require_once("data/config.php");            //配置文件
if(defined('ADMINPATH')) {
	require_once("data/config_admin.php");  //后台配置文件
}
require_once("lib/mysql.class.php");        //数据类
require_once("lib/smarty.class.php");       //模版类
require_once('lib/json.class.php');		    //JSON类
require_once("lib/func.class.php");         //核心类
require_once("lib/rabc.class.php");         //权限类
require_once("lib/image.class.php");        //图片类
require_once("lib/page.class.php");         //分页类

//模板引擎
$smt = new smarty();
smarty_cfg($smt);

//初始化数据库连接
$db	= new mysql($cfg["dbhost"],$cfg["dbuser"],$cfg["dbpass"],$cfg["dbname"]);

//读取网站设置信息
$sql_config = "SELECT * FROM `$cfg[dbpre]setting` WHERE id=1";
$db->query($sql_config);
$config_arr = $db->fetchRow();
foreach($config_arr as $key=>$val){
	if ($key!="id") {
		$cfg[$key] = $val;
	}
}
$smt->assign('cfg',$cfg);

//操作值
$action = empty($_GET['action']) ? '' : trim($_GET['action']); 	 //get action值
$do = empty($_GET['do']) ? '' : trim($_GET['do']);			 	 //get do值

//语言包
require_once("lang/".$cfg['lang']."/common.php");
if(file_exists("lang/".$cfg['lang']."/".$action.".php")) {
    require_once("lang/".$cfg['lang']."/".$action.".php");
}
if(defined('ADMINPATH')) {
	require_once("lang/".$cfg['lang']."/admin.php");
}
$lang = $_LANG;
$smt->assign('lang',$lang);


?>