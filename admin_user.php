<?php
require_once ('includes/init.php');
require_once ('includes/wechat.php');
admin_priv();

$action=isset($_GET['action'])?$_GET['action']:"";
$wechatObj = new wechatCallbackapiTest();
$templates = 'templates/';
//
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perpage = 15;
$start = ($page -1) * $perpage;
$table = DBFIX."wx_user";
$smarty->assign('ver',VER);

switch ($action){
	case "update"://更新《微信》上的数据
		$userlist = array();
		$groups = $wechatObj->GetGroups();
		$user = $wechatObj->GetUserList();
		//var_dump($user);exit();
		$userid = $user['data']['openid'];
		foreach($userid as $id){
			$uid = $db->getOne("select id from cg_wx_user where openid='$id'");
			if(!$uid){
				$cache = $wechatObj->GetUserDetail($id);
				$userlist[] = $cache;
				$id = $db->inserttable($table, $cache, 0);
			}
		}
		
		$totalnum=$user['total'];//总数
		$pagecount=ceil($totalnum/$perpage);
		vheader($filename);
	break;
	case "info":
	
	break;
	default://默认从库表中取值
		$sqladd = ' where 1=1';
		
		if (!empty ($_GET['keyword'])) { //keywords
			$sqladd .= " and nickname like '%" . $_GET['keyword'] . "%'";
		}
		$sqlfrom = " from " . $table . $sqladd;
		$totalnum = $db->result($db->query("select count(*) " . $sqlfrom), 0); //总数;
		$query = $db->query("select * " . $sqlfrom . " order by subscribe_time desc limit $start,$perpage");
		#echo("select * " . $sqlfrom . " order by addtime desc limit $start,$perpage");
		$data = $comments = array ();
		while ($data = $db->fetch_array($query)) {
			$data['time'] = date('Y-m-d H:i:s', $data['subscribe_time']);
			$comments[] = $data;
		}
		$multipage = multi($totalnum, $perpage, $page, '');
		$smarty->assign('multipage', $multipage);
		$smarty->assign('comments', $comments);
		$smarty->assign('totalnum', $totalnum);
		$smarty->assign('page', $page);
		$smarty->display('./'.$templates.'admin_user.html');
	break;
}
?>