<?php

//主动微信公众请求类


$wx = new Wechat;

// echo $wx-> getAccessToken();

// if($wx->createMeun()){	
// 		echo '成功';
// 	}else{
// 		echo '失败';
// 	}

class Wechat{

	const APPID = 'wx38d9ed140d3dd39f';
	const SECRET ='fca5f2867b166cf1df6425ff2d5432ab';

	//接口数组
	private $config = [];

	//构造方法 初始化
	public function __construct(){
		$this->config = include 'conf.php';
	}


	//创建自定义菜单
	public function createMeun(){
		//
		$url = sprintf($this->config['create_menu_url'],$this->getAccessToken());
		//
		$json = '{
				     "button":[
				     {    
				          "type":"click",
				          "name":"一级菜单",
				          "key":"click001"
				      },
				      {
				           "name":"二级菜单",
				           "sub_button":[
				           {    
				               "type":"view",
				               "name":"笑话",
				               "url":"click1"
				            },
				            {
				               "type":"click",
				               "name":"也是笑话",
				               "key":"click2"
				            }]
				       },
				       {    
				          "type": "pic_sysphoto",
	                      "name": "还是笑话",
	                      "key": "pic001"
				      }]
				 }';
				
				//
				$errcode = json_decode($this->http_request($url,$json),true)['errcode'];

				return $errcode == 0 ?true:false;

	}
	//素材上传功能
	public function upFile(string $type,string $filepath='',int $is_forever = 0){
		if (0 == $is_forever) { // 临时素材上传
			$url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=%s&type=%s';
		}else{ // 永久
			$url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=%s&type=%s';
		}
		// 格式化url字符中
		$url = sprintf($url,$this->getAccessToken(),$type);
		// 发起POST请求
		$json = $this->http_request($url,[],$filepath);
		// 把JSON转为数组
		$arr = json_decode($json,true);
		return $arr['media_id'];
	}

	//获取token
	private function getAccessToken(){

		//判断缓存中是否有access_token
		//有就度缓存，没有就写入缓存
		if(false != ($access_token =$this->memcache()->get(self::APPID))){
			return $access_token;
		}

		//url 的地址
		$url = sprintf($this->config['access_token_url'],self::APPID,self::SECRET);
		//get请求
		$arr = json_decode($this->http_request($url),true);
		//写入到缓存中
		$this->memcache()->set(self::APPID,$arr['access_token'],0,3600);
		//返回access_token
		return $arr['access_tkoen'];
	}
	
	private function memcache(){
		//
		$memcache = new Memcache();
		$memcache->addServer('127.0.0.1',11211);
		return $memcache;
	}

	//curl请求类
	private function http_request(string $url,$data = '',string $filepath = ''){
		// $filepath不为空表示有文件上传
		if(!empty($filepath)){
			$data['media'] = new CURLFile($filepath);
		}
		// 1、初始化 相当于打开了浏览器
		$ch = curl_init();
		// 2、相关的设置
		// 请求的URL地址设置
		curl_setopt($ch,CURLOPT_URL,$url);
		// 设置输出的信息不直接输出
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		// 取消https证书验证
		$res =curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		// echo $res;exit;
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		// 设置请求的超时时间 单是秒
		curl_setopt($ch,CURLOPT_TIMEOUT,10);
		// 模拟一个浏览器型号
		curl_setopt($ch,CURLOPT_USERAGENT,'MSIE');

		// 表示有数据上传
		if (!empty($data)) {
			// 如果是一个字符串，表示是一个json
			if (is_string($data)) {
				// 如果json类型加一个头信息说明   // 设置头信息
				curl_setopt($ch,CURLOPT_HTTPHEADER,[
					'Content-Type: application/json;charset=utf-8'
				]);
			}
			// 告诉curl你使用了post请求
			curl_setopt($ch,CURLOPT_POST,1);
			// post的数据
			curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		}
		// 3、执行请求操作
		$data = curl_exec($ch);
		// 得到请求的错误码  0返回请求成功，大于0表示请求有异常
		$errno = curl_errno($ch);
		//  echo $errno;exit;  //开启php_curl
		if ( 0 < $errno ) {
			// 抛出自己的异常
			throw new Exception(curl_error($ch), 1000);
		}
		// 4、关闭资源
		curl_close($ch);

		// 返回数据
		return $data;
	}
	
}