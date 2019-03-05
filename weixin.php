
<?php

//初次介入验证
 function checkSignature()
{
	//公众号传过来的数据
   $sign = $_GET["signature"];
   $time = $_GET["timestamp"];
   $nonce = $_GET["nonce"];
   $echostr = $_GET["echostr"];

	$tmpA['token'] ='weixin';   
	$tmpA['timestamp'] = $time;   
	$tmpA['nonce'] = $nonce;   

//字典排序
sort($tmpA, SORT_STRING);
//拼接成字符串
$tmpStr = implode( $tmpA );
//加密sha1
$tmpStr = sha1( $tmpStr );

//通过验证
if( $sign==$tmpStr ){
	return $echostr;
	}else{
		return '';
		}
	}

	