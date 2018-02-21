<?php
if(!defined('CORE'))exit("error!"); 

//列表	
if($do==""){}

//充值记录
if($do=="paylog"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	$search = " WHERE 1=1 ";
	$search_type = (isset($_GET['search_type']) && $_GET['search_type']!="all") ? " OR " : " AND ";
	if(isset($_GET['username']) && $_GET['username']!=""){
		$search .= $search_type . " u.username LIKE '%".strip_tags($_GET['username'])."%'";
	}
	if (isset($_GET['create_datetime_start']) && $_GET['create_datetime_start']!=""){
		$search .= $search_type . " UNIX_TIMESTAMP(u.create_datetime) < " . strtotime($_GET['create_datetime_start']);
	}
	if (isset($_GET['create_datetime_end']) && $_GET['create_datetime_end']!=""){
		$search .= $search_type . " UNIX_TIMESTAMP(u.create_datetime) < " . strtotime($_GET['create_datetime_end']);
	}
	$search = $search!=""&&$search_type==" OR " ? " AND (" . ltrim($search, " OR") . ")" : $search;
	
	//设置分页
	if($_POST[numPerPage]==""){$numPerPage=$cfg['page_size'];}else{$numPerPage=$_POST[numPerPage];}
	if($_GET[pageNum]==""||$_GET[pageNum]=="0" ){$pageNum="0";}else{$pageNum=($_GET[pageNum]-1)*$numPerPage;}
	$num=mysql_query("SELECT p.* FROM $cfg[dbpre]pay_log AS p LEFT JOIN $cfg[dbpre]user AS u ON p.user_id=u.id $search");//当前频道条数
	$total=mysql_num_rows($num);//总条数	
	$page=new page(array('total'=>$total,'perpage'=>$numPerPage));

	$sql="SELECT p.* FROM $cfg[dbpre]pay_log AS p LEFT JOIN $cfg[dbpre]user AS u ON p.user_id=u.id $search ORDER BY id DESC LIMIT $pageNum,$numPerPage";
	$db->query($sql);
	$list=$db->fetchAll();

	$smt->assign('list',$list);
	$smt->assign('menus',getActions(1));
	$smt->assign('numPerPage',$_POST[numPerPage]); //显示条数
	$smt->assign('pageNum',$_GET[pageNum]); //当前页数
	$smt->assign('total',$total);  //总条数
	$smt->assign ('page',$page->show());
	$smt->assign('title',$lang['title']['charge_log']);
	$smt->display('Html_2011.html');
	exit;
}

//删除充值记录
if($do=="delpaylog"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	$sql="DELETE FROM $cfg[dbpre]pay_log WHERE id IN (" . $_GET['id'] . ")";
	if($db->query($sql)){
		echo success($lang['tips']['del_success'],"?action=member&do=paylog");
	} else {
		echo error($lang['tips']['del_fail']);
	}
	adminlog($lang['log']['del_pay_log'] . $_GET['id']);
	exit;
}

//Z币记录
if($do=="accountlog"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	$search = " WHERE 1=1 ";
	$search_type = (isset($_GET['search_type']) && $_GET['search_type']!="all") ? " OR " : " AND ";
	if(isset($_GET['username']) && $_GET['username']!=""){
		$search .= $search_type . " u.username LIKE '%".strip_tags($_GET['username'])."%'";
	}
	if (isset($_GET['create_datetime_start']) && $_GET['create_datetime_start']!=""){
		$search .= $search_type . " UNIX_TIMESTAMP(u.create_datetime) < " . strtotime($_GET['create_datetime_start']);
	}
	if (isset($_GET['create_datetime_end']) && $_GET['create_datetime_end']!=""){
		$search .= $search_type . " UNIX_TIMESTAMP(u.create_datetime) < " . strtotime($_GET['create_datetime_end']);
	}
	$search = $search!=""&&$search_type==" OR " ? " AND (" . ltrim($search, " OR") . ")" : $search;
	
	//设置分页
	if($_POST[numPerPage]==""){$numPerPage=$cfg['page_size'];}else{$numPerPage=$_POST[numPerPage];}
	if($_GET[pageNum]==""||$_GET[pageNum]=="0" ){$pageNum="0";}else{$pageNum=($_GET[pageNum]-1)*$numPerPage;}
	$num=mysql_query("SELECT a.* FROM $cfg[dbpre]account_log AS a LEFT JOIN $cfg[dbpre]user AS u ON a.user_id=u.id $search");//当前频道条数
	$total=mysql_num_rows($num);//总条数	
	$page=new page(array('total'=>$total,'perpage'=>$numPerPage));

	$sql="SELECT a.* FROM $cfg[dbpre]account_log AS a LEFT JOIN $cfg[dbpre]user AS u ON a.user_id=u.id $search ORDER BY id DESC LIMIT $pageNum,$numPerPage";
	$db->query($sql);
	$list=$db->fetchAll();

	$smt->assign('list',$list);
	$smt->assign('menus',getActions(1));
	$smt->assign('numPerPage',$_POST[numPerPage]); //显示条数
	$smt->assign('pageNum',$_GET[pageNum]); //当前页数
	$smt->assign('total',$total);  //总条数
	$smt->assign ('page',$page->show());
	$smt->assign('title',sprintf($lang['title']['coin_log'],$cfg['coin_name']));
	$smt->display('Html_2012.html');
	exit;
}

//删除Z币记录
if($do=="delaccountlog"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	$sql="DELETE FROM $cfg[dbpre]account_log WHERE id IN (" . $_GET['id'] . ")";
	if($db->query($sql)){
		echo success($lang['tips']['del_success'],"?action=member&do=accountlog");
	} else {
		echo error($lang['tips']['del_fail']);
	}
	adminlog($lang['log']['del_account_log'] . $_GET['id']);
	exit;
}

//链接
if($do=="link"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	$search = " WHERE 1=1 ";
	$search_type = (isset($_GET['search_type']) && $_GET['search_type']!="all") ? " OR " : " AND ";
	if(isset($_GET['username']) && $_GET['username']!=""){
		$search .= $search_type . " u.username LIKE '%".strip_tags($_GET['username'])."%'";
	}
	if (isset($_GET['create_datetime_start']) && $_GET['create_datetime_start']!=""){
		$search .= $search_type . " UNIX_TIMESTAMP(u.create_datetime) < " . strtotime($_GET['create_datetime_start']);
	}
	if (isset($_GET['create_datetime_end']) && $_GET['create_datetime_end']!=""){
		$search .= $search_type . " UNIX_TIMESTAMP(u.create_datetime) < " . strtotime($_GET['create_datetime_end']);
	}
	$search = $search!=""&&$search_type==" OR " ? " AND (" . ltrim($search, " OR") . ")" : $search;
	
	//设置分页
	if($_POST[numPerPage]==""){$numPerPage=$cfg['page_size'];}else{$numPerPage=$_POST[numPerPage];}
	if($_GET[pageNum]==""||$_GET[pageNum]=="0" ){$pageNum="0";}else{$pageNum=($_GET[pageNum]-1)*$numPerPage;}
	$num=mysql_query("SELECT a.* FROM $cfg[dbpre]share_list AS a LEFT JOIN $cfg[dbpre]user AS u ON a.user_id=u.id $search");//当前频道条数
	$total=mysql_num_rows($num);//总条数	
	$page=new page(array('total'=>$total,'perpage'=>$numPerPage));

	$sql="SELECT a.* FROM $cfg[dbpre]share_list AS a LEFT JOIN $cfg[dbpre]user AS u ON a.user_id=u.id $search ORDER BY id DESC LIMIT $pageNum,$numPerPage";
	$db->query($sql);
	$list=$db->fetchAll();
	
	foreach ($list as $key=>$value) {
		$list[$key]['face_img'] = explode(",",$list[$key]['face']);
	}

	$smt->assign('list',$list);
	$smt->assign('menus',getActions(1));
	$smt->assign('numPerPage',$_POST[numPerPage]); //显示条数
	$smt->assign('pageNum',$_GET[pageNum]); //当前页数
	$smt->assign('total',$total);  //总条数
	$smt->assign ('page',$page->show());
	$smt->assign('title',$lang['title']['links']);
	$smt->display('Html_2013.html');
	exit;
}

//删除链接记录
if($do=="dellink"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	$sql="DELETE FROM $cfg[dbpre]share_list WHERE id IN (" . $_GET['id'] . ")";
	if($db->query($sql)){
		echo success($lang['tips']['del_success'],"?action=member&do=link");
	} else {
		echo error($lang['tips']['del_fail']);
	}
	adminlog($lang['log']['del_link'] . $_GET['id']);
	exit;
}


//等级列表
if($do=="level"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 
	
	//用户组
	$sql="SELECT r.*, ro.level_name AS parent_name FROM $cfg[dbpre]level AS r LEFT JOIN $cfg[dbpre]level AS ro ON r.parent_id=ro.level_id ORDER BY level_grade ASC,parent_id ASC";
	$db->query($sql);
	$level=$db->fetchAll();
	
	$level_arr = array();
	foreach ($level as $key=>$value)
	{
		foreach ($level as $k=>$v)
		{
			if ($v['parent_id']==$value['level_id'] || in_array($v['parent_id'],$level_arr[$key])) {
				$level_arr[$key][$value['level_id']] = $v['level_id'];
			}
		}
	}
	//print_r($level_arr);

	$smt->assign('menus',getActions(1));
    
	//模版
	$smt->assign('level',$level);
	$smt->assign('cur_user',$cur_user);
	$smt->assign('title',$lang['title']['level_list']);
	$smt->display('Html_2021.html');
	exit;
}

//添加等级
if($do=="newlevel"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 
	
	//用户组
	$sql="SELECT * FROM $cfg[dbpre]level WHERE 1";
	$db->query($sql);
	$level=$db->fetchAll();

	$smt->assign('menus',getActions(1));
    
	//模版
	$smt->assign('level',$level);
	$smt->assign('actions',getActions());
	$smt->assign('cur_user',$cur_user);
	$smt->assign('title',$lang['title']['add_level']);
	$smt->display('Html_2021a.html');
	exit;
}

//写入等级
if($do=="add_level"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	//查询
	$sql="SELECT * FROM `$cfg[dbpre]level` where level_name ='$_POST[level_name]' LIMIT 1";
	$db->query($sql);
	if($db->fetchRow()){echo error($lang['tips']['level_exists']);exit();}
	
	//数据处理
	$action_code = $_POST['action_code'];
	array_filter($action_code);
	$insert_arr = array(
						'level_name' => trim($_POST['level_name']),
						'level_describe' => $_POST['level_describe'],
						'point' => intval($_POST['point']),
						'parent_id' => $_POST['parent_id']
					  );
	
	$sql="INSERT INTO `$cfg[dbpre]level` " . insert_sql($insert_arr);
	if($db->query($sql)){
		echo success($msg,"?action=member&do=level");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['add_level'] . $_POST['level_name']);
	exit;
}

//编辑等级
if($do=="editlevel"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 
	
	//查询
	$sql=" SELECT * FROM $cfg[dbpre]level where level_id='$_GET[id]' LIMIT 1";
	$db->query($sql);
	$row=$db->fetchRow();
	
	//用户组
	$sql=" SELECT * FROM $cfg[dbpre]level WHERE 1";
	$db->query($sql);
	$level=$db->fetchAll();
	
	$smt->assign('menus',getActions(1));
    
	//模版
	$smt->assign('level',$level);
	$smt->assign('actions',getActions());
	$smt->assign('row',$row);
	$smt->assign('title',$lang['title']['edit_level']);
	$smt->display('Html_2021a.html');
	exit;
}

//更新等级
if($do=="update_level"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);

	//查询
	$sql="SELECT * FROM `$cfg[dbpre]level` where level_name ='$_POST[level_name]' AND level_id<>'$_POST[level_id]' LIMIT 1";
	$db->query($sql);
	if($db->fetchRow()){echo error($lang['tips']['level_exists']);exit();}
	
	//数据处理
	$action_code = $_POST['action_code'];
	array_filter($action_code);
	$update_arr = array(
						'level_name' => trim($_POST['level_name']),
						'level_describe' => $_POST['level_describe'],
						'point' => intval($_POST['point']),
						'parent_id' => $_POST['parent_id']
					  );

	$sql="UPDATE $cfg[dbpre]level " . update_sql($update_arr) ." $addsql WHERE `level_id` ='$_POST[level_id]' LIMIT 1 ;";

	if($db->query($sql)){
		echo success($msg,"?action=member&do=level");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['update_level'] . $_POST['level_name']);
	exit;
}

//删除等级
if($do=="dellevel"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	$sql="delete from `$cfg[dbpre]level` where `level_id`=$_GET[id] AND is_default<>-1 limit 1";
	if($db->query($sql)){
		echo success($msg,"?action=member&do=level");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['del_level'] . $_GET['id']);
	exit;
}


?>