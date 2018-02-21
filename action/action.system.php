<?php
if(!defined('CORE'))exit("error!"); 

if($do==""){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 

	//查询
	$sql="SELECT * FROM $cfg[dbpre]setting where 1=1";
	
	$db->query($sql);
	$row=$db->fetchRow();
	
	$row['count'] = htmlspecialchars($row['count']);
	$row['copyright'] = htmlspecialchars($row['copyright']);
	
	$smt->assign('menus',getActions(1));
	$smt->assign('row',$row);
	$smt->assign('title',$lang['title']['system']);
	$smt->display('Html_setting.html');
	exit;
}

if($do=="menu"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 

	$smt->assign('menus',getActions(1));
	$smt->assign('menus_list',getActions(0));//print_r(getActions(0));
	$smt->assign('row',$row);
	$smt->assign('title',$lang['title']['menu']);
	$smt->display('Html_menu.html');
	exit;
}

if($do=="menuadd"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 

	//数据处理
	$insert_arr = array(
						'name' => trim($_POST['name']),
						'action_code' => $_POST['action_code'],
						'parent_id' => intval(trim($_POST['parent_id'])),
						'level' => intval(trim($_POST['level'])),
						'class' => intval(trim($_POST['class'])),
						'orders' => intval(trim($_POST['orders']))
					  );

	$sql="INSERT INTO `$cfg[dbpre]admin_action` " . insert_sql($insert_arr);
	if($db->query($sql)){
		echo success($msg,"?action=system&do=menu");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['menuadd']);
	exit;
}

if($do=="menudelete"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 

	$sql="DELETE FROM `$cfg[dbpre]admin_action` WHERE action_id=".intval($_GET['id']);
	if($db->query($sql)){
		echo success($msg,"?action=system&do=menu");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['menudelete'] . $_GET['id']);
	exit;
}

if($do=="savemenu"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 

	//数据处理
	$err = 0;
	foreach ($_POST['name'] as $key=>$value) {
		$update_arr = array(
							'name' => trim($_POST['name'][$key]),
							'action_code' => trim($_POST['action_code'][$key]),
							'parent_id' => intval(trim($_POST['parent_id'][$key])),
							//'level' => intval(trim($_POST['level'])),
							'class' => intval(trim($_POST['class'][$key])),
							'orders' => intval(trim($_POST['orders'][$key]))
						  );
		$sql = "UPDATE $cfg[dbpre]admin_action " . update_sql($update_arr) ." WHERE `action_id` = '$key' ;";
		if($db->query($sql)){
			
		} else {
			$err++;
		}
	}
	//
	if ($err == 0) {
		echo success($msg,"?action=system&do=menu");
	} else {
		echo error($lang['tips']['edit_fail']);
	}
	
	adminlog($lang['log']['savemenu']);
	exit;
}

if($do=="save_setting"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 

	//数据处理
	$update_arr = array(
						'lang' => $_POST['lang'],
						'website' => trim($_POST['website']),
						'webtitle' => $_POST['webtitle'],
						'allow_file_type' => trim($_POST['allow_file_type']),
						'allow_file_size' => trim($_POST['allow_file_size']),
						'qq' => trim($_POST['qq']),
						'email' => trim($_POST['email']),
						'copyright' => htmlspecialchars_decode($_POST['copyright']),
						'count' => htmlspecialchars_decode($_POST['count']),
						'keywords' => $_POST['keywords'],
						'description' => $_POST['description'],
						'notice' => $_POST['notice'],
						'page_size' => intval(trim($_POST['page_size'])),
						'index_size' => intval(trim($_POST['index_size'])),
						'coin_name' => trim($_POST['coin_name']),
						'coin_rate' => trim($_POST['coin_rate']),
						'link_prize' => intval(trim($_POST['link_prize'])),
						'reg_prize' => intval(trim($_POST['reg_prize'])),
						'login_prize' => intval(trim($_POST['login_prize'])),
						'fee' => intval(trim($_POST['fee'])),
						'wait' => intval(trim($_POST['wait'])),
						'max_jump' => intval(trim($_POST['max_jump'])),
						'max_links' => intval(trim($_POST['max_links'])),
						'share_time' => trim($_POST['share_time']),
						'free_try_time' => intval(trim($_POST['free_try_time'])),
						'email_host' => trim($_POST['email_host']),
						'email_port' => trim($_POST['email_port']),
						'email_true_email' => trim($_POST['email_true_email']),
						'email_username' => trim($_POST['email_username']),
						'email_password' => trim($_POST['email_password']),
						'wechat_name' => trim($_POST['wechat_name']),
						'wechat_id' => trim($_POST['wechat_id']),
						'wechat_no' => trim($_POST['wechat_no']),
						'wechat_appid' => trim($_POST['wechat_appid']),
						'wechat_appsecret' => trim($_POST['wechat_appsecret']),
						'qqhl_appid' => trim($_POST['qqhl_appid']),
						'qqhl_appkey' => trim($_POST['qqhl_appkey']),
						'alipay_account' => trim($_POST['alipay_account']),
						'alipay_pid' => trim($_POST['alipay_pid']),
						'alipay_key' => trim($_POST['alipay_key'])
					  );

	$sql = "UPDATE $cfg[dbpre]setting " . update_sql($update_arr) ." WHERE `id` = 1 ;";
	
	if($db->query($sql)){
		echo success($msg,"?action=system");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['save_setting']);
	exit;
}

if($do=="log"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	//搜索条件
	$search = " WHERE 1=1 ";
	$search_type = (isset($_GET['search_type']) && $_GET['search_type']!="all") ? " OR " : " AND ";

	if (isset($_GET['create_datetime_start']) && $_GET['create_datetime_start']!=""){
		$search .= $search_type . " UNIX_TIMESTAMP(l.log_time) > " . strtotime($_GET['create_datetime_start']);
	}
	if (isset($_GET['create_datetime_end']) && $_GET['create_datetime_end']!=""){
		$search .= $search_type . " UNIX_TIMESTAMP(l.log_time) < " . strtotime($_GET['create_datetime_end']);
	}
	if (isset($_GET['username']) && $_GET['username']!=""){
		$search .= $search_type . " u.username = '" . $_GET['username'] . "'";
	}
	$search = $search!=""&&$search_type==" OR " ? " AND (" . ltrim($search, " OR") . ")" : $search;

	//设置分页
	if($_POST[numPerPage]==""){$numPerPage=$cfg['page_size'];}else{$numPerPage=$_POST[numPerPage];}
	if($_GET[pageNum]==""||$_GET[pageNum]=="0" ){$pageNum="0";}else{$pageNum=($_GET[pageNum]-1)*$numPerPage;}
	$num=mysql_query("SELECT * FROM $cfg[dbpre]admin_log AS l LEFT JOIN $cfg[dbpre]user AS u ON l.user_id=u.id $search");//当前频道条数
	$total=mysql_num_rows($num);//总条数	
	$page=new page(array('total'=>$total,'perpage'=>$numPerPage));

	//查询
	$sql="SELECT l.*,u.username,u.name FROM $cfg[dbpre]admin_log AS l LEFT JOIN $cfg[dbpre]user AS u ON l.user_id=u.id $search ORDER BY l.id DESC LIMIT $pageNum,$numPerPage";
	
	$db->query($sql);
	$list=$db->fetchAll();	
	
	
	$smt->assign('menus',getActions(1));
	$smt->assign('list',$list);
	$smt->assign('numPerPage',$_POST[numPerPage]); //显示条数
	$smt->assign('pageNum',$_GET[pageNum]); //当前页数
	$smt->assign('total',$total);  //总条数
	$smt->assign ('page',$page->show());
	$smt->assign('title',$lang['title']['log']);
	$smt->display('Html_1091.html');
	exit;
}

if($do=="dellog"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	//查询
	$sql="DELETE FROM $cfg[dbpre]admin_log WHERE id IN (" . $_GET['ids'] . ")";
	
	if($db->query($sql)){
		echo success($msg,"?action=system&do=log");
	} else {
		echo error($msg);
	}	
	adminlog($lang['log']['dellog'] . $_GET['ids']);
	exit;
}

if($do=="clearcache"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	if(delDirAndFile('tmp/compile/',0)){
		echo success($msg,"?action=system");
	} else {
		echo error($msg);
	}	
	adminlog($lang['log']['clearcache']);
	exit;
}

?>