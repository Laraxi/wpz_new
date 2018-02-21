<?php
define('ROOT_PATH', str_replace("\\", '/', substr(__FILE__, 0, strrpos(dirname(dirname(dirname(__FILE__))), DIRECTORY_SEPARATOR))).'/');
define('CORE_PATH', ROOT_PATH.'source/');

require(CORE_PATH.'init.php');
require(CORE_PATH.'vendor/alipay/alipay.config.php');
require(CORE_PATH.'vendor/alipay/lib/alipay_submit.class.php');
require(CORE_PATH.'vendor/alipay/lib/alipay_notify.class.php');


//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if ($verify_result) {
	$out_trade_no = $_GET['out_trade_no']; //商户订单号
	$trade_no = $_GET['trade_no']; //支付宝交易号
	$trade_status = $_GET['trade_status']; //交易状态
	
	//查询并同步订单状态
	$table = $DB->table('consumes');
	$row = $DB->fetch_one("SELECT cons_id, user_id, cons_money, cons_status FROM $table WHERE order_id='$out_trade_no' LIMIT 1");
	if ($row) { 
		if ($row['cons_status'] != 4) {
			 $where = array('cons_id' => $row['cons_id']);  
			 $score = round($money/100) * round($options['exchange_score'] / $options['exchange_money']);
			 $DB->update($table, array('trade_status' => $trade_status, 'cons_score' => $score,'trade_no'=>$trade_no, 'cons_status' => 4, 'cons_time' => time()), $where);
			 $DB->query("UPDATE ".$DB->table('users')." SET user_score=user_score+".$score." WHERE user_id='".$row['user_id']."' LIMIT 1");	 
		 }
		 
		alert('支付成功！', $options['site_url'].'member/?mod=consume');
	}
} else {
    alert('支付失败，请联系网站管理员！', $options['site_url'].'member/?mod=consume');
}
?>