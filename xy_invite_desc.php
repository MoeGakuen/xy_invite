<?php if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
return array(
	'plugin' => array(
		'name'        => '自定义多邀请码',
		'version'     => '1.1',
		'description' => '让站点支持多邀请码，邀请码支持自定义参数。',
		'onsale'      =>  false,
		'url'         => 'http://tb.hydd.cc',
		'for'         => '3.8+',
        'forphp'      => '5.3'
	),
	'author' => array(
		'author'      => '学园',
		'email'       => 'i@hydd.cc',
		'url'         => 'http://www.xybk.cc'
	),
	'view'   => array(
		'setting'     => true,
		'show'        => false,
		'vip'         => false,
		'private'     => false,
		'public'      => false,
		'update'      => false,
	),
	'page'   => array()
);
