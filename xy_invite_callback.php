<?php
function callback_install() {
	global $m;
	$m->query('CREATE TABLE IF NOT EXISTS `'.DB_NAME.'`.`'.DB_PREFIX.'xy_invite` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`code` varchar(100) DEFAULT NULL,
	`num` int(5) DEFAULT NULL,
	PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
	option::set('xy_invite_gs_yqm','学园-{年}-{随机[19]}');
	$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."plugins` SET `order`='50' WHERE `name`='xy_invite';");//兼容某些插件，放后加载。
}

function callback_remove() {
	global $m;
	option::set('yr_reg','');
	$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."options` WHERE `name` LIKE '%xy_invite_%'");
	$m->query('DROP TABLE `'.DB_NAME.'`.`'.DB_PREFIX.'xy_invite`');
}

function callback_init() {
	option::set('yr_reg','多邀请码已开启');
}

function callback_inactive() {
	option::set('yr_reg','');
}
