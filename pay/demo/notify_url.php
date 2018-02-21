<?php  
 include_once("log_.php");
 include_once("../WxPayPubHelper/WxPayPubHelper.php");
	//$log_ = new Log_();
	//  echo "log";
	//$log_name = "notify_url.log"; //log文件路径

	//$log_->log_result($log_name,"【支付成功】:\n".$xml."\n"); 
    //使用通用通知接口
	$notify = new Notify_pub();

	//存储微信的回调
	$xml = $GLOBALS['HTTP_RAW_POST_DATA'];	
	$notify->saveData($xml); 

	//验证签名，并回应微信。
	//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
	//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
	//尽可能提高通知的成功率，但微信不保证通知最终能成功。
	if($notify->checkSign() == FALSE){
		$notify->setReturnParameter("return_code","FAIL");//返回状态码
		$notify->setReturnParameter("return_msg","签名失败");//返回信息
	}else{
		$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
	}
	$returnXml = $notify->returnXml(); 
	echo $returnXml;
	//==商户根据实际情况设置相应的处理流程，此处仅作举例=======
	 
	if($notify->checkSign() == TRUE){
		if ($notify->data["return_code"] == "FAIL" || $notify->data["result_code"] == "FAIL"){
			//此处应该更新一下订单状态，商户自行增删操作
		} else{ 
			//商户订单号
			$out_trade_no = $notify->data["out_trade_no"];  
			$transaction_id = $notify->data["transaction_id"];			
			//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序  
		    
			 
		  } 
	}
?>


