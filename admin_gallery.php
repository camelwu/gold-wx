<?php
require_once('includes/init.php');
require_once('includes/wechat.php');
admin_priv();
$action=isset($_GET['action'])?$_GET['action']:"";
$types=isset($_GET['types'])?trim($_GET['types']):"image";
$templates = 'templates/';

$perpage = 15;
$page =postget('page')===""?1:(int)postget('page');
$start = ($page -1) * $perpage;

$table = DBFIX."wx_gallery";
$smarty->assign('ver',VER);
$smarty->assign('tit','素材');
$smarty->assign('action', $action);
$smarty->assign('types',$types);
$ctype = array(1=>'线路',2=>'签证',3=>'景点',5=>'游记');
switch ($action){
	case "add" :
		$sqlstr = "select * from $table where id=" . $_GET['id'] . "";
		$res = $db->getOneInfo($sqlstr);
		$smarty->assign('ctype', $ctype);
		
		$smarty->display('./'.$templates.'admin_gallery.html');
		break;
	case "handle":
		$wechatObj = new wechatCallbackapiTest();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
		$jsonmenu = '';
		$wechatObj->http_request($url,$jsonmenu);
	
		$id = $_POST['id'];
		if (empty ($id)) { //add
			unset ($_POST['id']);
			$pid = $_POST['pid'];
			$hots = $db->getOne("select hots from $table where pid=$pid order by hots desc");
			$do = 1;
			$_POST['hots'] = is_null($hots)?0:(int)$hots+1;
			$id = $db->inserttable($table, $_POST, 1);
		} else { //edit
			$do = 0;
			$db->updatetable($table, $_POST, array (
				'id' => $id
			));
		}
		//do_daily('menu', $id, $do, 'menu', $types);
		vheader($filename);
	break;
	case "pic":
		//判断主图
		$img = $db->getOne("select media_id from $table where urls is not null and media_id is not null and id=".$_POST['id']);
		
		if(!empty($img)){
			$thumb_media_id = $img;
		}else{
			$wechatObj = new wechatCallbackapiTest();
			$filedata = array('media'=>"@".$_POST["url"],'filename'=>$_POST["name"]);
			$res = $wechatObj->Uploadss($filedata);
			$tid = $db->inserttable("cg_wx_gallery", array('pid'=>$_POST["id"],'types'=>0,'name'=>$_POST['name'],'urls'=>$res['url'],'type'=>'image','media_id'=>$res['media_id'],'created_at'=>$now), 1);
			$thumb_media_id = $res['media_id'];
		}
		$string = $_POST["stroke"];
		preg_match_all("/<img([^>]*)\s*src=('|\")([^'\"]+)('|\")/", $string,$matches);
		$new_arr=array_unique($matches[2][0]);//去除数组中重复的值
		foreach($new_arr as $key=>$val){
			$picurl=get_name($val);//这里处理图片并得到处理后的地址
			str_replace($val,$picurl,$string);
		}
		
		$sql = "select *,p.id AS pid,t.id AS id from $table t left join ".DBFIX."product_route p on p.id=t.pid order by t.id desc";
		$query = $db->query($sql);
		while ($row = $db->fetch_array($query)) {
			$row['created_at'] = date("Y-m-d H:i:s",$row['created_at']);
			$res[] = $row;
		}
		$smarty->assign('otype',array('view'=>'浏览','click'=>'点击'));
		$smarty->assign('res', $res);
		
		$smarty->display('./'.$templates.'admin_gallery.html');
	break;
	default:
		if (!empty ($_GET['keywords'])) { //keywords
			if ($sqladd == "") {
				$sqladd .= " and p.title like '%" . $_GET['keywords'] . "%'";
			} else {
				$sqladd .= " and p.title like '%" . $_GET['keywords'] . "%'";
			}
		}
		$sql = "select *,p.id AS pid,t.id AS id from $table t left join ".DBFIX."product_route p on (p.id=t.pid) where t.type='".$types."'$sqladd order by t.id desc";
		$query = $db->query($sql);
		while ($row = $db->fetch_array($query)) {
			$row['created_at'] = date("Y-m-d H:i:s",$row['created_at']);
			$res[] = $row;
		}
		$smarty->assign('otype',array('view'=>'浏览','click'=>'点击'));
		$smarty->assign('res', $res);
		$multipage = multi($totalnum, $perpage, $page, '?types=' . $types);
		$smarty->assign('multipage', $multipage);
		$smarty->assign('comments', $comments);
		$smarty->assign('page', $page);
		$smarty->assign('keywords', $_GET['keywords']);
		$smarty->display('./'.$templates.'admin_gallery.html');
	break;
}
function get_name($pic_item,$id){
	$wechatObj = new wechatCallbackapiTest();
	$filedata = array('media'=>"@".$_POST["url"]);
	$res = $wechatObj->UploadImg($filedata);
	$tid = $db->inserttable("cg_wx_gallery", array('pid'=>$id,'types'=>0,'name'=>$row['num'],'urls'=>$res['url'],'type'=>'image'), 1);
	return $res['url'];
}
/*
{
   "articles": [
		 {
             "thumb_media_id":"qI6_Ze_6PtV7svjolgs-rN6stStuHIjs9_DidOHaj0Q-mwvBelOXCFZiq2OsIU-p",
             "author":"xxx",
			 "title":"Happy Day",
			 "content_source_url":"www.qq.com",
			 "content":"content",
			 "digest":"digest",
             "show_cover_pic":"1"
		 },
		 {
             "thumb_media_id":"qI6_Ze_6PtV7svjolgs-rN6stStuHIjs9_DidOHaj0Q-mwvBelOXCFZiq2OsIU-p",
             "author":"xxx",
			 "title":"Happy Day",
			 "content_source_url":"www.qq.com",
			 "content":"content",
			 "digest":"digest",
             "show_cover_pic":"0"
		 }
   ]
}
*/
?>