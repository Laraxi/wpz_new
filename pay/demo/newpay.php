<?php 
 if (!defined('IN_MEMBER')) exit('Access Denied');
include_once(CORE_PATH."php/WxPayPubHelper/WxPayPubHelper.php");
 
 $out_trade_no =$_POST['orderid'];
 $user_id=$_POST['user_id'];
 
 //使用统一支付接口
	  $unifiedOrder = new UnifiedOrder_pub();
		$subject = $_POST['subject'];
		$price=intval($_POST['price']);
	//设置统一支付接口参数
	//设置必填参数
	//appid已填,商户无需重复填写
	//mch_id已填,商户无需重复填写
	//noncestr已填,商户无需重复填写
	//spbill_create_ip已填,商户无需重复填写
	//sign已填,商户无需重复填写
	  $unifiedOrder->setParameter("body","积分充值");//商品描述
	//自定义订单号，此处仅作举例
	//$timeStamp = time();
	//echo $_POST['price'];
	 $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 

	 $unifiedOrder->setParameter("total_fee",$price);//总金额$price*100
	 
	 $unifiedOrder->setParameter("notify_url",'http://www.bilinks.cn/member/?mod=nnn');//通知地址 
	 	 
	 $unifiedOrder->setParameter("trade_type","NATIVE");//交易类型
	//非必填参数，商户可根据实际情况选填
	//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
	//$unifiedOrder->setParameter("device_info","XXXX");//设备号 
	//$unifiedOrder->setParameter("attach","XXXX");//附加数据 
	//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
	//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
	//$unifiedOrder->setParameter("openid","XXXX");//用户标识
	//$unifiedOrder->setParameter("product_id","XXXX");//商品ID
	 print_r($unifiedOrder);
	//获取统一支付接口结果
	  $unifiedOrderResult = $unifiedOrder->getResult();
	  print_r($unifiedOrderResult);
	//商户根据实际情况设置相应的处理流程
  if ($unifiedOrderResult["return_code"] == "FAIL") 
	  {
		//商户自行增加处理流程
 	echo "通信出错：".$unifiedOrderResult['return_msg']."<br>";
	  }
  elseif($unifiedOrderResult["result_code"] == "FAIL")
	  {
		//商户自行增加处理流程
		  echo "错误代码：".$unifiedOrderResult['err_code']."<br>";
		//echo "错误代码描述：".$unifiedOrderResult['err_code_des']."<br>";
	  }
  elseif($unifiedOrderResult["code_url"] != NULL)
	  {
		//从统一支付接口获取到code_url
 	 $code_url = $unifiedOrderResult["code_url"];
		//商户自行增加处理流程
		//......
		
				  //产生订单
$row = $DB->fetch_one("SELECT cons_id FROM ".$DB->table('consumes')." WHERE order_id='$out_trade_no' LIMIT 1");
if (!$row) {
	$data = array('user_id' => $user_id, 'order_id' => $out_trade_no, 'cons_name' => $subject, 'cons_type' => 1, 'cons_money' => $price, 'cons_status' => 0, 'cons_time' => time());
	$DB->insert($DB->table('consumes'), $data);
}
	  }else{
		  
		  

	  }

?>

 
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title>微信安全支付</title>
</head>
<body>
	<div align="center" id="qrcode">
	</div>
		<!--<div align="center">
		<p>订单号：<?php echo $out_trade_no; ?></p>
	</div>
<div align="center">
		<form  action="./order_query.php" method="post">
			<input name="out_trade_no" type='hidden' value="<?php echo $out_trade_no; ?>">
		    <button type="submit" >查询订单状态</button>
		</form>
	</div>
	<br>
	<div align="center">
		<form  action="./refund.php" method="post">
			<input name="out_trade_no" type='hidden' value="<?php echo $out_trade_no; ?>">
			<input name="refund_fee" type='hidden' value="1">
		    <button type="submit" >申请退款</button>
		</form>
	</div>
	<br>
	<div align="center">
		<a href="../index.php">返回首页</a>
	</div>-->
	 <input type="hidden" name="out_trade_no" id="out_trade_no" value="<?php echo $out_trade_no;?>" />
</body>
	<script src="http://www.bilinks.cn/source/php/demo/qrcode.js"></script>
	<script type="text/javascript" src="http://www.bilinks.cn/public/scripts/jquery.min.js"></script>
	<script>
		if(<?php echo $unifiedOrderResult["code_url"] != NULL; ?>)
		{
			var url = "<?php echo $code_url;?>";
			//参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
			var qr = qrcode(10, 'M');
			qr.addData(url);
			qr.make();
			var wording=document.createElement('p');
			wording.innerHTML = "扫我付款";
			var code=document.createElement('DIV');
			code.innerHTML = qr.createImgTag();
			var element=document.getElementById("qrcode");
			element.appendChild(wording);
			element.appendChild(code);
		}
		
		
		
		
		
		 $(function(){
           setInterval(function(){check()}, 5000);  //5秒查询一次支付是否成功
        })
        function check(){
            var url = "http://www.bilinks.cn/source/php/demo/order_query.php";
		  
            var out_trade_no = $("#out_trade_no").val();
            var param = {'out_trade_no':out_trade_no};
            $.post(url, param, function(data){
					console.info(data); 
                if(data=="success"){ 
                    alert("订单支付成功,即将跳转...");
                    window.location.href = "http://www.bilinks.cn";
                }else{
                    console.log(data);
                }
            });
        }
	</script>
</html> 