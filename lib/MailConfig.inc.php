<?php
include_once "MailClass.php";
$smtpserver = $cfg['email_host']; //您的smtp服务器的地址
$port = $cfg['email_port']; //smtp服务器的端口，一般是 25 
$smtpuser = $cfg['email_username']; //您登录smtp服务器的用户名
$smtppwd = $cfg['email_password']; //您登录smtp服务器的密码

$mail = new MySendMail();
//$mail->setServer("smtp@126.com", "XXXXX@126.com", "XXXXX"); //设置smtp服务器，普通连接方式
$mail->setServer($smtpserver, $smtpuser, $smtppwd, $port, true); //设置smtp服务器，到服务器的SSL连接


?>
