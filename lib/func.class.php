<?php
//====================================================
//		FileName: func.class.php
//		Summary:  系统函数配置
//====================================================

if(!defined('CORE'))exit("error!"); 

//检查登录
function check_login() {
	if (!isset($_SESSION['userid']) || $_SESSION['userid']=='') {
		$_SESSION = array();
		session_destroy();
		exit($lang_cn_member['rabc_timeout']);
	}
}

//安全验证
function _RunMagicQuotes(&$svar){
	if(!get_magic_quotes_gpc())	{
		if( is_array($svar) ){
			foreach($svar as $_k => $_v) $svar[$_k] = _RunMagicQuotes($_v);
		}else{
			$svar = addslashes($svar);
		}
	}
	return $svar;
}

//SMARTY模版配置
function smarty_cfg($self){
	global $cfg;
	global $user_list;
	global $css;
	$self->template_dir=$cfg["template_dir"];
	$self->cache_dir=$cfg["cache_dir"];
	$self->compile_dir=$cfg["compile_dir"];
	$self->assign('cfg',$cfg);
	$self->assign('user_list',$user_list);
	$self->assign('css',$css);
}

//安全过滤
foreach(Array('_GET','_POST','_COOKIE') as $_request){
	foreach($$_request as $_k => $_v) ${$_k} = _RunMagicQuotes($_v);
}

//dwz_ajax_succee
function success($msg,$url){
	$msg = $msg ? $msg : "操作成功!";
	$val = "<script>alert('".$msg."');window.location='".$url."';</script>";
	return $val;
}

//dwz_ajax_error
function error($msg,$url){
	$msg = $msg ? $msg : "操作错误!";
	$url = $url ? $url : "history.back();";
	$val = "<script>alert('".$msg."');" . $url . "</script>";
	return $val;
}

/*打印友好的数组形式*/
function dump($array){
	echo "<pre>";
	print_r($array);
	echo "<pre>";
}

//中文截取
function cnString($text, $length){
	if(strlen($text) <= $length){
		return $text;
	}
	$str = substr($text, 0, $length) . chr(0) ; 
	return $str;
}


//数组转化为select
function select($arr,$name,$self="",$cn_name="选择",$class="combox"){
	$slt .= "<select name=\"".$name."\" class=\"input ".$class."\" title=\"此项目必填\" validate=\"required:true\">";
	$slt .= "<option value=\"\" selected=\"selected\">".$cn_name."</option>";
	foreach($arr as $key=>$val){
		if($key==$self){
		$slt .= "    <option value=\"".$key."\" selected=\"selected\">".$val."</option>";
		}else{
		$slt .= "    <option value=\"".$key."\">".$val."</option>";
		}
	}
    $slt .= "</select>";
	return $slt;
}


//读取目录所有的文件名
function myreaddir($dir) {
	$handle=opendir($dir);
	$i=0;
	while($file=readdir($handle)){
		if ($file!="." && $file!=".." && !is_dir($file)){
		$list[$i]=$file;
		$i=$i+1;
		}
	}
	closedir($handle);
	rsort($list);
	return $list;
}

//验证内容
function Ifvalidate($arr){
	global $lang_cn;
	foreach($arr as $val){
		if(!$val){exit($lang_cn['validate']);}
	}
}

//arr=>json
function my_json_encode($phparr){
    if(function_exists("json_encode")){
      return json_encode($phparr);
    }else{
      require_once './include/json.php';
      $json = new Services_JSON;
      return $json->encode($phparr);
    }
}

//json=>arr
function json_to_array($web){
	$arr=array();
	foreach($web as $k=>$w){
	if(is_object($w)) $arr[$k]=json_to_array($w);  //判断类型是不是object
	else $arr[$k]=$w;
	}
	return $arr;
}

//数组转化为type循环th名称
function type_th($arr){
	foreach($arr as $key=>$val){
		$slt .= "<th>".$val."</th>\n";
	}
	return $slt;
}

//生成插入sql语句
function insert_sql($arr){
	$insert_sql = " (";
	foreach ($arr as $key=>$value) {
		$insert_sql .= "`".$key."`,";
	}
	$insert_sql = rtrim($insert_sql,",");
	$insert_sql .= ") VALUES (";
	foreach ($arr as $key=>$value) {
		$insert_sql .= "'".$value."',";
	}
	$insert_sql = rtrim($insert_sql,",") . ") ";
	
	return $insert_sql;
}

//生成更新sql语句
function update_sql($arr){
	$update_sql = " set ";
	foreach ($arr as $key=>$value) {
		$update_sql .= "`".$key."`='".$value."',";
	}
	
	return rtrim($update_sql,",");
}

//获取文件格式
function get_file_type($filename) { 
	$type=explode("." , $filename);
	$count=count($type)-1;
	return $type[$count]; 
}

//获取文件大小
function get_size($file_name)  {
	$fs=filesize($file_name);
	if($fs<1024)
	return $fs." Byte";
	elseif($fs>=1024&&$fs<1024*1024)
	return @number_format($fs/1024, 3)." KB";
	elseif($fs>=1024*1024 && $fs<1024*1024*1024)
	return @number_format($fs/(1024*1024), 3)." M";
	elseif($fs>=1024*1024*1024)
	return @number_format($fs/(1024*1024*1024), 3)." G";
}

//遍历删除
function delDirAndFile( $dirName, $include_dir=1) {
  if ( $handle = opendir( "$dirName" ) ) {
	 while ( false !== ( $item = readdir( $handle ) ) ) {
		if ( $item != "." && $item != ".." ) {
		   if ( is_dir( "$dirName/$item" ) ) {
				  delDirAndFile( "$dirName/$item" );
		   } else {
				  @unlink( "$dirName/$item" );
		   }
		}
	 }
	 closedir( $handle );
	 if ($include_dir==1) {
		 @rmdir( $dirName );
	 }
	 return true;
  }
}

//创建目录
function mkdirm($path) { 
  if (!file_exists($path)) 
  { 
	mkdirm(dirname($path)); 
	mkdir($path, 0777); 
  }
}

//文件上传
function upfile($file,$str,$pre,$path) {
	global $cfg;
	$allow_file_type=$cfg["allow_file_type"]; //文件限制类型
	$file_size=$cfg["allow_file_size"]*1024;  //文件大小限制
	$file_path=!$path ? "data/userup/".date("Ym",time())."/" : $path;    //默认文件保存路径
	if (!file_exists($file_path)) { 
		mkdirm($file_path);
	}
	
	$imgurl="";
	if (isset($file["name"]) && $file["name"]!="") {
		$file_type=get_file_type($file["name"]);
		if (!stripos($allow_file_type,$file_type)) {
			die(error($str."格式不正确!"));
		} elseif ($file["size"] > $file_size){
			die(error($str."文件太大!"));
		} else {
			//$full_file_path=$file_path.$file["name"];
			if ($file["error"] > 0) {
			  die(error("Return Code: " . $file["error"] . "<br />"));
			} else  {
			  $new_file_name=$pre.date("YmdHis",time()).".".$file_type;
			  
			  $imgurl=$file_path.$new_file_name;
			  move_uploaded_file($file["tmp_name"],$imgurl);
			}
		}
	}
	
	return $imgurl;
}

//查询重复
function has_record($table,$sql_str){
	global $db, $cfg;
	$sql="SELECT * FROM `$cfg[dbpre]$table` WHERE $sql_str LIMIT 1";
	$db->query($sql);
	if($db->fetchRow()){
		return true;
	}
	return false;
}

//操作日志
function adminlog($str){
	global $db, $cfg;
	$log_time = date("Y-m-d H:i:s", time());
	$user_id = $_SESSION['userid'];
	$mac = new GetMacAddr(PHP_OS);
	$ip_address = $mac->mac_addr;  
	$sql="INSERT INTO `$cfg[dbpre]admin_log` (log_time, user_id, log_info, ip_address) VALUES ('$log_time', '$user_id', '$str', '$ip_address')";
	$db->query($sql);
}

//查询用户简要信息
function get_user_info($user_id,$column='') {
	global $db, $cfg;
	$sql="SELECT username,name,gender,department,position FROM `$cfg[dbpre]user` WHERE id='$user_id' LIMIT 1";
	$db->query($sql);
	$row = $db->fetchRow();
	
	return !$column ? $row : $row[$column];
}

//查询用户ID
function get_user_id($username,$column='') {
	global $db, $cfg;
	$sql="SELECT id,username,name,gender,department,position FROM `$cfg[dbpre]user` WHERE username='$username' LIMIT 1";
	$db->query($sql);
	$row = $db->fetchRow();
	
	return !$column ? $row : $row[$column];
}

//菜单
function getActions($type=0) {
	global $db, $cfg;
	
	switch ($type) {
		case 0:
			$sql="SELECT * FROM $cfg[dbpre]admin_action ORDER BY parent_id ASC, orders ASC, action_id ASC";
			$db->query($sql);
			$arr = $db->fetchAll();
			
			$new_arr = array();
			foreach ($arr as $key=>$value) {
				if ($value['parent_id']==0 && $value['level']==1) {
					$new_arr[$value['action_id']]['info'] = $value;
				} else if ($value['parent_id']>0 && $value['level']==2) {
					$new_arr[$value['parent_id']]['son'][$value['action_id']]['info'] = $value;
				} else if ($value['parent_id']>0 && $value['level']==3) {
					$son_id = $value['parent_id'];
					foreach ($arr as $k=>$v) {
						if ($v['action_id']==$son_id && $v['level']==2) {
							$parent_id = $v['parent_id'];
							break;
						}
					}
					$new_arr[$parent_id]['son'][$value['parent_id']]['child'][] = $value;
				}
			}
		break;
		
		case 1:
			$sql="SELECT u.actions,r.action_list FROM $cfg[dbpre]user AS u LEFT JOIN $cfg[dbpre]role AS r ON u.roleid=r.role_id WHERE u.id='$_SESSION[userid]'";
			$db->query($sql);
			$row = $db->fetchRow();
			$actions = $row['actions'] ? $row['actions'] : $row['action_list'];
			$addsql = $actions ? " AND action_id IN ($actions)" : "";
			
			$sql=" SELECT * FROM $cfg[dbpre]admin_action WHERE 1=1 $addsql ORDER BY parent_id ASC, orders ASC, action_id ASC";
			$db->query($sql);
			$arr = $db->fetchAll();
			
			$new_arr = array();
			foreach ($arr as $key=>$value) {
				if ($value['parent_id']==0 && $value['level']==1) {
					$new_arr[$value['action_id']]['info'] = $value;
				} else if ($value['parent_id']>0 && $value['level']==2) {
					$new_arr[$value['parent_id']]['son'][$value['action_id']]['info'] = $value;
				} else if ($value['parent_id']>0 && $value['level']==3) {
					$son_id = $value['parent_id'];
					foreach ($arr as $k=>$v) {
						if ($v['action_id']==$son_id && $v['level']==2) {
							$parent_id = $v['parent_id'];
							break;
						}
					}
					$new_arr[$parent_id]['son'][$value['parent_id']]['child'][] = $value;
				}
			}
		break;
	}
	//print_r($new_arr);
	return $new_arr;
}

?>