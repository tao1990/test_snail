<?php
/**
 * AOP SDK 入口文件
 * 请不要修改这个文件，除非你知道怎样修改以及怎样恢复
 * @author mengyu.wh
 */
// 载入sdk的client
require_once dirname(__FILE__).'/aop/AopClient.php';

// 按需加载对应request
spl_autoload_register(function($class){
	if (strpos($class,'Request')) {
		require_once dirname(__FILE__).'/aop/request/'.$class.'.php';
	}else{
		throw new Exception("无法自动加载类「".$class."」 SDK AopSdk只能自动加载aop下的Request文件，其他文件请您自主载入");
	}
});