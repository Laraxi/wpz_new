<?php

define('CORE', str_replace("\\", '/', substr(__FILE__, 0, strrpos(dirname(dirname(__FILE__)), DIRECTORY_SEPARATOR))).'/');

require_once(CORE."data/config.php");            //配置文件
require_once(CORE."lib/mysql.class.php");        //数据类
require_once(CORE."lib/smarty.class.php");       //模版类
require_once(CORE."lib/json.class.php");		 //JSON类
require_once(CORE."lib/func.class.php");         //核心类

require_once(CORE.'pay/alipay/alipay.config.php');
require_once(CORE.'pay/alipay/lib/alipay_notify.class.php');


//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if ($verify_result) {
	$out_trade_no = $_REQUEST['out_trade_no']; //商户订单号
	$trade_no = $_REQUEST['trade_no'];         //支付宝交易号
	$trade_status = $_REQUEST['trade_status']; //交易状态
	
	$sql="UPDATE $cfg[dbpre]pay_log SET `mark`='$trade_status',`status`=1 WHERE `status`=0 AND order_id='" . $out_trade_no . "'";
	if ($db->query($sql)) {
		echo 'success';
	}
}
else {
    //验证失败
    echo "fail";
}
?>