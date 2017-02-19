<?php
require_once('includes/init.php');
require_once('includes/wechat.php');
admin_priv();
$action=isset($_GET['action'])?$_GET['action']:"";
$page =isset($_GET['page'])?(int)$_GET['page']:1;
$templates = 'templates/';

//
$perpage = 15;
$page = empty ($_GET['page']) ? 1 : intval($_GET['page']);
$start = ($page -1) * $perpage;

$tit = "消息";
$table = DBFIX."wx_msg";

$smarty->assign('tit', $tit);
$smarty->assign('action', $action);


switch ($action) {
	case "handle" :
		$id = $_GET['id'];
		$do = 0;
		unset ($_GET['action']);
		unset ($_GET['page']);
		unset ($_GET['types']);
		if (!empty ($id)) {
			$db->updatetable($table, $_GET, array (
				'id' => $id
			));
		}
		vheader("?types=$types&page=$page");
		break;
	case "rep":
		$openid = isset($_GET['openid'])?trim($_GET['openid']):"";
		if(!empty($openid)){
			$sql = 	"select * from $table m LEFT JOIN cg_wx_user u on u.openid=m.FromUserName where m.ToUserName='".$openid."' or m.FromUserName='".$openid."' order by m.CreateTime desc";	
			$query = $db->query($sql);
			
			$data = $comments = array ();
			while ($data = $db->fetch_array($query)) {
				$data['time'] = date('Y-m-d H:i:s', $data['CreateTime']);
				$comments[] = $data;
			}
			$smarty->assign('comments', $comments);
			$smarty->assign('openid', $openid);
			$smarty->display('./'.$templates.'admin_msg.html');
		}else{
			vheader("?page=$page");
		}
	break;
	case "view":
		if (!empty ($_GET['id'])) {
			$id = $_GET['id'];
		} else {
			$id = 0;
		}
		$sqlstr = "select * from $table where id=" . $id;
		$info = $db->fetch_array($db->query($sqlstr));
		$smarty->assign('info', $info);
		$smarty->display('./'.$templates.'allow.html');
		break;
	default:
		$sqladd = " where m.ToUserName='gh_4090246579e3'";
		
		if (!empty ($_GET['keyword'])) { //keywords
			$sqladd .= " and m.Content like '%" . $_GET['keyword'] . "%'";
		}
		$sqlfrom = " from " . $table . " m LEFT JOIN cg_wx_user u on u.openid=m.FromUserName" . $sqladd;
		$totalnum = $db->result($db->query("select count(*) " . $sqlfrom), 0); //总数;
		$query = $db->query("select * " . $sqlfrom . " order by m.CreateTime desc limit $start,$perpage");
		
		$data = $comments = array ();
		while ($data = $db->fetch_array($query)) {
			$data['time'] = date('Y-m-d H:i:s', $data['CreateTime']);
			$comments[] = $data;
		}
		$multipage = multi($totalnum, $perpage, $page, '');
		$smarty->assign('multipage', $multipage);
		$smarty->assign('comments', $comments);
		$smarty->assign('totalnum', $totalnum);
		$smarty->assign('page', $page);
		$smarty->display('./'.$templates.'admin_msg.html');
		break;
}
?>