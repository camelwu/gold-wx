<?php
require_once('includes/init.php');
admin_priv();
$action=isset($_GET['action'])?$_GET['action']:"";
$templates = 'templates/';
$keyword = postget('keyword');
$perpage = 15;
$page = empty ($_GET['page']) ? 1 : intval($_GET['page']);
$start = ($page -1) * $perpage;
$smarty->assign('action', $action);

if("log"==$action) {
	$tit = '管理日志';
	$table = DBFIX."daily";
	$sqladd = ' where 1=1';
	if (!empty ($keyword)) { //keywords
		$sqladd .= " and name like '%" . $keyword . "%'";
	}
	$sqlfrom = " from " . $table . $sqladd;
	$totalnum = $db->result($db->query("select count(*) " . $sqlfrom), 0); //总数;
	$query = $db->query("select * " . $sqlfrom . " order by addtime desc limit $start,$perpage");
	$data = $comments = array ();
	while ($data = $db->fetch_array($query)) {
		$data['system'] = get_os($data['sys']);
		$data['broswer'] = get_broswer($data['sys']);
		$comments[] = $data;
	}
}else{
	$tit = "上传日志";
	$table = DBFIX."attached";
	$sqladd = ' where 1=1';
	if (!empty ($keyword)) { //keywords
		$sqladd .= " and locate ('".$keyword."' , name) > 0";
	}
	$sqlfrom = " from " . $table . $sqladd;
	$totalnum = $db->result($db->query("select count(*) " . $sqlfrom), 0); //总数;
	$query = $db->query("select * " . $sqlfrom . " order by addtime desc limit $start,$perpage");
	$data = $comments = array ();
	while ($data = $db->fetch_array($query)) {
		//$data['system'] = get_os($data['sys']);
		//$data['broswer'] = get_broswer($data['sys']);
		$comments[] = $data;
	}
}
	$multipage = multi($totalnum, $perpage, $page, '?action=' . $action);
	$smarty->assign('multipage', $multipage);
	$smarty->assign('comments', $comments);
	$smarty->assign('totalnum', $totalnum);
	$smarty->assign('tit', $tit);
	$smarty->assign('keyword', $keyword);
	$smarty->display('./'.$templates.'config_log.html');
?>