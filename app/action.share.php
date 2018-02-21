<?php
if(!defined('CORE'))exit("error!"); 

//链接	
if($do==""){
	exit;
}

//写入
if($do=="add"){
	ini_set('user_agent', 'Mozilla/5.0 (Linux; Android 4.2.1; en-us; Nexus 4 Build/JOP40D) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.166 Mobile Safari/535.19');
	$json = new Services_JSON;
	$result = array(
				'status' => '',
				'msg' => '',
				'money' => '',
	);
	
	//检查登录
	if (!isset($_SESSION['userid'])) {
		$result['status'] = '202';
		$result['msg'] = $lang['tips']['login_share'];
		$rejson = $json->encode($result);
		echo $rejson;
		exit;
	}

	If_rabc($action,$do); //检测权限

	//录入时间
	$creat_datetime = time();
	
	//解析链接
	$links = isset($_POST['short']) && $_POST['short']!='' ? explode(",",trim($_POST['short'])) : "";
	
	$money = $cfg['link_prize'];  //分享一个链接获得的币数
	//数据处理
	$sql = "";
	$key = 0;
	$max_links = 0;
	foreach ($links as $value) {
		//过滤中文
		if (strpos($value, 'http')>0) {
			$value = explode('http',$value);
			if ($value[1]) {
				$value ='http'.$value[1];
			}
		}
		
		//判断重复
		$sql = "SELECT count(*) AS count FROM $cfg[dbpre]share_list WHERE link='$value'";
		$db -> query($sql);
		$count = $db -> fetchRow();
		if ($count['count'] > 0) {
			continue;
		}
		
		//每日条数限制
		if ($cfg['max_links']!='' && $cfg['max_links']!='0') {
			$sql = "SELECT qq_id FROM $cfg[dbpre]user WHERE id='$_SESSION[userid]'";
			$db -> query($sql);
			$row = $db -> fetchRow();
			if ($row['qq_id']!="0") {
				$sql = "SELECT count(*) AS count FROM $cfg[dbpre]share_list WHERE user_id='$_SESSION[userid]' AND create_time>".strtotime(date("Y-m-d",$creat_datetime)." 00:00:00");
				$db -> query($sql);
				$max_count = $db -> fetchRow();
				if ($max_count['count'] >= $cfg['max_links']) {
					$max_links = 1;
					break;
				}
			}
		}
		
		//采集开始
		$content = file_get_contents($value);
	
		if ($content)
		{
			$content = str_replace("'", "", autoiconv($content));
			
			//群组成员规则
			$rule_group_member = 'FileUtils.g_name="@@@";FileUtils.u_avator_list';
			
			//群组成员数量规则
			$rule_group_member_number = '<span id="group-user-num">@@@</span>';
			
			//群组成员头像规则
			$rule_group_member_avatar = 'FileUtils.u_avator_list=JSON.parse(@@@);FileUtils.mobileModel="Android";';
			
			preg_match("/".restr($rule_group_member)."/is",$content,$regm);
			$group_member = fstr(strip_tags($regm[1]), restr($rule_group_member, 'f'));
			//$group_member = str_replace("*", "-", $group_member);
			if (strpos($group_member,'*')) {
				$group_member = str_replace("*", "-", $group_member);
			} else {
				$len = strlen($group_member);
				$group_member = substr($group_member,0,($len>15 ? 6 : 3))."----".substr($group_member,$len-($len>15 ? 6 : 3),$len);
			}
			
			//echo $group_member;
			
			preg_match("/".restr($rule_group_member_number)."/is",$content,$regm);
			$group_number = fstr(strip_tags($regm[1]), restr($rule_group_member_number, 'f'));
			//echo $group_number;
			
			preg_match("/".restr($rule_group_member_avatar)."/is",$content,$regm);
			$group_avatar = fstr(strip_tags($regm[1]), restr($rule_group_member_avatar, 'f'));
			$group_avatar = str_replace(array("\\",'("','")'), array("","",""), $group_avatar);
			$rowjson = $json->decode($group_avatar);
			$group_avatars = array();
			foreach ($rowjson as $value1) {
				$group_avatars[] = $value1->photo;
			}
			$group_avatar_str = implode(",", $group_avatars);
			//echo implode(",", $group_avatars);
			
			if ($group_member && $group_number>0) {
				$insert_arr = array(
									'face' => $group_avatar_str,
									'name' => $group_member,
									'number' => $group_number,
									'link' => $value,
									'create_time' => $creat_datetime,
									'user_id' => $_SESSION['userid']
								  );
				$sql = "INSERT INTO `$cfg[dbpre]share_list` " . insert_sql($insert_arr);
				if ($db->query($sql)) {
					$key++;
					
					//赠送Z币
					$sql1 = "INSERT INTO `$cfg[dbpre]account_log` (`user_id`,`pay_type`,`amount`,`left_money`,`mark`,`pay_datetime`) VALUES ('$_SESSION[userid]','2','$money','".($user_info['money']+$money)."','".$lang['tips']['link_prize']."','$creat_datetime')";
					$db->query($sql1);
					
					//更新总额
					$sql1 = "UPDATE `$cfg[dbpre]user` SET money=(money+$money) WHERE id='$_SESSION[userid]'";
					$db->query($sql1);
				}
			}
		}
	}
	
	//更新SESSION
	$sql = "SELECT money FROM $cfg[dbpre]user WHERE id = '$_SESSION[userid]'";
	$db->query($sql);
	$row = $db->fetchRow();
	$_SESSION['money'] = $row['money'];
	
	//删除旧链接
	$keeps = $cfg['index_size'];  //保留最新记录条数，后台设置
	$sql = "SELECT id FROM $cfg[dbpre]share_list ORDER BY id DESC LIMIT $keeps";
	$db->query($sql);
	$list = $db->fetchAll();
	if (count($list)==$keeps) {
		$del_id = end($list);
		$sql = "DELETE FROM $cfg[dbpre]share_list WHERE id<" . $del_id['id'];
		$db->query($sql);
	}

	$result['msg'] = $key>0 ? sprintf($lang['tips']['share_msg1'],$key) . ($max_links==1 ? sprintf($lang['tips']['share_msg2'],$cfg['max_links']) : "") : ($max_links==0 ? $lang['tips']['share_msg3'] : sprintf($lang['tips']['share_msg2'],$cfg['max_links']));
	$result['money'] = $key*$money;
	$result['status'] = '200';
	
	$rejson = $json->encode($result);
	echo $rejson;
}

function restr($str,$filter='')
{
	if ($str != "" && $str != null)
	{
		if (strpos($str,'|||')>-1)
		{
			$stra = explode("|||", $str);
			$str = str_replace("@@@","(.*?)",$stra[0]);
			$strf = $stra[1];
		}
		else
		{
			$str = str_replace("@@@","(.*?)",$str);
		}
		$str = str_replace(array("/","'","&#39;"), array("\/","",""), $str);
	}
	return !$filter=='f' ? $str : $strf;
}

function fstr($str, $strf='')
{
	if ($str != "" && $str != null && $strf != "" && $strf != null)
	{
		if (strpos($strf,'$$$')>-1)
		{
			$strfa = explode("$$$", $strf);
			$strf1 = explode(",", $strfa[0]);
			$strf2 = explode(",", $strfa[1]);
			$str = str_replace($strf1, $strf2, $str);
		}
		else
		{
			$strf = explode(",", $strf);
			$str = str_replace($strf, array(), $str);
		}
	}
	$str = trim(str_replace(array("\n","\r"), array("",""), $str));
	return $str;
}

function fchs($str,$strr='')
{
	$str = $strr ? rtrim($str, $strr) : $str;
	$str = trim(str_replace(array("&lt;","&gt;","&quot;","&amp;","&nbsp;"," ","/"), array("","","","","","",""), $str));
	return preg_replace("/[\x80-\xff]/","",$str); 
}

function autoiconv($str,$type = "gb2312//IGNORE")
{
	if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$str) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$str) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$str) == true) 
	{
		return $str;
	}
	else
	{
		return iconv($type, "utf-8", $str);
	}
}

?>