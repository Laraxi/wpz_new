<?php
if(!defined('CORE'))exit("error!"); 

//首页	
if($do==""){
	//if(!isLogin()){exit($lang_cn_member['rabc_is_login']);} //判断是否登录
	
	//读取分享链接列表
	//条件
	$where = " WHERE 1=1 ";
	
	//计算最近两天数量，如果不足90条，则放宽时间
	$numa = mysql_query("SELECT * FROM $cfg[dbpre]share_list WHERE create_time>".(time()-172800));
	$totala = mysql_num_rows($numa);
	if ($totala>90) {
		$where .= " AND create_time>".(time()-172800);
		$numPerPage=200;
	}else{
		$numPerPage=90;
	}
	//$numPerPage = $cfg['index_size'] ? $cfg['index_size'] : $numPerPage;
	
	if($_GET[pageNum]==""||$_GET[pageNum]=="0" ){$pageNum="0";}else{$pageNum=($_GET[pageNum]-1)*$numPerPage;}
	$num=mysql_query("SELECT s.* FROM $cfg[dbpre]share_list AS s LEFT JOIN $cfg[dbpre]user AS u ON s.user_id=u.id $where");//当前频道条数
	$total=mysql_num_rows($num);//总条数	
	$page=new page(array('total'=>$total,'perpage'=>$numPerPage));

	$sql="SELECT s.*,u.username FROM $cfg[dbpre]share_list AS s LEFT JOIN $cfg[dbpre]user AS u ON s.user_id=u.id $where ORDER BY id DESC LIMIT $pageNum,$numPerPage";
	$db->query($sql);
	$list=$db->fetchAll();
	
	$share_time_control = $cfg['share_time'] && $cfg['share_time']>0 ? $cfg['share_time'] : 1;
	
	foreach ($list as $key=>$value) {
		$pass_time = ceil((time()-$value['create_time'])/$share_time_control);
		if ($pass_time<60) {
			$list[$key]['time'] = $lang['label']['time1'];
		} else if ($pass_time>60 && $pass_time<3600) {
			$list[$key]['time'] = ceil($pass_time/60).$lang['label']['time2'];
		} else if ($pass_time>3600 && $pass_time<86400) {
			$list[$key]['time'] = ceil($pass_time/3600).$lang['label']['time3'];
		} else if ($pass_time>86400) {
			$list[$key]['time'] = ceil($pass_time/86400).$lang['label']['time4'];
		}
		$list[$key]['face'] = explode(",", $list[$key]['face']);
		
		$list[$key]['link'] = substr($list[$key]['link'],0,strlen($list[$key]['link'])-3)."...";
	}
	//print_r($list);
	$smt->assign('list',$list);
	$smt->assign('total',$total);
	$smt->display($tpl_pre.'index.html');
	exit;
}

else if($do=="jump"){
	
	//检查登录
	if (!isset($_SESSION['userid'])) {
		echo success($lang['tips']['please_login'],"?action=member&do=login");
		exit;
	}
	
	$short = intval($_GET['short']);
	
	$sql="SELECT s.*,u.expire FROM $cfg[dbpre]share_list AS s LEFT JOIN $cfg[dbpre]user AS u ON s.user_id=u.id WHERE s.id = '$short' LIMIT 1";
	$db->query($sql);
	$row=$db->fetchRow();
	
	//新用户无限制试用
	if ($cfg['free_try_time'] && $cfg['free_try_time']>0) {
		$expire = time() - $user_info['create_datetime'];
		if ($expire<= $cfg['free_try_time']) {
			$expire = ceil(($cfg['free_try_time']-$expire)/60);
			$smt->assign('msg',$lang['tips']['try_vip']);
			$smt->assign('url',$row['link']);
			$smt->assign('wait','1');
			$smt->assign('expire',$expire);
			$smt->display('wait_try_vip.html');
			exit();
		}		
	}	
	
	//跳转计数限制
	$sql="SELECT jump_count FROM $cfg[dbpre]user WHERE id = '$_SESSION[userid]' LIMIT 1";
	$db->query($sql);
	$row1=$db->fetchRow();
	$today = date("Ymd", time());
	if ($row1 && $row1['jump_count']!='') {
		$jump_count = explode(":", $row1['jump_count']);
		$count = $jump_count[1] && $jump_count[0]==$today ? $jump_count[1] : 0;
		if ($jump_count[1] && $count>=$cfg['max_jump'] && $jump_count[0]==$today) {
			$smt->assign('msg',sprintf($lang['tips']['max_jump'],$cfg['max_jump']));
			$smt->assign('url','index.php');
			$smt->assign('wait','3');
			$smt->display('error.html');
			exit();
		} else {
			$sql = "UPDATE $cfg[dbpre]user SET `jump_count`='" . $today . ":" . ($count+1) . "' WHERE `id`=" . $_SESSION['userid'];
			$db->query($sql);
		}
	} else {
		$sql = "UPDATE $cfg[dbpre]user SET `jump_count`='" . $today . ":1' WHERE `id`=" . $_SESSION['userid'];
		$db->query($sql);
	}
	
	if ($user_info['levelid']>1) {
		//header('Location:'.$row['link']);
		$smt->assign('msg',$lang['tips']['vip_jump']);
		$smt->assign('url',$row['link']);
		$smt->assign('wait','1');
		$smt->display('wait_vip.html');
	} else {
		//非VIP进行限制
		if ($cfg['fee'] && !$cfg['wait']) {
			if ($user_info['money']>$cfg['fee']) {
				$sql="UPDATE $cfg[dbpre]user SET `money`=money-$cfg[fee] WHERE id = '$_SESSION[userid]' LIMIT 1";
				if ($db->query($sql)) {
					$sql="INSERT INTO $cfg[dbpre]account_log (`user_id`,`pay_type`,`amount`,`left_money`,`mark`,`pay_datetime`) VALUES ('$record[id]','3','-$cfg[fee]','".($user_info['money']-$cfg['fee'])."','".$lang['tips']['jump_link']."','".$time."')";
					$db->query($sql);
					$smt->assign('msg',$lang['tips']['member_jump']);
					$smt->assign('url',$row['link']);
					$smt->assign('wait','3');
					$smt->display('wait_vip.html');
					//header('Location:'.$row['link']);
				}
			} else {
				$smt->assign('msg',$lang['tips']['lack_balance']);
				$smt->assign('url','?action=member&do=charge');
				$smt->assign('wait','3');
				$smt->display('error.html');
			}
		} else if (!$cfg['fee'] && $cfg['wait']) {
			$smt->assign('msg',sprintf($lang['tips']['member_jump_wait'],$cfg['wait']));
			$smt->assign('url',$row['link']);
			$smt->assign('wait',$cfg['wait']);
			$smt->display($tpl_pre.'wait.html');
		}
	}
	
	exit;
}


else if($do=="top"){
	//if(!isLogin()){exit($lang_cn_member['rabc_is_login']);} //判断是否登录
	$smt->display($tpl_pre.'top.html');
	exit;
}

else if($do=="menu"){
	//if(!isLogin()){exit($lang_cn_member['rabc_is_login']);} //判断是否登录
	$smt->display($tpl_pre.'menu.html');
	exit;
}

else if($do=="main"){
	//if(!isLogin()){exit($lang_cn_member['rabc_is_login']);} //判断是否登录
	$smt->display($tpl_pre.'main.html');
	exit;
}
?>
