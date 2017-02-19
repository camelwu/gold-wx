<?php
require_once('includes/init.php');
require_once('includes/wechat.php');
admin_priv();
$action=isset($_GET['action'])?$_GET['action']:"";
$templates = 'templates/';
//
$table = DBFIX."wx_menu";
$smarty->assign('ver',VER);
$smarty->assign('tit','菜单');
switch ($action){
	case "up" :
		if((int)$_GET['hots']>0){
			$db->query("update $table set hots=if(hots>0,hots-1,0) where id=" . $_GET['id']);
			$db->query("update $table set hots=hots+1 where hots=".$_GET['hots']." and id!=" . $_GET['id']);
		}
		vheader($filename);
		break;
	case "down":
		$num = $_GET['pid']==0?2:4;
		if((int)$_GET['hots']<$num){
			$db->query("update $table set hots=hots+1 where id=" . $_GET['id']);
			$db->query("update $table set hots=if(hots>0,hots-1,0) where hots=".$_GET['hots']." and id!=" . $_GET['id']);
		}
		vheader($filename);
		break;
	case "add":
		$pid = $_POST['pid'];
		if($pid==0){//一级菜单
			$hots = $db->getOne("select hots from $table where pid=0 order by hots desc limit 0,1");
			$_POST['hots'] = is_null($hots)?0:(int)$hots+1;
			if((int)$hots>=2){
				echo('<script>alert("一级菜单最多3个");histroy.go(-1);</script>');exit;
			}
		}else{
			$hots = $db->getOne("select hots from $table where pid=$pid order by hots desc limit 0,1");
			$_POST['hots'] = is_null($hots)?0:(int)$hots+1;
			if((int)$hots>=4){
				echo('<script>alert("二级菜单最多5个");histroy.go(-1);</script>');exit;vheader($filename);
			}
		}

		$do = 1;
		$id = $db->inserttable($table, $_POST, 1);
		
		//do_daily('menu', $id, $do, 'menu', $types);
		vheader($filename);
	break;
	case "del" :
		$sqlstr = "delete from $table where id=" . $_GET['id'] . "";
		$sqlstr2 = "delete from $table where pid=" . $_GET['id'] . "";
		$db->query($sqlstr);
		$db->query($sqlstr2);
		
		vheader($filename);
	break;
	case "editc":
		$id = $_POST['id'];
		$do = 0;
		$db->updatetable($table, $_POST, array (
			'id' => $id
		));
		
		vheader($filename);
	break;
	case "handle":
		$wechatObj = new wechatCallbackapiTest();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
		//
		$sql = "select * from $table where pid=0 order by hots asc";
		$query = $db->query($sql);
		while ($row = $db->fetch_array($query)) {
			//
			$totalnum = $db->result($db->query("select count(*) from $table where pid=".$row['id']), 0);
			echo $totalnum;
			if((int)$totalnum>0){
				
				$res = array();
				$q = $db->query("select * from $table where pid=".$row['id']." order by hots asc");
				while ($dat = $db->fetch_array($q)) {
					$type = $dat["type"]=='view'?"url":"key";
					$res[] = array("type"=>$dat["type"],"name"=>$dat["title"],$type=>$dat["html"]);
					
				}
				#var_dump($res);
				$arr[] = array('name'=>$row['title'],'sub_button'=>$res);
			}else{
				$type = $row["type"]=='view'?"url":"key";
				$arr[] = array("type"=>$row["type"],"name"=>$row["title"],$type=>$row["html"]);
				
                /*$arr["sub_button": [ ]*/
			}
			$menu['button'] = $arr;
		}
		var_dump(json_encode($arr));
		$jsonmenu = json_encode($menu);
		$wechatObj->http_request($url,$jsonmenu);
	break;
	default:
		
		$sql = "select * from $table where pid=0 order by hots asc";
		$query = $db->query($sql);
		while ($row = $db->fetch_array($query)) {
			$res[] = $row;
			$pid[$row['id']] = $row['title'];
			$q = $db->query("select * from $table where pid=".$row['id']." order by hots asc");
			while ($dat = $db->fetch_array($q)) {
				$res[] = $dat;
			}
		}
		$smarty->assign('otype',array('view'=>'浏览','click'=>'点击'));
		$smarty->assign('res', $res);
		$smarty->assign('pid', $pid);
		$smarty->display('./'.$templates.'config_menu.html');
	break;
}
/*
{
    "menu": {
        "button": [
            {
                "name": "优选线路", 
                "sub_button": [
                    {
                        "type": "view", 
                        "name": "特色推荐", 
                        "url": "http://weixin.cgbt.net/features", 
                        "sub_button": [ ]
                    }, 
                    {
                        "type": "view", 
                        "name": "自由行", 
                        "url": "http://weixin.cgbt.net/free", 
                        "sub_button": [ ]
                    }, 
                    {
                        "type": "view", 
                        "name": "旅行攻略", 
                        "url": "http://weixin.cgbt.net/blog", 
                        "sub_button": [ ]
                    }
                ]
            }, 
            {
                "type": "view", 
                "name": "景点门票", 
                "url": "http://weixin.cgbt.net/sight", 
                "sub_button": [ ]
            }, 
            {
                "type": "view", 
                "name": "旅行签证", 
                "url": "http://weixin.cgbt.net/visa", 
                "sub_button": [ ]
            }
        ]
    }
}
*/
?>