<?php
//获取微信回复类型
function get_weixin_reply_type($flag = null) {
	$arr = array(
		'1' => '关注自动回复',
		'2' => '关键词自动回复',
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

//获取微信消息类型
function get_weixin_msg_type($flag = null) {
	$arr = array(
		'text' => '文本消息',
		'image' => '图片消息',
        'voice' => '语音消息',
        'video' => '视频消息',
        'music' => '音乐消息',
        'news' => '图文消息',
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

//获取微信关键词匹配类型
function get_weixin_keyword_type($flag = null) {
	$arr = array(
		'1' => '完全匹配',
		'2' => '包含匹配',
	);
	if (isset($flag)) {
		return $arr[$flag];
	} else {
		return $arr;
	}
}

