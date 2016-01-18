<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 
global $m;

//邀请码参数替换
function getdata_yqm($gs){
	$data = str_ireplace('{年}',date("Y"),$gs);
	$data = str_ireplace('{月}',date("m"),$data);
	$data = str_ireplace('{日}',date("d"),$data);
	$data = str_ireplace('{时}',date("H"),$data);
	$data = str_ireplace('{分}',date("i"),$data);
	$data = str_ireplace('{秒}',date("s"),$data);
	preg_match('/{随机\[([1-9]|1[0-9])\]}/',$data, $re);
	if (!empty($re[1])) $data = str_ireplace($re[0],getRandStr($re[1]),$data);
	return $data; 
}

//剩余次数格式替换
function getdata_sy($gs,$num = 0){
	$gs = !empty($gs) ? $gs : '#剩余{次数}次';
	$num = !empty($num) ? $num : rand(2,100);
	$data = str_ireplace('{次数}',$num,$gs);
	return $data; 
}

//生成邀请码
if (isset($_GET['new'])) {
	$snum = !empty($_POST['snum']) ? $_POST['snum'] : '';
	$ynum = !empty($_POST['ynum']) ? $_POST['ynum'] : 1;
	$gs = option::get('xy_invite_gs_yqm');
	
	if(empty($snum)){$emsg = '生成数量不能为空！';}
	elseif($snum <= 0){$emsg = '生成数量不能小于1！';}
	elseif($snum > 100){$emsg = '生成数量不能超过100！';}
	elseif(empty($ynum)){$emsg = '使用次数不能为空！';}
	elseif($ynum < -1){$emsg = '使用次数不能小于 -1， -1表示无限。';}
	elseif($ynum > 99999){$emsg = '使用次数不能大于99999！';}
	elseif(empty($gs)){$emsg = '没有设置邀请码格式，请先设置邀请码生成格式！';$page=1;}
	elseif(strlen(getdata_yqm($gs)) > 100){$emsg = '邀请码长度超出限制，请重新设置！请控制在100字符内！';$page=1;}
	if(!empty($emsg)) {
		$page = !empty($page) ? $page : 3;
		ReDirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=xy_invite&page='.$page.'&error_msg='.$emsg);
	}
	for($i=0;$i<$snum;$i++){
		$yqm = sqladds(getdata_yqm($gs));
		$sql = "INSERT INTO `".DB_NAME."`.`".DB_PREFIX."xy_invite` (`code`, `num`) VALUES ('{$yqm}', '{$ynum}');";
		$m->query($sql);
	}
	ReDirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=xy_invite&page=2&msg=邀请码生成完毕。');
}
//清空所有邀请码
if (isset($_GET['delete'])) {
	$m->query("truncate table `".DB_NAME."`.`".DB_PREFIX."xy_invite`");
	ReDirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=xy_invite&page=2&msg=邀请码已清空！');
}
//删除单个邀请码
if (isset($_GET['del']) && !empty($_GET['id'])) {
	$id = (int)$_GET['id'];
	$m->query("DELETE FROM `".DB_NAME."`.`".DB_PREFIX."xy_invite` WHERE `id` = {$id}");
	ReDirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=xy_invite&page=2&msg=已删除邀请码 NO.'.$id.'！');
}
//基本设置
if (isset($_GET['set'])) {
	if(!empty($_POST['gs_yqm'])) {
		if(strlen(getdata_yqm($_POST['gs_yqm'])) > 100) {
			ReDirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=xy_invite&error_msg=邀请码长度超出限制！请控制在100字符内！');
		} else {
			$show_num = !empty($_POST['show_num']) ? 1 : 0;
			$hide_num_1 = !empty($_POST['hide_num_1']) ? 1 : 0;
			$hide_num_wx = !empty($_POST['hide_num_wx']) ? 1 : 0;
			option::set('xy_invite_gs_yqm',$_POST['gs_yqm']);
			option::set('xy_invite_gs_sy',$_POST['gs_sy']);
			option::set('xy_invite_shownum',$show_num);
			option::set('xy_invite_hidenum_1',$hide_num_1);
			option::set('xy_invite_hidenum_wx',$hide_num_wx);
			ReDirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=xy_invite&msg=设置已保存！');
		}
	} else {
		ReDirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=xy_invite&error_msg=邀请码格式不能为空！');
	}
}
//多邀请码开启
if (isset($_GET['open'])) {
	option::set('yr_reg','多邀请码已开启');
	ReDirect(SYSTEM_URL.'index.php?mod=admin:setplug&plug=xy_invite&msg=已开启邀请码注册功能！');
}
//错误提示
if (isset($_GET['error_msg'])) {echo '<div class="alert alert-danger alert-dismissable">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>错误：'.strip_tags($_GET['error_msg']).'</div>';
}
//提示
if (isset($_GET['msg'])) {
	echo '<div class="alert alert-info alert-dismissable">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.strip_tags($_GET['msg']).'</div>';
}
//未开启邀请码提示
if (!option::get('yr_reg')) {echo '<div class="alert alert-warning alert-dismissable">警告：没有开启邀请码注册！ <a href="index.php?mod=admin:setplug&plug=xy_invite&open">点击开启</a></div>';}
?>
<!-- NAVI -->
<ul class="nav nav-tabs" id="PageTab">
	<li class="active"><a href="#adminid" id="tab_1" data-toggle="tab" onClick="$('#newid2').css('display','none');$('#newid').css('display','none');$('#adminid').css('display','');">基本设置</a></li>
	<li><a href="#newid" id="tab_2" data-toggle="tab" onClick="$('#newid').css('display','');$('#adminid').css('display','none');$('#newid2').css('display','none');">邀请码管理</a></li>
	<li><a href="#newid2" id="tab_3" data-toggle="tab" onClick="$('#newid2').css('display','');$('#adminid').css('display','none');$('#newid').css('display','none');">邀请码生成</a></li>
</ul><br/>
<!-- END NAVI -->

<!-- PAGE1: ADMINID-->
<div class="tab-pane fade in active" id="adminid">
	<a name="#adminid"></a>
	<form action="index.php?mod=admin:setplug&plug=xy_invite&set" method="post">
		<div class="table-responsive">
			<table class="table table-hover"><tbody>
				<tr><td>
                	<div class="input-group">
						<span class="input-group-addon">邀请码格式</span>
						<input type="text" name="gs_yqm" class="form-control" value="<?php echo option::get('xy_invite_gs_yqm');?>" placeholder="参考格式：学园-{年}-{随机[18]}" required/>
					</div><br/>
					<div class="input-group">
						<span class="input-group-addon">邀请码预览</span>
						<input type="text" class="form-control" value="<?php echo getdata_yqm(option::get('xy_invite_gs_yqm'));?>" placeholder="警告，当前没有设置格式，将会生成失败" disabled/>
					</div>
				</td></tr>
				<tr><td>
                	<div class="input-group">
						<label><input type="checkbox" name="show_num" value="1" <?php echo option::get('xy_invite_shownum') == 1 ? 'checked' : ''; ?>> 批量复制时输出剩余次数</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<label><input type="checkbox" name="hide_num_1" value="1" <?php echo option::get('xy_invite_hidenum_1') == 1 ? 'checked' : ''; ?>> 剩余次数为1时不输出次数</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<label><input type="checkbox" name="hide_num_wx" value="1" <?php echo option::get('xy_invite_hidenum_wx') == 1 ? 'checked' : ''; ?>> 无限次邀请码不输出次数</label>
					</div><br/>
					<div class="input-group">
						<span class="input-group-addon">剩余次数格式</span>
						<input type="text" name="gs_sy" class="form-control" value="<?php echo option::get('xy_invite_gs_sy');?>" placeholder="在此输入剩余格式，留空将默认：#剩余{次数}次"/>
					</div><br/>
					<div class="input-group">
						<span class="input-group-addon">导出格式预览</span>
						<input type="text" class="form-control" value="<?php echo getdata_yqm(option::get('xy_invite_gs_yqm')).getdata_sy(option::get('xy_invite_gs_sy'));?>" placeholder="警告，当前没有设置格式，将会生成失败" disabled />
					</div>
				</td></tr>
			</tbody></table>
		</div>
		<button type="submit" class="btn btn-success">保存</button>
	</form><br/><br/><br/>

	<div class="panel panel-default">
		<div class="panel-heading" onClick="$('#win_bduss').fadeToggle();"><h3 class="panel-title"><span class="glyphicon glyphicon-chevron-down"></span> 格式参数简介</h3></div>
		<div class="panel-body" id="win_bduss">
        	<h3 style="margin-top:0;">邀请码格式参数</h3>
			<p>1、<b>{年}</b> 该参数会被替换成当前日期的年份。</p>
			<p>2、<b>{月}</b> 该参数会被替换成当前日期的月份。</p>
			<p>3、<b>{日}</b> 该参数会被替换成当天日期。</p>
            <p>4、<b>{时}</b> 该参数会被替换成当前时间的小时。</p>
            <p>5、<b>{分}</b> 该参数会被替换成当前时间的分钟。</p>
            <p>6、<b>{秒}</b> 该参数会被替换成当前时间的秒钟。</p>
            <p>7、<b>{随机[n]}</b> 该参数会被替换成随机的字符串；n表示字符串长度，数字范围限制在（1-19），超过范围无效！</p><br/>
            <h3 style="margin-top:0;">剩余次数格式参数</h3>
            <p><b>{次数}</b> 该参数会被替换成当前邀请码剩余次数。</p>
		</div>
	</div>
</div>
<!-- END PAGE1 -->

<!-- PAGE2: NEWID -->
<div class="tab-pane fade" id="newid" style="display:none">
	<a name="#newid"></a>
	<table class="table table-hover">
		<tbody>
			<tr>
				<th>#</th>
				<th>邀请码<?php $s = $m->query("SELECT * FROM `".DB_NAME."`.`".DB_PREFIX."xy_invite`");
				if($s->num_rows > 0){$gs_sy = option::get('xy_invite_gs_sy');echo '&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#copycode">批量复制</button>';}?></th>
				<th>剩余次数</th>
				<th>操作</th>
			</tr>
<?php
	while ($x = $m->fetch_array($s)) {
		$x_num = $x['num'] == -1 ? '无限' : $x['num'];
		if($x_num == 1) {$ys = 'default';}
		elseif ($x_num > 1) {$ys = 'primary';}
		else {$ys = 'success';}

		$data .= '<tr><td>'.$x['id'].'</td><td>'.$x['code'].'</td><td><span class="label label-'.$ys.'">'.$x_num.'</span></td><td><button type="button" onclick="{if(confirm(\'确定要删除这个邀请码吗？一旦删除无法恢复！\')){window.location = \'./index.php?mod=admin:setplug&plug=xy_invite&id='.$x['id'].'&del\';}return false;};" class="btn btn-sm btn-danger">删除</button></td></tr>';
		if (option::get('xy_invite_shownum') == 1) {
			if ($x_num == 1) {
				$sy_num = option::get('xy_invite_hidenum_1') != 1 ? getdata_sy($gs_sy,$x_num) : '';
			} elseif ($x_num == '无限') {
				$sy_num = option::get('xy_invite_hidenum_wx') != 1 ? getdata_sy($gs_sy,$x_num) : '';
			} else {
				$sy_num = getdata_sy($sygs,$x_num);
			}
		}
		$yqm .= $x['code'].$sy_num.PHP_EOL;
	}
	echo $data;
?>
		</tbody>
	</table>
</div>
<!-- END PAGE2 -->

<!-- PAGE3: NEWID2 -->
<div class="tab-pane fade" id="newid2" style="display:none">
	<form action="index.php?mod=admin:setplug&plug=xy_invite&new" method="post">
		<div class="input-group">
			<span class="input-group-addon">生成数量</span>
			<input type="number" name="snum" min="1" max="100" class="form-control" placeholder="请在此输入生成数量，为空将生成失败" value="10" required />
		</div><br>
		<div class="input-group">
			<span class="input-group-addon">有效次数</span>
			<input type="number" name="ynum" min="-1" max="99999" class="form-control" placeholder="邀请码有效次数，留空将默认一次性，-1 为无限。" />
		</div><br>
		<button type="submit" onClick="if(!confirm('确定要生成邀请码吗？')){return false;};" class="btn btn-success">生成邀请码</button>&nbsp;&nbsp;<button type="button" onClick="{if(confirm('确定要清空邀请码吗？一旦清空无法恢复！！')){window.location = './index.php?mod=admin:setplug&plug=xy_invite&delete';}return false;};" class="btn btn-danger">清空所有邀请码</button>
	</form>
</div>
<!-- END PAGE3 -->

<!-- modal -->
<div class="modal fade" id="copycode" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">请手动复制下面的邀请码</h4>
      </div>
      <div class="modal-body">
		<textarea id="invitecode" class="form-control"><?php echo $yqm;?></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
      </div>
    </div>
  </div>
</div>
<script language="javascript">document.getElementById("invitecode").style.height = (document.body.scrollHeight*0.5) + "px";</script>
<!-- END modal -->

<?php //页码判断
if(!empty($_GET['page']) || !empty($page)) {
	$page = !empty($page) ? $page : (int)$_GET['page'];
	if($page == 2) {
		echo '<script language="javascript">document.getElementById("tab_2").click();</script>';
	} elseif($page == 3) {
		echo '<script language="javascript">document.getElementById("tab_3").click();</script>';
	}
}
?>