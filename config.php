<?php 


return [
	// 文本消息xml
	'text' =>"<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			</xml>",

		'image_text' =>"<xml>
				  <ToUserName><![CDATA[%s]]></ToUserName>
				  <FromUserName><![CDATA[%s]]></FromUserName>
				  <CreateTime>%s</CreateTime>
				  <MsgType><![CDATA[news]]></MsgType>
				  <ArticleCount>1</ArticleCount>
				  <Articles>
				    <item>
				      <Title><![CDATA[%s]]></Title>
				      <Description><![CDATA[%s]]></Description>
				      <PicUrl><![CDATA[%s]]></PicUrl>
				      <Url><![CDATA[%s]]></Url>
				    </item>
				  </Articles>
				</xml>"


];