<?php
if(!defined('CORE'))exit("error!"); 

//验证登录
if($do=="loginok"){
	$name=isset($_POST['username']) ? trim($_POST['username']) : "";
	$pwd=isset($_POST['password']) && trim($_POST['password'])!="" ? md5($_POST['password']) : "";
	$vcode=strtolower($_POST['vcode']);
	
	//$validate_arr=array($name,$pwd,$vcode);
	//Ifvalidate($validate_arr);
	if ($name==""){
		die(error($lang['tips']['empty_username']));
	}
	if ($pwd==""){
		die(error($lang['tips']['empty_password']));
	}
	if ($vcode==""){
		die(error($lang['tips']['empty_vcode']));
	}
	
	if ($vcode!=strtolower($_SESSION['vcode'.$_POST['code_id']])){
		die(error($lang['tips']['wrong_vcode']));
	}

	$sql = "SELECT u.id,u.username,u.name,u.roleid,r.role_name,r.level_grade from $cfg[dbpre]user AS u LEFT JOIN $cfg[dbpre]role AS r ON u.roleid=r.role_id WHERE u.username = '$name' AND u.password = '$pwd' limit 1 ";
	//echo $sql;
	$db->query($sql);
	if ($record = $db->fetchRow()){	//登录成功
		
		//记录登录时间
		$sql = "UPDATE $cfg[dbpre]user SET last_login = '".time()."' WHERE `id`=" . $record['id'];
		$db->query($sql);
		
		//增加在线用户
		$sql="SELECT MAX(id) AS id FROM $cfg[dbpre]online_user LIMIT 1";
		$db->query($sql);
		$row=$db->fetchRow();
		$newid= $row['id'] ? $row['id']+1 : 1;
		$sql="INSERT INTO $cfg[dbpre]online_user (`id`,`user_id`,`username`,`name`,`last_login`) VALUES ('$newid','$record[id]','$record[username]','$record[name]','".time()."')";
		$db->query($sql);
	

		$_SESSION['isLogin'] 	= true;
		$_SESSION['userid']		= $record['id'];
		$_SESSION['username']	= $record['username'];
		$_SESSION['role_name']  = $record['role_name'];
		$_SESSION['roleid']	    = $record['roleid'];
		$_SESSION['level_grade']= $record['level_grade'];
		$_SESSION['time_out']   = $record['time_out'];
		$_SESSION['last_do_time'] = time();
		exit($lang_cn['rabc_login_ok']);
	}
	else
		exit($lang_cn['rabc_login_error']);
	exit;
}

//登录	
if($do=="login"){
	$smt->assign('menus',getActions(1));
	$smt->assign('web_name',$web_name);
	$smt->assign('title',$lang['title']['login']);
	$smt->display('Login.html');
	exit;
}

//退出	
if($do=="logout"){
	
	//先更新最新登录
	$sql = "UPDATE $cfg[dbpre]user SET last_login = '".time()."' WHERE `id`=" . $_SESSION['userid'];
	$db->query($sql);
	
	//删除在线用户表
	$sql = "DELETE FROM $cfg[dbpre]online_user WHERE `user_id`=" . $_SESSION['userid'];
	$db->query($sql);
	
	//清空session
	$_SESSION = array();
	session_destroy();
	exit($lang_cn['rabc_logout']);
}

//列表	
if($do==""){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 
	$search = " WHERE (r.level_grade>=".(isset($_SESSION['level_grade']) && $_SESSION['level_grade']!="" ? $_SESSION['level_grade'] : 0) . " OR u.qq_id<>'' OR u.qq_id<>0) ";
	$search_type = (isset($_GET['search_type']) && $_GET['search_type']!="all") ? " OR " : " AND ";
	if(isset($_GET['username']) && $_GET['username']!=""){
		$search .= $search_type . " u.username LIKE '%".strip_tags($_GET['username'])."%'";
	}
	if (isset($_GET['name']) && $_GET['name']!=""){
		$search .= $search_type . " u.name LIKE '%" . strip_tags($_GET['name']) . "%'";
	}
	if (isset($_GET['creat_datetime_start']) && $_GET['creat_datetime_start']!=""){
		$search .= $search_type . " UNIX_TIMESTAMP(u.creat_datetime) < " . strtotime($_GET['creat_datetime_start']);
	}
	if (isset($_GET['creat_datetime_end']) && $_GET['creat_datetime_end']!=""){
		$search .= $search_type . " UNIX_TIMESTAMP(u.creat_datetime) < " . strtotime($_GET['creat_datetime_end']);
	}
	$search = $search!=""&&$search_type==" OR " ? " AND (" . ltrim($search, " OR") . ")" : $search;
	
	//设置分页
	if($_POST[numPerPage]==""){$numPerPage=$cfg['page_size'];}else{$numPerPage=$_POST[numPerPage];}
	if($_GET[pageNum]==""||$_GET[pageNum]=="0" ){$pageNum="0";}else{$pageNum=($_GET[pageNum]-1)*$numPerPage;}
	$num=mysql_query("SELECT u.* FROM $cfg[dbpre]user AS u LEFT JOIN $cfg[dbpre]role AS r ON u.roleid=r.role_id LEFT JOIN $cfg[dbpre]user AS uc ON u.create_id=uc.id $search");//当前频道条数
	$total=mysql_num_rows($num);//总条数	
	$page=new page(array('total'=>$total,'perpage'=>$numPerPage));
	
	//权限条件
	//查询下级角色
	if ($_SESSION['userid']>1) {
		$sql="SELECT role_id,level_grade,parent_id FROM $cfg[dbpre]role WHERE 1=1 AND (level_grade>$_SESSION[level_grade] OR role_id=$_SESSION[roleid]) ORDER BY parent_id ASC, level_grade ASC";
		$db->query($sql);
		$role_list=$db->fetchAll();
		$role_arr = array();
		foreach ($role_list AS $key=>$value)
		{
			if ($value['parent_id']==$_SESSION['roleid'] || in_array($value['parent_id'],$role_arr)) {
				$role_arr[] = $value['role_id'];
			}
		}
		//print_r($role_arr);
	
		$role_ids = implode(",", $role_arr);
		$search .= $role_ids ? " AND r.role_id IN (" . $role_ids . ")" : "";
	}

	//查询
	$sql="SELECT u.*, uc.name AS create_name, r.role_name,r.role_name, l.level_name FROM $cfg[dbpre]user AS u LEFT JOIN $cfg[dbpre]role AS r ON u.roleid=r.role_id LEFT JOIN $cfg[dbpre]user AS uc ON u.create_id=uc.id LEFT JOIN $cfg[dbpre]level AS l ON u.roleid=l.level_id $search ORDER BY u.id DESC LIMIT $pageNum,$numPerPage";
	
	$db->query($sql);
	$list=$db->fetchAll();
	
	foreach ($list as $key=>$value) {
		if ($list[$key]['password']=='') {
			$list[$key]['role_name']='';
			$list[$key]['create_name']='';
		} else {
			$list[$key]['level_name']='';
		}
	}
	
	$smt->assign('menus',getActions(1));
	$smt->assign('list',$list);
	$smt->assign('numPerPage',$_POST[numPerPage]); //显示条数
	$smt->assign('pageNum',$_GET[pageNum]); //当前页数
	$smt->assign('total',$total);  //总条数
	$smt->assign ('page',$page->show());
	$smt->assign('title',$lang['title']['user_manage']);
	$smt->display('Html_1022.html');
	exit;
}

//新建	
if($do=="new"){	
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 
	
	$cur_user = array(
					  'username'=>$_SESSION['username'],
					  'department'=>$_SESSION['department'],
					  'creatdate'=>date("Y-m-d",time())
					);
	
	//用户组
	$sql=" SELECT * FROM $cfg[dbpre]role";
	$db->query($sql);
	$role=$db->fetchAll();
	
	$smt->assign('menus',getActions(1));
    
	//模版
	$smt->assign('role',$role);
	$smt->assign('cur_user',$cur_user);
	$smt->assign('do',$do);
	$smt->assign('title',$lang['title']['new_user']);
	$smt->display('Html_1022a.html');
	exit;
}

//写入用户
if($do=="add"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);

	$creat_datetime=date("Y-m-d H:i:s", time());
	$password=md5($_POST['password']);

	//查询
	$sql="SELECT * FROM `$cfg[dbpre]user` where username ='$_POST[username]' LIMIT 1";
	$db->query($sql);
	if($db->fetchRow()){echo  error($lang['tips']['user_exists']);exit();}
	
	//数据处理
	$insert_arr = array(
						'name' => $_POST['name'],
						'username' => $_POST['username'],
						'password' => $password,
						'gender' => $_POST['gender'],
						'phone' => $_POST['phone'],
						'roleid' => $_POST['roleid'],
						'create_datetime' => $creat_datetime,
						'update_datetime' => $creat_datetime
					  );
	
	$sql="INSERT INTO `$cfg[dbpre]user` " . insert_sql($insert_arr);
	if($db->query($sql)){
		echo success($msg,"?action=user");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['add_user'] . $_POST['username']);
	exit;
}

//编辑	
if($do=="edit"){	
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 
	
	$smt->assign('menus',getActions(1));
    
	//查询
	$sql="SELECT u.*, r.* FROM $cfg[dbpre]user AS u LEFT JOIN $cfg[dbpre]role AS r ON u.roleid=r.role_id where u.id='{$_GET['id']}' LIMIT 1";
	
	$db->query($sql);
	$row=$db->fetchRow();
	$json = new Services_JSON;
	$rowjson = $json->encode($row);
	
	//用户组
	$sql="SELECT * FROM $cfg[dbpre]role";
	$db->query($sql);
	$role=$db->fetchAll();
	
	//会员等级
	$sql="SELECT * FROM $cfg[dbpre]level";
	$db->query($sql);
	$level=$db->fetchAll();
	
	$cur_user = array(
					  'username'=>$_SESSION['username'],
					  'department'=>$_SESSION['department'],
					  'level_grade'=>$_SESSION['level_grade'],
					  'creatdate'=>date("Y-m-d",time())
					);
					
	//模版
	$smt->assign('row',$row);
	$smt->assign('role',$role);
	$smt->assign('level',$level);
	$smt->assign('cur_user',$cur_user);
	$smt->assign('rowjson',$rowjson);
	$smt->assign('title',$lang['title']['edit_user']);
	$smt->display('Html_1022b.html');
	exit;
}

//更新
if($do=="update"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	if(!$_POST[id]){echo error($msg);exit;}
	
	if (isset($_POST['password']) && isset($_POST['repassword']) && trim($_POST['password'])!="" && trim($_POST['repassword'])!="") {
		if (trim($_POST['password'])!=trim($_POST['repassword'])) {
			echo error($lang['tips']['wrong_repassword']);
			exit;
		} else {
			$addsql = ", `password` = '". md5(trim($_POST['password'])) ."'";
		}
	} 
	
	$update_datetime = time();
	
	//数据处理
	$update_arr = array(
					'update_datetime' => $update_datetime
				  );
	if (isset($_POST['name']) && $_POST['name']!='') {
		$update_arr['name'] = $_POST['name'];
	}
	if (isset($_POST['username']) && $_POST['username']!='') {
		$update_arr['username'] = $_POST['username'];
	}
	if (isset($_POST['money']) && $_POST['money']!='') {
		$update_arr['money'] = $_POST['money'];
	}
	if (isset($_POST['roleid']) && $_POST['roleid']!='') {
		$update_arr['roleid'] = $_POST['roleid'];
	}
	if (isset($_POST['level']) && $_POST['level']!='') {
		$update_arr['roleid'] = $_POST['level'];
	}
	if (isset($_POST['expire']) && $_POST['expire']!='') {
		$update_arr['expire'] = strtotime($_POST['expire']);
	}
	if (isset($_POST['parent_id']) && $_POST['parent_id']!='') {
		$update_arr['parent_id'] = $_POST['parent_id'];
	}
		  
	$sql="UPDATE $cfg[dbpre]user " . update_sql($update_arr) ." $addsql WHERE `id` ='$_POST[id]' LIMIT 1 ;";
	
	if($db->query($sql)){
		echo success($msg,"?action=user");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['update_user'] . $_POST['id']);
	exit;
}

//删除用户
if($do=="canceloa"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	if (intval($_GET['id'])==1) {
		echo error($lang['tips']['cannot_del_admin']);
		exit;
	}
	$sql="delete from `$cfg[dbpre]user` where `id`=$_GET[id] limit 1";
	if($db->query($sql)){
		echo success($msg,"?action=user");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['cancel_oa'] . $_GET['id']);
	exit;
}

//角色列表
if($do=="oaaction"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 
	
	//用户组
	$sql=" SELECT r.*, ro.role_name AS parent_name FROM $cfg[dbpre]role AS r LEFT JOIN $cfg[dbpre]role AS ro ON r.parent_id=ro.role_id ORDER BY level_grade ASC,parent_id ASC";
	$db->query($sql);
	$role=$db->fetchAll();
	
	$role_arr = array();
	foreach ($role as $key=>$value)
	{
		foreach ($role as $k=>$v)
		{
			if ($v['parent_id']==$value['role_id'] || in_array($v['parent_id'],$role_arr[$key])) {
				$role_arr[$key][$value['role_id']] = $v['role_id'];
			}
		}
	}
	//print_r($role_arr);

	$smt->assign('menus',getActions(1));
    
	//模版
	$smt->assign('role',$role);
	$smt->assign('cur_user',$cur_user);
	$smt->assign('title',$lang['title']['role_list']);
	$smt->display('Html_1031.html');
	exit;
}

//添加角色
if($do=="newrole"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 
	
	//用户组
	$sql=" SELECT * FROM $cfg[dbpre]role WHERE 1";
	$db->query($sql);
	$role=$db->fetchAll();

	$smt->assign('menus',getActions(1));
    
	//模版
	$smt->assign('role',$role);
	$smt->assign('actions',getActions());
	$smt->assign('cur_user',$cur_user);
	$smt->assign('title',$lang['title']['add_role']);
	$smt->display('Html_1031a.html');
	exit;
}

//写入角色
if($do=="add_role"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	if(intval($_POST['level_grade'])>=$_SESSION['level_grade']&&$_SESSION['userid']!=1){echo error($lang['tips']['only_lower_role']);exit();}

	//查询
	$sql="SELECT * FROM `$cfg[dbpre]role` where role_name ='$_POST[role_name]' LIMIT 1";
	$db->query($sql);
	if($db->fetchRow()){echo error($lang['tips']['role_exists']);exit();}
	
	//数据处理
	$action_code = $_POST['action_code'];
	array_filter($action_code);
	$insert_arr = array(
						'role_name' => trim($_POST['role_name']),
						'role_describe' => $_POST['role_describe'],
						'action_list' => implode(",",$action_code),
						'parent_id' => $_POST['parent_id'],
						'level_grade' => $_POST['level_grade']
					  );
	
	$sql="INSERT INTO `$cfg[dbpre]role` " . insert_sql($insert_arr);
	if($db->query($sql)){
		echo success($msg,"?action=user&do=oaaction");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['add_role'] . $_POST['role_name']);
	exit;
}

//编辑角色
if($do=="editrole"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 
	
	//查询
	$sql=" SELECT * FROM $cfg[dbpre]role where role_id='$_GET[id]' LIMIT 1";
	$db->query($sql);
	$row=$db->fetchRow();
	
	//用户组
	$sql=" SELECT * FROM $cfg[dbpre]role WHERE 1";
	$db->query($sql);
	$role=$db->fetchAll();
	
	$smt->assign('menus',getActions(1));
    
	//模版
	$smt->assign('role',$role);
	$smt->assign('actions',getActions());
	$smt->assign('row',$row);
	$smt->assign('title',$lang['title']['edit_role']);
	$smt->display('Html_1031a.html');
	exit;
}

//更新角色
if($do=="update_role"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);

	//查询
	$sql="SELECT * FROM `$cfg[dbpre]role` where role_name ='$_POST[role_name]' AND role_id<>'$_POST[role_id]' LIMIT 1";
	$db->query($sql);
	if($db->fetchRow()){echo error($lang['tips']['role_exists']);exit();}
	
	//数据处理
	$action_code = $_POST['action_code'];
	array_filter($action_code);
	$update_arr = array(
						'role_name' => trim($_POST['role_name']),
						'role_describe' => $_POST['role_describe'],
						'level_grade' => $_POST['level_grade'],
						'parent_id' => $_POST['parent_id'],
						'action_list' => implode(",",$action_code)
					  );

	$sql="UPDATE $cfg[dbpre]role " . update_sql($update_arr) ." $addsql WHERE `role_id` ='$_POST[role_id]' LIMIT 1 ;";

	if($db->query($sql)){
		echo success($msg,"?action=user&do=oaaction");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['update_role'] . $_POST['role_name']);
	exit;
}

//删除角色
if($do=="delrole"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	$sql="delete from `$cfg[dbpre]role` where `role_id`=$_GET[id] limit 1";
	if($db->query($sql)){
		echo success($msg,"?action=user&do=oaaction");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['del_role'] . $_GET['id']);
	exit;
}

//单个用户权限
if($do=="action"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do); 
	
	//查询
	$sql=" SELECT * FROM $cfg[dbpre]user where id='$_GET[id]' LIMIT 1";
	$db->query($sql);
	$row=$db->fetchRow();

	$json = new Services_JSON;
	$rowjson = $json->encode($row);
	
	$smt->assign('menus',getActions(1));
    
	//模版
	$smt->assign('actions',getActions());
	$smt->assign('rowjson',$rowjson);
	$smt->assign('row',$row);
	$smt->assign('title',$lang['title']['edit_user']);
	$smt->display('Html_1031b.html');
	exit;
}

//更新用户权限
if($do=="save_action"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	$action_code = $_POST['action_code'];
	array_filter($action_code);
	
	//更新时间
	$update_datetime = date("Y-m-d H:i:s", time());
	
	//数据处理
	$update_arr = array(
						'actions' => implode(",", $action_code),
						'update_datetime' => $update_datetime
					  );	

	$sql="UPDATE $cfg[dbpre]user " . update_sql($update_arr) ." WHERE `id` ='$_POST[id]' LIMIT 1 ;";

	if($db->query($sql)){
		echo success($msg,"?action=user");
	} else {
		echo error($msg);
	}
	adminlog($lang['log']['save_action'] . $_POST['id']);
	exit;
}

if($do=="deluser"){
	If_rabc($action,$do); //检测权限
	is_admin($action,$do);
	
	//查询
	$sql="DELETE FROM $cfg[dbpre]user WHERE id IN (" . $_GET['ids'] . ") AND id<>1";
	
	if($db->query($sql)){
		echo success($msg,"?action=user");
	} else {
		echo error($msg);
	}	
	adminlog($lang['log']['del_user'] . $_GET['ids']);
	exit;
}

?>