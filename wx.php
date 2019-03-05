<?php
/**
 * 被动公众平台接口
 */


$wx = new Wx();


class Wx {

	// 和公众平台约定好的token值
	private const TOKEN = 'weixin';

	private $obj;

	// 消息的xml
	private $config = [];


	public function __construct(){
		// 判断参数就执行验证
		if (isset($_GET["echostr"])) {  
			echo $this->checkSignature();
		}else{
			$this->config = include 'config.php';
			// 接收消息方法和处理
			echo $this->acceptMesage();			
		}
	}


	/**
	 * 接受消息处理
	 */
	private function acceptMesage(){
		// 获取公众平台发送过来的xml数据
		$xml = file_get_contents('php://input');
		// 写接受日志
		$this->writeLog($xml);

		// 把xml转为object对象
		$this->obj = simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
		
		// 消息类型
		$type = $this->obj->MsgType;

		// 动态方法 
		$funName = $type.'Fun';

		return $this->$funName();
		
	}

	    /*
     * 响应文本(图文)
     * */
    private function textFun(){
        $content = (string)$this->obj->Content;

        if (stristr($content, '图文')) {
        	return $this->createtuwen();
        }
        //接收文本
        return $this->createText($content);
    }

	 //生成文本消息的xml
	private function createText(string $content){
		return sprintf($this->config['text'],$this->obj->FromUserName,$this->obj->ToUserName,time(),"服务器：".$content);
	}

    //生成图文消息的xml
	private function createtuwen(){
	return sprintf($this->config['image_text'],$this->obj->FromUserName,$this->obj->ToUserName,time(),'呵呵','哈哈哈哈哈哈','https://ss0.bdstatic.com/94oJfD_bAAcT8t7mm9GUKT-xh_/timg?image&quality=100&size=b4000_4000&sec=1551614574&di=ef1199e5fb539cc063c582b6b0718553&src=http://i.shangc.net/2017/0803/20170803020240557.jpg','http://www.baidu.com');
	}



	 /*
    写日志
     * */
    private function writeLog($xml,$flag = 0){
        $title = $flag == 0 ? '接收' : '发送';
        $date = date('y-m-d h:i:s');
        $log = $title.'['.$date."]\n";
        $log .= "---------------------------------\n";
        $log .= $xml."\n";
        $log .= "---------------------------------\n";
        //写日志
        file_put_contents('wx.xml',$log,FILE_APPEND);
    }


	 //初次接入验证
	 
	private function checkSignature(){
		# 公众平台传过来的数据
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];
		$echostr = $_GET["echostr"];

		$tmpArr['token'] = self::TOKEN;
		$tmpArr['timestamp'] = $timestamp;
		$tmpArr['nonce'] = $nonce;
		# 进行字典
		sort($tmpArr, SORT_STRING);
		# 拼接成字符串
		$tmpStr = implode( $tmpArr );
		# 进行sha1加密
		$tmpStr = sha1( $tmpStr );

		# 验证通过
		if( $tmpStr == $signature ){
			return $echostr;
		}

		# 验证不通过
		return '';
	}
}





