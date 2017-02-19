<?php
require_once('includes/init.php');
require_once('includes/wechat.php');
admin_priv();
$action=isset($_GET['action'])?$_GET['action']:"";
$id=isset($_GET['id'])?(int)$_GET['id']:0;
$templates = 'templates/';

//
$perpage = 15;
$page = empty ($_GET['page']) ? 1 : intval($_GET['page']);
$start = ($page -1) * $perpage;
$types = array('文字','单图文','多图文');
$ctype = array(1=>'线路',2=>'签证',3=>'景点',5=>'游记');
$tit = "自动回复";
$table = DBFIX."wx_request";

$smarty->assign('tit', $tit);
$smarty->assign('action', $action);
$smarty->assign('id', $id);

switch ($action) {
	case "handle" :
		$id = $_POST['id'];
		$do = 0;
		var_dump($_POST);
		if (!empty ($id)) {
			$db->updatetable($table, $_POST, array (
				'id' => $id
			));
		}else{
			$ids = $db->inserttable($table, $_POST, 0);
		}
		exit("id=".$ids);//vheader("?id=$id&page=$page");
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
		$smarty->display('./'.$templates.'admin_request.html');
		break;
	default:
		$sqladd = ' where 1=1';
		
		if (!empty ($_GET['keyword'])) { //keywords
			if ($sqladd == "") {
				$sqladd .= " and title like '%" . $_GET['keyword'] . "%'";
			} else {
				$sqladd .= " and title like '%" . $_GET['keyword'] . "%'";
			}
		}
		$sqlfrom = " from " . $table . $sqladd;
		$totalnum = $db->result($db->query("select count(*) " . $sqlfrom), 0); //总数;
		$query = $db->query("select * " . $sqlfrom . " order by addtime desc");
		$info = array();
		$data = $comments = array ();
		while ($data = $db->fetch_array($query)) {
			$comments[] = $data;
			$info = $data['id']==$id?$data:array();
		}
		$multipage = multi($totalnum, $perpage, $page, '?action=' . $action);
		$smarty->assign('comments', $comments);
		$smarty->assign('info', $info);
		$smarty->assign('id', $id);
		$smarty->assign('types', $types);
		$smarty->assign('ctype', $ctype);
		$smarty->display('./'.$templates.'admin_request.html');
		break;
}
?>