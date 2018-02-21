<?php
     include_once("../../../system/common.php"); 

     if($_REQUEST['order_id'] && $_REQUEST['out_trade_no']){
		$order_id = intval($_REQUEST['order_id']); 
		$payment_notice_sn = trim($_REQUEST['out_trade_no']);
       
		$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		if($order['type'] == 1){	//会员充值
			$title_name = "会员微信充值";
		}else{
			$sql = "select name from ".DB_PREFIX."deal_order_item where order_id =". intval($order_id);
			$title_name = $GLOBALS['db']->getOne($sql);
		} 
		
		if ($order_id == 0){
			$payment_notice_sn = $GLOBALS['request']['out_trade_no'];
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
			$order_id = intval($payment_notice['order_id']);
		} 
		
		if($payment_notice_sn)
		{
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
		}
	 
		if (empty($order)){
			echo "订单不存在";
			exit();
		}
	
		if ($order['pay_status'] == 2){
			//echo "订单已经收款";
			$_url = "http://www.365um.com/wap/index.php?ctl=pay_order&order_id=".$order['id'];
			Header("Location: $_url");
			exit();
		}
		
		$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id=".intval($order['payment_id']));
		$pay_code = strtolower($payment_info['class_name']);
		
		if($pay_code !="wxpay"){
			echo "不支持的支付方式";
			exit();
		} 
	}
/**
 * JS_API支付demo
 * ====================================================
 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
 * 成功调起支付需要三个步骤：
 * 步骤1：网页授权获取用户openid
 * 步骤2：使用统一支付接口，获取prepay_id
 * 步骤3：使用jsapi调起支付
 */
	include_once("../WxPayPubHelper/WxPayPubHelper.php");
	//使用jsapi接口
	$jsApi = new JsApi_pub(); 
	//=========步骤1：网页授权获取用户openid============
	//通过code获得openid
	if (!isset($_GET['code'])){
		//触发微信返回code码
		$url = $jsApi->createOauthUrlForCode(WxPayConf_pub::JS_API_CALL_URL);
		$state = json_encode(array(
				"body" => $title_name,
				"out_trade_no" => $payment_notice_sn,
				"total_fee" => $payment_notice['money'],
		));  
		$url = str_replace("STATE", $state, $url);
		Header("Location: $url");  
	}else{
		//获取code码，以获取openid
		$code = $_GET['code'];
		$jsApi->setCode($code);
		$weixin_openid = $jsApi->getOpenId();
		$state = json_decode($_GET['state'],TRUE);  
	}
	
	//=========步骤2：使用统一支付接口，获取prepay_id============
	//使用统一支付接口
	$unifiedOrder = new UnifiedOrder_pub();
	
	//设置统一支付接口参数
	//设置必填参数
	//appid已填,商户无需重复填写
	//mch_id已填,商户无需重复填写
	//noncestr已填,商户无需重复填写
	//spbill_create_ip已填,商户无需重复填写
	//sign已填,商户无需重复填写
	$unifiedOrder->setParameter("openid","$weixin_openid");//商品描述
	$unifiedOrder->setParameter("body",$state['body']);//商品描述
	//自定义订单号，此处仅作举例
	$timeStamp = time();
	$out_trade_no = WxPayConf_pub::APPID."$timeStamp";
	$unifiedOrder->setParameter("out_trade_no","".$state['out_trade_no']);//商户订单号 
	$unifiedOrder->setParameter("total_fee","".intval(floatval($state['total_fee'])*100));//总金额
	$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
	$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
	  
	//非必填参数，商户可根据实际情况选填
	//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
	//$unifiedOrder->setParameter("device_info","XXXX");//设备号 
	//$unifiedOrder->setParameter("attach","XXXX");//附加数据 
	//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
	//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
	//$unifiedOrder->setParameter("openid","XXXX");//用户标识
	//$unifiedOrder->setParameter("product_id","XXXX");//商品ID 
	$prepay_id = $unifiedOrder->getPrepayId();
	//=========步骤3：使用jsapi调起支付============
	$jsApi->setPrepayId($prepay_id); 
	$jsApiParameters = $jsApi->getParameters();
	//echo $jsApiParameters; 
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <title>微信安全支付</title> 
	<script type="text/javascript"> 
		//调用微信JS api 支付
		function jsApiCall(){ 
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				<?php echo $jsApiParameters; ?>,
				function(res){
					//WeixinJSBridge.log(res.err_msg);
					if(res.err_msg == "get_brand_wcpay_request:ok"){
						alert("微信支付成功!");
						history.back(); 
					}else{
						alert("微信支付失败!");
					}
				}
			);
		}

		function callpay(){ 
			if (typeof WeixinJSBridge == "undefined"){
			    if( document.addEventListener ){
			        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			    }else if (document.attachEvent){
			        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
			        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			    }
			}else{
			    jsApiCall();
			}
		}

		callpay();
	</script>
</head>
</html>