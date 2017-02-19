<?php
require_once('includes/init.php');
require_once('includes/wechat.php');
admin_priv();

$action=trim(postget('action'));
$types =postget('types')===""?0:(int)postget('types');
$templates = 'templates/';

$perpage = 15;
$page =postget('page')===""?1:(int)postget('page');
$start = ($page -1) * $perpage;
//$tablb = DBFIX."product_route";
if($types==0){
	$tit = "线路产品";
	$table = DBFIX."product_route";
}else{
	$tit = "旅游产品";
	$table = DBFIX."scenic";
}
$smarty->assign('tit', $tit);
$smarty->assign('action', $action);
$smarty->assign('types', $types);

switch ($action) {
	case "handle" :
		$sel_ids=postget('id');
		$ids=is_array($sel_ids)?implode(',',$sel_ids):$sel_ids;
		$op =postget("op_type");
		if($op==="up"){
			$wechatObj = new wechatCallbackapiTest();
			$sqlstr = "select * from $table where url is not null and id in($ids)";
			$qy = $db->query($sqlstr);
			while($data = $db->fetch_array($qy)) {
				$id = $data['id'];
				$cache = explode("/",$data['url']);
				//$data['url'] = reset($cache)."_big.".end($cache);
				$data['url'] = strpos($data['url'],"/")==0?$data['url']:"/".$data['url'];
				$filepath = dirname(__FILE__).$data['url'];
				$picname =end($cache);
				$filedata = array('media'=>"@".$filepath,'filename'=>$picname);
				$res = $wechatObj->Uploadss($filedata);
				$tid = $db->inserttable("cg_wx_gallery", array('pid'=>$id,'types'=>$types,'name'=>$data['title'],'urls'=>$res['url'],'type'=>'image','media_id'=>$res['media_id'],'created_at'=>$now), 1);
				if($types==0){
					$sql = "select * from cg_product_route_stroke where routeid=$id";
					$query = $db->query($sql);
					$url='';
					while ($row = $db->fetch_array($query)) {
						if(!empty($row['url'])){
							$url = $row['url'];
						}else{
							$res = $db->getOneInfo("select url from cg_scenic where url is not null and locate(id,'".$row['scenic']."')");
							if(!empty($res)){
								$url = $res['url'];
							}
						}
						$cache = explode("/",$url);
						//$url = reset($cache)."_big.".end($cache);
						$url = strpos($url,"/")==0?$url:"/".$url;
						$filepath = dirname(__FILE__).$url;
						$picname = end($cache);
						$filedata = array('media'=>"@".$filepath,'filename'=>$picname);
						$res = $wechatObj->UploadImg($filedata);
						$tid = $db->inserttable("cg_wx_gallery", array('pid'=>$id,'types'=>$types,'name'=>$row['num'],'urls'=>$res['url'],'type'=>'image'), 1);
					}
				}else{
					$sql = "select * from cg_hotel where id=$id and url is not null";
					$query = $db->query($sql);
					while ($row = $db->fetch_array($query)) {
						$url = $row['url'];
						$cache = explode("/",$url);
						//$url = reset($cache)."_big.".end($cache);
						$url = strpos($url,"/")==0?$url:"/".$url;
						$filepath = dirname(__FILE__).$url;
						$picname = end($cache);
						$filedata = array('media'=>"@".$filepath,'filename'=>$picname);
						$res = $wechatObj->UploadImg($filedata);
						$tid = $db->inserttable("cg_wx_gallery", array('pid'=>$id,'types'=>$types,'name'=>$row['num'],'urls'=>$res['url'],'type'=>'image'), 1);
					}
				}
				echo "<script>alert('成功上传图片到微信服务器！');location.href='?types=".$types."&page=".$page."';</script>";
			}
		}else if($op=="pass"){//pass:通过验证，可在weixin.cgbt.net显示
			$db->query("update $table set status=1 where id in($ids)");
			echo "<script>alert('成功通过，线路可在weixin.cgbt.net访问！');location.href='?types=".$types."&page=".$page."';</script>";
		}
		//vheader("?types=$types&page=$page");
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
		if($types===0){// and locate('6',p.op_type)
			$sqladd = " where locate('1',p.op_type) and p.url is not null";
		}else{
			$sqladd = " where locate('1',p.op_type) and p.url is not null";
		}
		if (!empty ($_GET['keyword'])) { //keywords
			if ($sqladd == "") {
				$sqladd .= " and p.title like '%" . $_GET['keyword'] . "%'";
			} else {
				$sqladd .= " and p.title like '%" . $_GET['keyword'] . "%'";
			}
		}
		$sqlfrom = " from $table p left join cg_wx_gallery t on (t.pid=p.id and p.title=t.name)". $sqladd;
		$totalnum = $db->result($db->query("select count(*) " . $sqlfrom), 0); //总数;
		$query = $db->query("select *,p.id AS id,t.id AS tid " . $sqlfrom . " order by p.id desc limit $start,$perpage");
		
		$data = $comments = array ();
		while ($data = $db->fetch_array($query)) {
			$comments[] = $data;
		}
		$multipage = multi($totalnum, $perpage, $page, '?types=' . $types);
		$smarty->assign('multipage', $multipage);
		$smarty->assign('comments', $comments);
		$smarty->assign('totalnum', $totalnum);
		$smarty->assign('page', $page);
		$smarty->display('./'.$templates.'admin_allow.html');
		break;
}
?>