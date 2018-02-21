<?php 
session_start(); 

$sessionvar = 'vcode'.$_REQUEST["code_id"]; //Session变量名称 
$width = 126; //图像宽度 
$height = 50; //图像高度 

$operator = '+-*'; //运算符 

$code = array(); 
$code[] = mt_rand(1,9); 
$code[] = $operator{mt_rand(0,2)}; 
$code[] = mt_rand(1,9); 
$code[] = $operator{mt_rand(0,2)}; 
$code[] = mt_rand(1,9); 
$codestr = implode('',$code); 
eval("\$result = ".implode('',$code).";"); 
$code[] = '='; 

$_SESSION[$sessionvar] = $result; 

$img = ImageCreate($width,$height); 
ImageColorAllocate($img, mt_rand(230,250), mt_rand(230,250), mt_rand(230,250)); 
$color = ImageColorAllocate($img, 0, 0, 0); 

$offset = 0; 
foreach ($code as $char) { 
$offset += 15; 
$txtcolor = ImageColorAllocate($img, mt_rand(0,255), mt_rand(0,150), mt_rand(0,255)); 
ImageChar($img, 5, $offset, 15, $char, $txtcolor); 
} 

for ($i=0; $i<600; $i++) { 
$pxcolor = ImageColorAllocate($img, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)); 
ImageSetPixel($img, mt_rand(0,$width), mt_rand(0,$height), $pxcolor); 
} 

header('Content-type: image/png'); 
ImagePng($img); 
?>
