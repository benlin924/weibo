<?php
return array(
	//'配置项'=>'配置值'
	'DB_HOST'=>'localhost', //上线时填写线上的服务器地址
	'DB_USER'=>'root', //买数据库时他会给你的用户名
	'DB_PWD'=>'', //数据库密码
	'DB_NAME'=> 'weibo',
	'DB_PORT'   => '3306', // 端口
	'DB_PREFIX'=> 'hd_', //数据库表前缀
	'DB_TYPE'=>'mysql', //数据库类型
	//'DEFAULT_THEME'=> 'default', //默认主题模板

	'TOKEN_ON'   =>  true, //是否开启令牌验证
	'TOKEN_NAME'  =>  'token',// 令牌验证的表单隐藏字段名称
	'TOKEN_TYPE'  =>  'md5',//令牌验证哈希规则

	'URL_MODEL'   =>  1,  //url的模式
	//'URL_HTML_SUFFIX'   =>  '',  // URL伪静态后缀设置

	//用于异位或加密的KEY值
	'ENCTYPTION_KEY'=>'xiaobin',
	//自动登录的保存时间
	'AUTO_LOGIN_TIME' => time()+3600*24*7,
);