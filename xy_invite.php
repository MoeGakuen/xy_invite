<?php 
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

function xy_invite_navi() { 
	echo '<li ';
	if(isset($_GET['plug']) && $_GET['plug'] == 'xy_invite') { echo 'class="active"'; }
	echo '><a href="index.php?mod=admin:setplug&plug=xy_invite"><span class="glyphicon glyphicon-tag"></span> 邀请码设置</a></li>';
}

function xy_invite_set() {
	if(isset($_GET['mod']) && $_GET['mod'] == "admin:set")	echo '<script type="text/javascript">$("#yr_reg").attr("disabled",true);</script>';
}

function xy_invite_verify() {
	global $m;
	if (option::get('enable_reg') != '1') {msg('注册失败：该站点已关闭注册');}
	$name = isset($_POST['user']) ? sqladds($_POST['user']) : '';
	$mail = isset($_POST['mail']) ? sqladds($_POST['mail']) : '';
	$pw = isset($_POST['pw']) ? sqladds($_POST['pw']) : '';
	$yr = isset($_POST['yr']) ? sqladds($_POST['yr']) : '';
	if (empty($name) || empty($mail) || empty($pw)) {msg('注册失败：请正确填写账户、密码或邮箱');}
    if ($_POST['pw'] != $_POST['rpw']) {msg('注册失败：两次输入的密码不一致，请重新输入');}
	if (!checkMail($mail)) {msg('注册失败：邮箱格式不正确');}
	$x = $m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users` WHERE `name` = '{$name}' OR `email` = '{$mail}' LIMIT 1");
	if ($x['total'] > 0) {msg('注册失败：用户名或邮箱已经被注册');}

	$yr_reg = option::get('yr_reg');
	if (!empty($yr_reg)) {
		if (empty($yr)) {
			msg('注册失败：请输入邀请码');
		} else {
			$z = $m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."xy_invite`");
			if ($z['total'] <= 0) {
				msg('系统错误：邀请码不足，请联系管理员添加！');
			} else {
				$s = $m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."xy_invite` WHERE `code`='{$yr}'");
				if ($s->num_rows <= 0) {
					msg('注册失败：邀请码错误！');
				} else {
					$r = $s->fetch_array();
					$r_num = (int)$r['num'];
					if($r_num == 1){
						$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."xy_invite` WHERE `id` = ".$r['id']);
					} else {
						if($r_num > 1){
							$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX."xy_invite` SET `num`=num-1 WHERE `id`='".$r['id']."';");
						}
					}
				}
			}
		}
	}

	$y = $m->once_fetch_array("SELECT COUNT(*) AS total FROM `".DB_NAME."`.`".DB_PREFIX."users`");
	if ($y['total'] <= 0) {
		$role = 'admin';
	} else {
		$role = 'user';
	}
	doAction('admin_reg_2');
	$m->query('INSERT INTO `'.DB_NAME.'`.`'.DB_PREFIX.'users` (`id`, `name`, `pw`, `email`, `role`, `t`) VALUES (NULL, \''.$name.'\', \''.EncodePwd($pw).'\', \''.$mail.'\', \''.$role.'\', \''.getfreetable().'\');');
	doAction('admin_reg_3');
	ReDirect('index.php?mod=login&msg=' . urlencode('成功注册，请输入账号信息登录本站 [ 账号为用户名或邮箱地址 ]'));die;
}

addAction('admin_reg_1','xy_invite_verify');
addAction('footer','xy_invite_set');
addAction('navi_2','xy_invite_navi');
addAction('navi_8','xy_invite_navi');
?>