<?php
/**
 * wechat ajax
 * $Author: wusongbo $
 * $Date: 2015-04-01 $
 */
include_once('./includes/init.php');
admin_priv();
//
$ctypes = array(1=>'线路',2=>'签证',3=>'景点',5=>'游记');
$links = array(1=>'tours',2=>'visa',3=>'sight',5=>'blog');
//
$action=isset($_GET['action'])?$_GET['action']:"";
$types=isset($_GET['types'])?(int)$_GET['types']:1;
$ctype=isset($_GET['ctype'])?$_GET['ctype']:0;
$keys=isset($_GET['keys'])?$_GET['keys']:"";
//
switch($action){
	case "getxml":
		if($ctype==1){
			$table = 'cg_product_route';
			$sqladd = '';
		}else{
			$table = 'cg_scenic';
			$sqladd = ' and types='.$ctype;
		}
		$sql = "select * from $table where locate('$keys',title)$sqladd";
		//0:word,1:single,2:mulity
		$res = array();
		if($types==1){
			$data = $db->getOneInfo($sql);
			if(!empty($data))
			array_push($res,$data);
		}else{
			$query = $db->query($sql);
			while($data = $db->fetch_array($query)){
				array_push($res,$data);
			}
		}
		//
		echo json_encode($res);
	break;
	case "getsc":
		$sql = "select * from cg_product_route t right join cg_wx_gallery p on p.pid=t.id where locate('$keys',t.title)";
		//0:word,1:single,2:mulity
		$res = array();
		$query = $db->query($sql);
		while($data = $db->fetch_array($query)){
			array_push($res,$data);
		}
		echo json_encode($res);
	break;
	case "getmsg":
		$sql = "select id,title,url,feature,price2,price3 from cg_product_route where url is not null and locate('$keys',title)";
		#2:网站售价，3：门市价
		$query = $db->query($sql);
		$res = $imgs = array();
		while($data = $db->fetch_array($query)){
			//加入行程，并且有图片的选中；方便后续直接上传到微信服务器。
			$que = $db->query("select * from cg_product_route_stroke where routeid=".$data['id']." order by num");
			$stroke='';
			while($web = $db->fetch_array($que)){
				array_push($imgs,$web["url"]);
				$stroke.='<div id="day_'.$web["num"].'"><p>第：'.$web["num"].'天</p>';
				$stroke.=!empty($web["url"])?'<p><img src="'.$web["url"].'"></p>':'';
				$stroke.='<strong>'.$web["departure"].'-'.$web["arrived"].'</strong>';
				$stroke.=!empty($web["eats"])?'<br> <strong>食：</strong>'.$web["eats"]:'';
				$stroke.=!empty($web["hotel"])?'<br> <strong>住：</strong>'.$web["hotel"]:'';
				$stroke.=!empty($web["scenic"])?'<br> <strong>娱：</strong>'.$web["scenic"]:'';
				$stroke.=!empty($web["content"])?'<br> '.$web["content"].'</div>':'</div>';
			}
			$data["imgs"] = $imgs;
			$data["stroke"] = $stroke;
			array_push($res,$data);
		}
		echo json_encode($res);
	break;
	default:
		$smarty->display('./templates/gallery.html');
	break;
}
?>