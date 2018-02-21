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
	/*if ($vcode==""){
		die(error("请填写验证码！"));
	}
	
	if ($vcode!=strtolower($_SESSION['vcode'.$_POST['code_id']])){
		die(error("验证码不正确！"));
	}*/
	
	$sql = "SELECT u.id,u.username,u.name,u.roleid,u.money,u.expire,u.last_login,r.level_name from $cfg[dbpre]user AS u LEFT JOIN $cfg[dbpre]level AS r ON u.roleid=r.level_id WHERE u.username = '$name' AND u.password = '$pwd' limit 1 ";
	//echo $sql;
	$db->query($sql);
	
	//登录成功
	if ($record = $db->fetchRow()){
		
		$time = time();
		
		$add_sql = "";

		//判断VIP期限
		if ($record['expire']<$time) {
			$add_sql .= ", `roleid`=1 ";
			$record['roleid'] = 1;
		}
		
		//赠送金币
		if (date("Y-m-d",$record['last_login'])!=date("Y-m-d",$time)) {
			$add_sql .= ", `money`=`money`+".$cfg['login_prize'].", `status`=0";
			$sql="INSERT INTO $cfg[dbpre]account_log (`user_id`,`pay_type`,`amount`,`left_money`,`mark`,`pay_datetime`) VALUES ('$record[id]','1','$cfg[login_prize]','$cfg[login_prize]','".$lang['tips']['login_prize']."','".$time."')";
			$db->query($sql);
		}
		
		$sql = "UPDATE $cfg[dbpre]user SET last_login = '".$time."' $add_sql WHERE `id`=" . $record['id'];
		$db->query($sql);
		
		//增加在线用户
		$sql="SELECT MAX(id) AS id FROM $cfg[dbpre]online_user LIMIT 1";
		$db->query($sql);
		$row=$db->fetchRow();
		$newid= $row['id'] ? $row['id']+1 : 1;
		$sql="INSERT INTO $cfg[dbpre]online_user (`id`,`user_id`,`username`,`name`,`last_login`) VALUES ('$newid','$record[id]','$record[username]','$record[name]','".$time."')";
		$db->query($sql);
	

		$_SESSION['isLogin'] 	= true;
		$_SESSION['userid']		= $record['id'];
		$_SESSION['username']	= $record['username'];
		$_SESSION['level_name'] = $record['level_name'];
		$_SESSION['levelid']	= $record['roleid'];
		$_SESSION['money']      = $record['money'];
		$_SESSION['expire']     = $record['expire'];
		exit($lang_cn_member['rabc_login_ok']);
	}
	else
		exit($lang_cn_member['rabc_login_error']);
	exit;
}

//登录	
if($do=="login"){
	require_once("API/qqConnectAPI.php");
	$qc = new QC();
	$qc->qq_login();
	
	/*$smt->assign('web_name',$web_name);
	$smt->assign('title',"登录");
	$smt->display($tpl_pre.'member_login.html');
	exit;*/
}

//手动登录	
if($do=="mlogin"){
	$smt->assign('web_name',$web_name);
	$smt->assign('title',$lang['title']['login']);
	$smt->display($tpl_pre.'member_login.html');
	exit;
}

//QQ登录成功
if($do=="qqloginok"){
	$qq_info = $_SESSION['qq_info'];
	$openid = $qq_info['openid'];
	if ($openid) {
		$time = time();
	
		$sql = "SELECT u.id,u.username,u.name,u.roleid,u.money,u.expire,u.last_login,r.level_name,r.level_grade from $cfg[dbpre]user AS u LEFT JOIN $cfg[dbpre]level AS r ON u.roleid=r.level_id WHERE u.qq_id = '$openid' limit 1 ";
		//echo $sql;
		$db->query($sql);
		
		//登录成功
		if ($record = $db->fetchRow()){
	
			//判断VIP期限
			if ($record['expire']<$time) {
				$add_sql = ", `roleid`=1 ";
				$record['roleid'] = 1;
			}
			//赠送金币
			if (date("Y-m-d",$record['last_login'])!=date("Y-m-d",$time)) {
				$add_sql .= ", `money`=`money`+".$cfg['login_prize'].", `status`=0";
				$sql="INSERT INTO $cfg[dbpre]account_log (`user_id`,`pay_type`,`amount`,`left_money`,`mark`,`pay_datetime`) VALUES ('$record[id]','1','$cfg[login_prize]','$cfg[login_prize]','".$lang['tips']['login_prize']."','".$time."')";
				$db->query($sql);
			}
			
			//记录登录时间
			$sql = "UPDATE $cfg[dbpre]user SET last_login = '".$time."' $add_sql WHERE `id`=" . $record['id'];
			$db->query($sql);
			
			//增加在线用户
			$sql="SELECT MAX(id) AS id FROM $cfg[dbpre]online_user LIMIT 1";
			$db->query($sql);
			$row=$db->fetchRow();
			$newid= $row['id'] ? $row['id']+1 : 1;
			$sql="INSERT INTO $cfg[dbpre]online_user (`id`,`user_id`,`username`,`name`,`last_login`) VALUES ('$newid','$record[id]','$record[username]','$record[name]','".$time."')";
			$db->query($sql);
		
			$_SESSION['isLogin'] 	= true;
			$_SESSION['userid']		= $record['id'];
			$_SESSION['username']	= $record['username'];
			$_SESSION['level_name'] = $record['level_name'];
			$_SESSION['levelid']	= $record['roleid'];
			$_SESSION['money']      = $record['money'];
			$_SESSION['expire']     = $record['expire'];
			exit($lang_cn_member['rabc_login_ok']);
		} else {
			$sql = "INSERT INTO $cfg[dbpre]user (qq_id,name,gender,qq,money,last_login,roleid,create_datetime,status) VALUES('$openid','$qq_info[nickname]','$qq_info[gender]','$qq','$cfg[reg_prize]','$time','1','$time','1') ";
			//echo $sql;
			$db->query($sql);
			
			$sql = "SELECT u.id,u.username,u.name,u.roleid,u.money,r.level_name,r.level_grade from $cfg[dbpre]user AS u LEFT JOIN $cfg[dbpre]level AS r ON u.roleid=r.level_id WHERE u.qq_id = '$openid' limit 1 ";
			//echo $sql;
			$db->query($sql);
			if ($record = $db->fetchRow()){	//登录成功
				//记录登录时间，生成username，6位数，100000+id
				$username = 100000+$record['id'];
				$sql = "UPDATE $cfg[dbpre]user SET last_login = '".$time."', username='$username' WHERE `id`=" . $record['id'];
				$db->query($sql);
				
				//增加在线用户
				$sql="SELECT MAX(id) AS id FROM $cfg[dbpre]online_user LIMIT 1";
				$db->query($sql);
				$row=$db->fetchRow();
				$newid= $row['id'] ? $row['id']+1 : 1;
				
				$sql="INSERT INTO $cfg[dbpre]online_user (`id`,`user_id`,`username`,`name`,`last_login`) VALUES ('$newid','$record[id]','$record[username]','$record[name]','".$time."')";
				$db->query($sql);
				
				//增加送币记录
				$sql="INSERT INTO $cfg[dbpre]account_log (`user_id`,`pay_type`,`amount`,`left_money`,`mark`,`pay_datetime`) VALUES ('$record[id]','0','$cfg[reg_prize]','$cfg[reg_prize]','".$lang['tips']['reg_prize']."','".$time."')";
				$db->query($sql);
		
				$_SESSION['isLogin'] 	= true;
				$_SESSION['userid']		= $record['id'];
				$_SESSION['username']	= $username;
				$_SESSION['level_name'] = $record['level_name'];
				$_SESSION['levelid']	= $record['roleid'];
				$_SESSION['level_grade']= $record['level_grade'];
				$_SESSION['money']      = $record['money'];
				$_SESSION['last_do_time'] = $time;
				exit($lang_cn_member['rabc_login_ok']);
			}
		}
		$_SESSION['qq_info'] = '';
	} else {
		echo $lang['tips']['login_fail'];
		exit();
	}
	exit;
}

//退出	
if($do=="logout"){
	if (isset($_SESSION['userid'])) {
		//先更新最新登录
		$sql = "UPDATE $cfg[dbpre]user SET `last_login` = '".time()."', `logout_time`='".time()."' WHERE `id`=" . $_SESSION['userid'];
		$db->query($sql);
		
		//删除在线用户表
		$sql = "DELETE FROM $cfg[dbpre]online_user WHERE `user_id`=" . $_SESSION['userid'];
		$db->query($sql);
	}
	//清空session
	$_SESSION = array();
	session_destroy();
	exit($lang_cn_member['rabc_logout']);
}

//默认	
if($do==""){
	If_rabc($action,$do); //检测权限

	//查询
	$sql="SELECT u.* FROM $cfg[dbpre]user AS u LEFT JOIN $cfg[dbpre]level AS r ON u.roleid=r.level_id WHERE u.id='$_SESSION[userid]' LIMIT 1";
	$db->query($sql);
	$row=$db->fetchRow();

	$smt->assign('row',$row);
	$smt->assign('title',$lang['title']['user_home']);
	$smt->display($tpl_pre.'member_index.html');
	exit;
}

//在线充值
if($do=="charge"){
	check_login();        //检查登录
	If_rabc($action,$do); //检测权限
	
	$coin_qty = array($cfg['coin_rate']*10,$cfg['coin_rate']*30,$cfg['coin_rate']*60,);

	$smt->assign('title',$lang['title']['charge_online']);
	$smt->assign('coin_qty',$coin_qty);
	$smt->display($tpl_pre.'member_charge.html');
	exit;
}

//充值记录
if($do=="paylog"){
	check_login();        //检查登录
	If_rabc($action,$do); //检测权限
	
	//设置分页
	if($_POST[numPerPage]==""){$numPerPage=$cfg['page_size'];}else{$numPerPage=$_POST[numPerPage];}
	if($_GET[pageNum]==""||$_GET[pageNum]=="0" ){$pageNum="0";}else{$pageNum=($_GET[pageNum]-1)*$numPerPage;}
	$num=mysql_query("SELECT p.* FROM $cfg[dbpre]pay_log AS p LEFT JOIN $cfg[dbpre]user AS u ON p.user_id=u.id WHERE p.user_id='$_SESSION[userid]' AND p.status=1");//当前频道条数
	$total=mysql_num_rows($num);//总条数	
	$page=new page(array('total'=>$total,'perpage'=>$numPerPage));

	$sql="SELECT p.* FROM $cfg[dbpre]pay_log AS p LEFT JOIN $cfg[dbpre]user AS u ON p.user_id=u.id WHERE p.user_id='$_SESSION[userid]' AND p.status=1 LIMIT $pageNum,$numPerPage";
	$db->query($sql);
	$list=$db->fetchAll();

	$smt->assign('list',$list);
	$smt->assign('numPerPage',$_POST[numPerPage]); //显示条数
	$smt->assign('pageNum',$_GET[pageNum]); //当前页数
	$smt->assign('total',$total);  //总条数
	$smt->assign ('page',$page->show());
	$smt->assign('title',$lang['title']['charge_log']);
	$smt->display($tpl_pre.'member_pay_log.html');
	exit;
}

//Z币记录
if($do=="accountlog"){
	check_login();        //检查登录
	If_rabc($action,$do); //检测权限
	
	//设置分页
	if($_POST[numPerPage]==""){$numPerPage=$cfg['page_size'];}else{$numPerPage=$_POST[numPerPage];}
	if($_GET[pageNum]==""||$_GET[pageNum]=="0" ){$pageNum="0";}else{$pageNum=($_GET[pageNum]-1)*$numPerPage;}
	$num=mysql_query("SELECT a.* FROM $cfg[dbpre]account_log AS a LEFT JOIN $cfg[dbpre]user AS u ON a.user_id=u.id WHERE a.user_id='$_SESSION[userid]'");//当前频道条数
	$total=mysql_num_rows($num);//总条数	
	$page=new page(array('total'=>$total,'perpage'=>$numPerPage));

	$sql="SELECT a.* FROM $cfg[dbpre]account_log AS a LEFT JOIN $cfg[dbpre]user AS u ON a.user_id=u.id WHERE a.user_id='$_SESSION[userid]' LIMIT $pageNum,$numPerPage";
	$db->query($sql);
	$list=$db->fetchAll();

	$smt->assign('list',$list);
	$smt->assign('numPerPage',$_POST[numPerPage]); //显示条数
	$smt->assign('pageNum',$_GET[pageNum]); //当前页数
	$smt->assign('total',$total);  //总条数
	$smt->assign ('page',$page->show());
	$smt->assign('title',$lang['title']['charge_log']);
	$smt->assign('title',sprintf($lang['title']['coin_log'],$cfg['coin_name']));
	$smt->display($tpl_pre.'member_account_log.html');
	exit;
}

//客服
if($do=="service"){
	If_rabc($action,$do); //检测权限

	$sql="SELECT u.* FROM $cfg[dbpre]user AS u LEFT JOIN $cfg[dbpre]level AS r ON u.roleid=r.level_id WHERE u.id='$_SESSION[userid]' LIMIT 1";
	$db->query($sql);
	$row=$db->fetchRow();

	$smt->assign('row',$row);
	$smt->assign('title',$lang['title']['service']);
	$smt->display($tpl_pre.'member_service.html');
	exit;
}

//升级
if($do=="upgrade"){
	If_rabc($action,$do); //检测权限
	$json = new Services_JSON;
	$result = array(
				'status' => '',
				'msg' => '',
				'money' => '',
	);

	//检查登录
	if (!isset($_SESSION['userid'])) {
		$result['status'] = '202';
		$result['msg'] = $lang['tips']['please_login'];
		$rejson = $json->encode($result);
		echo $rejson;
		exit;
	}

	$sql="SELECT money FROM $cfg[dbpre]user WHERE id='$_SESSION[userid]' LIMIT 1";
	$db->query($sql);
	$row=$db->fetchRow();
	
	if ($row['money']<$_POST['amount']) {
		$result['status'] = '201';
		$result['msg'] = $lang['tips']['lack_balance1'];
		$rejson = $json->encode($result);
		echo $rejson;
		exit;
	} else {
		//查询该金币能升级的级别
		$sql="SELECT level_id,level_name FROM $cfg[dbpre]level WHERE point<='".$_POST['amount']*$cfg['coin_rate']."' ORDER BY point DESC LIMIT 1";
		$db->query($sql);
		$rowa=$db->fetchRow();
		
		$expire = ($user_info['expire_or']>0 && $user_info['expire_or']>time()) ? $user_info['expire_or']+8640*intval($_POST['amount']) : time()+8640*intval($_POST['amount']);
		
		$sql="UPDATE $cfg[dbpre]user set `expire`='".$expire."', `money`=money-'".intval($_POST['amount'])."', `roleid`='$rowa[level_id]' WHERE id='$_SESSION[userid]' LIMIT 1";
		if ($db->query($sql)) {
			//扣费记录
			$sql1 = "INSERT INTO `$cfg[dbpre]account_log` (`user_id`,`pay_type`,`amount`,`left_money`,`mark`,`pay_datetime`) VALUES ('$_SESSION[userid]','2','-".intval($_POST['amount'])*$cfg['coin_rate']."','".($row['money']-intval($_POST['amount']))."','".$lang['tips']['up_vip']."','".time()."')";
			$db->query($sql1);
			
			$result['status'] = '200';
			$result['msg'] = $lang['tips']['up_vip_success'];
			$result['money'] = '-'.intval($_POST['amount']);
			$rejson = $json->encode($result);
			echo $rejson;
			exit;
		}
	}
}

//AJAX查询登录送金币
if($do=="ajaxloginprize"){
	If_rabc($action,$do); //检测权限

	$json = new Services_JSON;
	$result = array('msg' => '');
	
	$sql="SELECT status FROM $cfg[dbpre]user WHERE id='$_SESSION[userid]' LIMIT 1";
	$db->query($sql);
	$row=$db->fetchRow();
	
	if ($row['status']==0) {
		$sql="UPDATE $cfg[dbpre]user SET `status`=1 WHERE id='$_SESSION[userid]' LIMIT 1";
		if ($db->query($sql)) {
			$result['msg'] = 'OK';
			$rejson = $json->encode($result);
			echo $rejson;
			exit;
		}
	}
}
?>