<?php 


return [
	
	//获取access_token的地址
	'access_token_url' => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',
	
	//自定义菜单的创建的地址
	'create_menu_url' => ' https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s',

];


