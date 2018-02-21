<?php
ob_start();
if(!defined('CORE'))exit("error!"); 

$do = $_REQUEST['do'];

if($do=="jumpcharge"){
	//金额
	$amount = intval($_POST['amount']);
	
	//商户订单号，商户网站订单系统中唯一订单号，必填
	$out_trade_no = date("YmdHis",time()).$_SESSION['userid'];
	
	//订单名称，必填
	$subject = $lang['pay']['charge'];
	
	//付款金额，必填
	$amount = ($_POST['amount']);
	
	if ($_POST['channel']=='alipay') {//支付宝

		//付款金额，必填
		$total_amount = $amount;
	
		//商品描述，可空
		$body = '';
		
		require('pay/alipay/alipay.config.php');
		require('pay/alipay/lib/alipay_submit.class.php');
		
		/**************************请求参数**************************/
		//支付类型
		$payment_type = '1';
		//必填，不能修改
		//服务器异步通知页面路径
		$notify_url = 'http://www.009wp.com/pay/alipay/notify_url.php';
		//需http://格式的完整路径，不能加?id=123这类自定义参数
		//echo $notify_url."<br/>";
		//页面跳转同步通知页面路径
		$return_url = 'http://www.009wp.com/?action=pay&do=success&channel=alipay';
		//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
		
		//卖家支付宝帐户
		//$seller_email = $_POST['seller_email'] ? $_POST['seller_email'] : $options['alipay_account'];
		//必填
		
		//商户订单号
		$out_trade_no = $out_trade_no;
		//商户网站订单系统中唯一订单号，必填
		//echo $out_trade_no."<br/>";
		//订单名称
		$subject = $subject;
		//必填
		//echo $subject."<br/>";
		//付款金额
		$price = intval($amount);
		//必填
		//echo $price."<br/>";
		//商品数量
		$quantity = '1';
		//必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
		//物流费用
		$logistics_fee = '0.00';
		//必填，即运费
		//物流类型
		$logistics_type = 'EXPRESS';//DIRECT
		//必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
		//物流支付方式
		$logistics_payment = 'SELLER_PAY';
		//必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
		//订单描述
		
		$body = $_POST['body'] ? $_POST['body'] : "";
		//商品展示地址
		$show_url = $_POST['show_url'] ? $_POST['show_url'] : "";
		//需以http://开头的完整路径，如：http://www.xxx.com/myorder.html
		
		//收货人姓名
		$receive_name = $_POST['receive_name'] ? $_POST['receive_name'] : "";
		//如：张三
		
		//收货人地址
		$receive_address = $_POST['receive_address'] ? $_POST['receive_address'] : "";
		//如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
		
		//收货人邮编
		$receive_zip = $_POST['receive_zip'] ? $_POST['receive_zip'] : "";
		//如：123456
		
		//收货人电话号码
		$receive_phone = $_POST['receive_phone'] ? $_POST['receive_phone'] : "";
		//如：0571-88158090
		
		//收货人手机号码
		$receive_mobile = $_POST['receive_mobile'] ? $_POST['receive_mobile'] : "";
		//如：13312341234
		
		/************************************************************/
		  //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1
		//构造要请求的参数数组，无需改动
		$parameter = array(
			//'service' => 'create_partner_trade_by_buyer',
			'service' => 'create_direct_pay_by_user',
			'partner' => trim($alipay_config['partner']),
			'seller_email' => $alipay_config['alipay_account'],
			'payment_type' => $payment_type,
			'notify_url' => $notify_url,
			'return_url' => $return_url,
			'out_trade_no' => $out_trade_no,
			'subject' => $subject,
			'total_fee'	=> $price, 
			//'price'	=> $price,
			//'quantity'	=> $quantity,
			'logistics_fee'	=> $logistics_fee,
			'logistics_type'	=> $logistics_type,
			'logistics_payment'	=> $logistics_payment,
			'body' => $body,
			'show_url' => $show_url,
			//'receive_name' => $receive_name,
			//'receive_address' => $receive_address,
			//'receive_zip' => $receive_zip,
			//'receive_phone'	=> $receive_phone,
			//'receive_mobile' => $receive_mobile,
			'anti_phishing_key'	=> $anti_phishing_key,
			'exter_invoke_ip'	=> $exter_invoke_ip,
			'_input_charset' => trim(strtolower($alipay_config['input_charset']))
		);
		//echo $parameter."<br/>";
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter, 'get', $lang['pay']['jumping']);
		
		//产生订单
		$sql="INSERT INTO $cfg[dbpre]pay_log (`user_id`,`order_id`,`amount`,`status`,`mark`,`pay_datetime`) VALUES ('$_SESSION[userid]','$out_trade_no','".($price*$cfg['coin_rate'])."','0','".$lang['pay']['pay_type1'].$subject."','".time()."')";
		$db->query($sql);
		
		echo $html_text;
		
	} else if ($_POST['channel']=='weixin2') { //微信扫码
		include_once("pay/WxPayPubHelper/WxPayPubHelper.php");
		
		$user_id = $_SESSION['userid'];
		
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();
		$price=$amount;
		//设置统一支付接口参数
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//spbill_create_ip已填,商户无需重复填写
		//sign已填,商户无需重复填写
		$unifiedOrder->setParameter("body",$lang['pay']['body']);//商品描述
		//自定义订单号，此处仅作举例
		//$timeStamp = time();
		//echo $_POST['price'];
		$unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 
		
		$unifiedOrder->setParameter("total_fee",$price*100);//总金额$price*100
		
		//$unifiedOrder->setParameter("notify_url",'http://www.009wp.com/?action=pay&do=success&channel=weixin');//通知地址 
		$unifiedOrder->setParameter("notify_url",'http://www.009wp.com/pay/demo/notify_url.php');//通知地址 
		
		if ($tpl_pre!='') { 
			$unifiedOrder->setParameter("trade_type","MWEB");//交易类型
		} else {
			$unifiedOrder->setParameter("trade_type","NATIVE");//交易类型
		}
		//非必填参数，商户可根据实际情况选填
		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
		//$unifiedOrder->setParameter("device_info","XXXX");//设备号 
		//$unifiedOrder->setParameter("attach","XXXX");//附加数据 
		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
		//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
		//$unifiedOrder->setParameter("openid","XXXX");//用户标识
		//$unifiedOrder->setParameter("product_id","XXXX");//商品ID
		// print_r($unifiedOrder);
		//获取统一支付接口结果
		$unifiedOrderResult = $unifiedOrder->getResult();
		// print_r($unifiedOrderResult);
		//商户根据实际情况设置相应的处理流程
		if ($unifiedOrderResult["return_code"] == "FAIL") 
		{
			//商户自行增加处理流程
			echo $lang['pay']['send_error'].$unifiedOrderResult['return_msg']."<br>";
		}
		elseif($unifiedOrderResult["result_code"] == "FAIL")
		{
			//商户自行增加处理流程
			echo $lang['pay']['error_code'].$unifiedOrderResult['err_code']."<br>";
			//echo "错误代码描述：".$unifiedOrderResult['err_code_des']."<br>";
		}
		elseif($unifiedOrderResult["code_url"] != NULL || $unifiedOrderResult["mweb_url"] != NULL)
		{
			//商户自行增加处理流程
			
			//产生订单
			$sql="INSERT INTO $cfg[dbpre]pay_log (`user_id`,`order_id`,`amount`,`status`,`mark`,`pay_datetime`) VALUES ('$_SESSION[userid]','$out_trade_no','".($price*$cfg['coin_rate'])."','0','".$lang['pay']['pay_type2'].$subject."','".time()."')";
			$db->query($sql);
			
			//从统一支付接口获取到code_url
			if ($unifiedOrderResult["code_url"] != NULL)
			{
				$code_url = $unifiedOrderResult["code_url"];
				$smt->assign('code_url',$code_url);
			}
			else
			{
				header("Location:".$unifiedOrderResult["mweb_url"]."&redirect_url=".urlencode('http://www.009wp.com/?action=pay&do=orderquery&out_trade_no='.$out_trade_no.'&jump=yes'));
				$smt->assign('mweb_url',$unifiedOrderResult["mweb_url"]);
			}
		}

		$smt->assign('out_trade_no',$out_trade_no);
		$smt->assign('title',$lang['pay']['pay_type2']);
		$smt->display($tpl_pre.'wxpay.html');
		exit;
	}
}

if ($do=='success') {
	require('pay/alipay/alipay.config.php');
	require('pay/alipay/lib/alipay_submit.class.php');
	require('pay/alipay/lib/alipay_notify.class.php');
	
	//删除不参加签名验证的项
	unset($_GET['action']);
	unset($_GET['do']);
	unset($_GET['channel']);
	
	//计算得出通知验证结果
	$alipayNotify = new AlipayNotify($alipay_config);
	$verify_result = $alipayNotify->verifyReturn();
	if ($verify_result) {
		$out_trade_no = $_GET['out_trade_no']; //商户订单号
		$trade_no = $_GET['trade_no']; //支付宝交易号
		$trade_status = $_GET['trade_status']; //交易状态
		
		$sql="SELECT * FROM $cfg[dbpre]pay_log WHERE `status`=1 AND order_id='" . $out_trade_no . "' AND user_id='".$_SESSION['userid']."' AND mark='$trade_status'";
		$db->query($sql);
		$row = $db->fetchRow();
		if ($row) {
			$sql="UPDATE $cfg[dbpre]pay_log SET `mark`='".$lang['pay']['pay_type1a']."' WHERE `status`=1 AND order_id='" . $out_trade_no . "' AND user_id='".$_SESSION['userid']."'";
			if ($db->query($sql)) {
				$sql="SELECT amount FROM $cfg[dbpre]pay_log WHERE order_id='" . $out_trade_no . "' AND user_id='".$_SESSION['userid']."'";
				$db->query($sql);
				$row = $db->fetchRow();
				if ($row) {
					$pay_amount = $row['amount'];
					$sql="SELECT money FROM $cfg[dbpre]user WHERE id='" . $_SESSION['userid'] . "'";
					$db->query($sql);
					$row1 = $db->fetchRow();
					$new_money = $row1['money'] + $row['amount'];
					$sql="UPDATE $cfg[dbpre]user SET money='$new_money' WHERE id='" . $_SESSION['userid'] . "'";
					$db->query($sql);
					
					//增加金币记录
					$sql="INSERT INTO $cfg[dbpre]account_log (`user_id`,`pay_type`,`amount`,`left_money`,`mark`,`pay_datetime`) VALUES ('$_SESSION[userid]','2','$pay_amount','$new_money','".$lang['tips']['charge_res']."','".time()."')";
					$db->query($sql);
					echo success($lang['tips']['charge_success'],"?action=member&do=paylog");
				}
			}
		} else {
			echo success($lang['tips']['charge_fail'],"?action=member&do=paylog");
		}
	}
}

if ($do=='orderquery') {
	include_once("pay/WxPayPubHelper/WxPayPubHelper.php");
	
	//5秒刷新，防止一些变态浏览器返回时无反应
	echo '<script language="javascript">setInterval(function(){window.location.reload();}, 5000);</script>';
	
	//订单号
	if (!isset($_REQUEST["out_trade_no"]))
	{
		$out_trade_no = " ";
	}else{
	    $out_trade_no = $_REQUEST["out_trade_no"];

		//使用订单查询接口
		$orderQuery = new OrderQuery_pub();
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//sign已填,商户无需重复填写
		$orderQuery->setParameter("out_trade_no","$out_trade_no");//商户订单号 
		//非必填参数，商户可根据实际情况选填
		//$orderQuery->setParameter("sub_mch_id","XXXX");//子商户号  
		//$orderQuery->setParameter("transaction_id","XXXX");//微信订单号
		
		//获取订单查询结果
		$orderQueryResult = $orderQuery->getResult();
		//print_r($orderQueryResult);
		//商户根据实际情况设置相应的处理流程,此处仅作举例
		if ($orderQueryResult["return_code"] == "FAIL") {
			echo $lang['pay']['send_error'].$orderQueryResult['return_msg']."<br>";
		}
		elseif($orderQueryResult["result_code"] == "FAIL"){
			echo $lang['pay']['error_code'].$orderQueryResult['err_code']."<br>";
			echo $lang['pay']['error_detail'].$orderQueryResult['err_code_des']."<br>";
		}
		else{
			$out_trade_no=$orderQueryResult['out_trade_no']; //商户订单号
			$transaction_id=$orderQueryResult['transaction_id'];
			if($transaction_id){
				//查询并同步订单状态
				$sql="SELECT * FROM $cfg[dbpre]pay_log WHERE `status`=0 AND order_id='" . $out_trade_no . "' AND user_id='".$_SESSION['userid']."'";
				$db->query($sql);
				$row = $db->fetchRow();
				if ($row) {
					$sql="UPDATE $cfg[dbpre]pay_log SET `status`=1 WHERE `status`=0 AND order_id='" . $out_trade_no . "' AND user_id='".$_SESSION['userid']."'";
					if ($db->query($sql)) {
						$sql="SELECT amount FROM $cfg[dbpre]pay_log WHERE order_id='" . $out_trade_no . "' AND user_id='".$_SESSION['userid']."'";
						$db->query($sql);
						$row = $db->fetchRow();
						if ($row) {
							$pay_amount = $row['amount'];
							$sql="SELECT money FROM $cfg[dbpre]user WHERE id='" . $_SESSION['userid'] . "'";
							$db->query($sql);
							$row1 = $db->fetchRow();
							$new_money = $row1['money'] + $row['amount'];
							$sql="UPDATE $cfg[dbpre]user SET money='$new_money' WHERE id='" . $_SESSION['userid'] . "'";
							$db->query($sql);
							
							$_SESSION['money'] = $new_money;
							
							//增加金币记录
							$sql="INSERT INTO $cfg[dbpre]account_log (`user_id`,`pay_type`,`amount`,`left_money`,`mark`,`pay_datetime`) VALUES ('$_SESSION[userid]','2','$pay_amount','$new_money','".$lang['tips']['charge_res']."','".time()."')";
							$db->query($sql);
						}
						if (isset($_REQUEST['jump']) && $_REQUEST['jump']=='yes') {
							echo success($lang['tips']['charge_success'],"?action=member&do=paylog");
						} else {
							echo "success";
						}
					} else {
						if (isset($_REQUEST['jump']) && $_REQUEST['jump']=='yes') {
							echo success($lang['tips']['charge_fail2'].$cfg['qq'],"?action=member&do=service");
						} else {
							echo "fail";
						}
					}
				}
				exit();
			}
		}	
	}
}





?>