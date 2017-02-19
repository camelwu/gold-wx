<?php
/**
 * weixin.cgbt.net
 * 手机版
 * $Author: wusongbo $
 * $Date: 2015-04-20 $
 */

include_once('./includes/init.php');

/*paramster----------------------------------------------------*/
$template = 'moban';
$optype = "1";
$perpage = 8;
//rewrite
$enname = isset($_GET['enname'])?$_GET['enname']:"";
$id = isset($_GET['id'])?$_GET['id']:0;
$match = isset($_GET['match'])?$_GET['match']:"------";
//get
$q = isset($_GET['q'])?$_GET['q']:"";
$action = isset($_GET['action'])?$_GET['action']:"";
$types = isset($_GET['types'])?$_GET['types']:0;
$ctype = isset($_GET['ctype'])?$_GET['ctype']:0;
$ctype1 = isset($_GET['ctype1'])?$_GET['ctype1']:0;
$page = isset($_GET['page'])?intval($_GET['page']):1;
$start = ($page -1) * $perpage;
/*-------------------------------------------------------------*/
//if(!function_exists('init_config')){//maybe ajax request
	
	if($action == 'ajax'){
		//include_once (V_ROOT . '/small/ajax.php');
		if($q==="scenic"){
			
			echo json_encode(wx_product($perpage,$optype,$types,$ctype,$ctype1,$start));
		}elseif($q==="tour"){
			echo json_encode(wx_route($perpage,$optype,$ctype,$ctype1,$start));
		}else{
			echo "null";
		}
		exit;
	}
//}else{
	include_once('./includes/wechat.php');
traceHttp();
	$wechatObj = new wechatCallbackapiTest();
	if (isset($_GET['echostr'])) {//初始校验token
		$wechatObj->valid();
	}else{//其他处理
		if(isset($_GET['signature'])){//isset($GLOBALS["HTTP_RAW_POST_DATA"])
			$wechatObj->responseMsg();
		}
	}
//}
/*
*moban参数
*/
$smarty->assign('root','/');
$smarty->assign('enname',$enname);
/*if ($bid)
	$sqlwhere = " where bid=$bid and locate('1',op_type)";
	else*/
$sqlwhere = " where 1=1 and locate('1',op_type)";
/*根据栏目enname判断*/
switch($enname){
	case "mobile":
		include_once('./mobile.php');
		exit;
	break;
	case "tour":
		$sqlfrom = "from (select p.*,t.hid,t.title name,t.departure,t.price1 price_1,t.price2 price_2,t.userid uid,t.op_type optype,t.op_user opuser,t.bid as bid2 from cg_product_route_sale t,cg_product_route p where t.id=p.id and p.id=$id) result";
			$info = $db->getOneInfo("select * ".$sqlfrom);
			if (!empty ($info)) {
				$info['go_time'] = go_tim($info['go_type'],$info['go_time']);
				$info['ro_type'] = strpos($info['ro_type'],',')?explode(',',$info['ro_type']):$info['ro_type'];
				$info['addr'] = strpos($info['addr'],',')?explode(',',$info['addr']):$info['addr'];
				$info['tim'] = strpos($info['tim'],',')?explode(',',$info['tim']):$info['tim'];
				$pic = array();
				$query = $db->query("select title,url from cg_product_route_do where id=".$id." and ctype=1 and url is not Null and url!='' order by hid asc");
				while ($data = $db->fetch_array($query)) {
					$pic[] = $data;
				}
				$smarty->assign('pic', $pic);
				//day
				$query = $db->query("select * from cg_product_route_stroke where routeid=".$id." order by num asc limit ".$info['go_day']);
				while ($data = $db->fetch_array($query)) {
					$data['eats'] = go_eat($data['eats']);
					$data['scenics'] = go_sight($data['scenic']);
					$day[] = $data;
				}
				$smarty->assign('stroke', $day);
			}else{
				$smarty->assign('error', "请正确访问！");
			}
			$smarty->assign('info', $info);
			$smarty->display(V_ROOT.'./'.$template.'/details.html');
			exit;
	break;
	case "features"://特色
		$classid=112;
		$classid2=0;$limitsql='';
		if (empty ($id)) {//list
			$smarty->assign('data',wx_route($perpage,$optype,$classid,0,$start));
			$sqladd = " where 1=1 and locate('1',optype)";
			if($optype!=='1')
				$sqladd .= " and locate('" . $optype . "',optype)";
			if ($classid)
				$sqladd .= " and classid=" . $classid;
			if ($classid2)
				$sqladd .= " and classid2=" . $classid2;
			
				$orderbysql = ' order by hid desc';
			$sqlfrom = "from (select p.id,p.title,p.info_id,p.classid,p.classid2,p.go_day,p.go_num,p.keywords,p.price2,p.url,p.city2,p.info,t.hid,t.title name,t.departure,t.price1 price_1,t.price2 price_2,t.userid uid,t.op_type optype,t.op_user opuser,t.bid from cg_product_route_sale t,cg_product_route p where t.id=p.id and p.id) result";
			$sql = "select * " . $sqlfrom . $sqladd . $orderbysql . $limitsql;

			$totalnum = $db->result($db->query("select count(*) " . $sqlfrom. $sqladd), 0); //总数
			$totalnum = $totalnum==''?0:$totalnum;
			$pagecount = ceil($totalnum/8); //页数
			$smarty->assign('pagecount',$pagecount);
			$smarty->assign('totalnum',$totalnum);
			$smarty->assign('classid',$classid);
			$smarty->assign('cnname','出境游');$smarty->assign('cnword','美丽的线路');
			$smarty->display(V_ROOT.'./'.$template.'/tour.html');exit;
		}else{
			$sqlfrom = "from (select p.*,t.hid,t.title name,t.departure,t.price1 price_1,t.price2 price_2,t.userid uid,t.op_type optype,t.op_user opuser,t.bid as bid2 from cg_product_route_sale t,cg_product_route p where t.id=p.id and p.id=$id) result";
			$info = $db->getOneInfo("select * ".$sqlfrom);
			if (!empty ($info)) {
				$info['go_time'] = go_tim($info['go_type'],$info['go_time']);
				$info['ro_type'] = strpos($info['ro_type'],',')?explode(',',$info['ro_type']):$info['ro_type'];
				$info['addr'] = strpos($info['addr'],',')?explode(',',$info['addr']):$info['addr'];
				$info['tim'] = strpos($info['tim'],',')?explode(',',$info['tim']):$info['tim'];
				$query = $db->query("select title,url from cg_product_route_do where id=".$id." and ctype=1 and url is not Null and url!='' order by hid asc");
				while ($data = $db->fetch_array($query)) {
					$pic[] = $data;
				}
				$smarty->assign('pic', $pic);
				//day
				$query = $db->query("select * from cg_product_route_stroke where routeid=".$id." order by num asc limit ".$info['go_day']);
				while ($data = $db->fetch_array($query)) {
					$data['eats'] = go_eat($data['eats']);
					$data['scenics'] = go_sight($data['scenic']);
					$day[] = $data;
				}
				$smarty->assign('stroke', $day);
			}else{
				$smarty->assign('error', "请正确访问！");
			}
			$smarty->assign('info', $info);
			$smarty->display(V_ROOT.'./'.$template.'/details.html');
			exit;
		}
	break;
	case "free"://自由行
		$classid=117;
		$classid2=0;$limitsql='';
		if (empty ($id)) {
			$smarty->assign('data',wx_route($perpage,$optype,$classid,0,$start));
			$sqladd = " where 1=1 and locate('1',optype)";
			if($optype!=='1')
				$sqladd .= " and locate('" . $optype . "',optype)";
			if ($classid)
				$sqladd .= " and classid=" . $classid;
			if ($classid2)
				$sqladd .= " and classid2=" . $classid2;
			
				$orderbysql = ' order by hid desc';
			$sqlfrom = "from (select p.id,p.title,p.info_id,p.classid,p.classid2,p.go_day,p.go_num,p.keywords,p.price2,p.url,p.city2,p.info,t.hid,t.title name,t.departure,t.price1 price_1,t.price2 price_2,t.userid uid,t.op_type optype,t.op_user opuser,t.bid from cg_product_route_sale t,cg_product_route p where t.id=p.id and p.url is not Null and p.id) result";
			$sql = "select * " . $sqlfrom . $sqladd . $orderbysql . $limitsql;
	
			$totalnum = $db->result($db->query("select count(*) " . $sqlfrom. $sqladd), 0); //总数
			$totalnum = $totalnum==''?0:$totalnum;
			$pagecount = ceil($totalnum/8); //页数
			$smarty->assign('pagecount',$pagecount);
			$smarty->assign('totalnum',$totalnum);
			$smarty->assign('classid',$classid);
			$smarty->assign('cnname','自由行');$smarty->assign('cnword','牛人的线路');
			$smarty->display(V_ROOT.'./'.$template.'/tour.html');exit;
		}else{
			$sqlfrom = "from (select p.*,t.hid,t.title name,t.departure,t.price1 price_1,t.price2 price_2,t.userid uid,t.op_type optype,t.op_user opuser,t.bid as bid2 from cg_product_route_sale t,cg_product_route p where t.id=p.id and p.id=$id) result";
			$info = $db->getOneInfo("select * ".$sqlfrom);
			if (!empty ($info)) {
				$info['go_time'] = go_tim($info['go_type'],$info['go_time']);
				$info['ro_type'] = strpos($info['ro_type'],',')?explode(',',$info['ro_type']):$info['ro_type'];
				$info['addr'] = strpos($info['addr'],',')?explode(',',$info['addr']):$info['addr'];
				$info['tim'] = strpos($info['tim'],',')?explode(',',$info['tim']):$info['tim'];
				$query = $db->query("select title,url from cg_product_route_do where id=".$id." and ctype=1 and url is not Null and url!='' order by hid asc");
				while ($data = $db->fetch_array($query)) {
					$pic[] = $data;
				}
				$smarty->assign('pic', $pic);
				//day
				$query = $db->query("select * from cg_product_route_stroke where routeid=".$id." order by num asc limit ".$info['go_day']);
				while ($data = $db->fetch_array($query)) {
					$data['eats'] = go_eat($data['eats']);
					$data['scenics'] = go_sight($data['scenic']);
					$day[] = $data;
				}
				$smarty->assign('stroke', $day);
			}else{
				$smarty->assign('error', "请正确访问！");
			}
			$smarty->assign('info', $info);
			$smarty->display(V_ROOT.'./'.$template.'/details.html');exit;
		}
	break;
	case "visa"://签证
		$sqlfrom = "from cg_scenic";
		$types = 2;
		
		if (empty ($id)){
			$area = array();
			$query = $db->query("select title,hots from cg_class where classtype=5 and pid=0 order by hots limit 8");
			while ($row = $db->fetch_array($query)) {
				
				$cid = (int)$row['hots']+1;
				$sql = "select t.aid as id,p.title from cg_area p,cg_scenic t where t.types=2 and t.cid=$cid and p.id=t.aid group by t.aid ";
				$query1 = $db->query($sql);
				$res = array();
				
				while ($val = $db->fetch_array($query1)) {
					$res[] = $val;
				}
				
				if(count($res)){$stat[] = $row['title'];$area[] = $res;}
			}
			$smarty->assign('stat', $stat);
			$smarty->assign('area', $area);
		}else{
			$sql = "select title from cg_area where id=" . $id;
			$country = $db->getOneInfo($sql);
			$smarty->assign('country',$country);
			
			$sqlwhere = " where 1=1 and locate('$optype',op_type)";
			$sqladd = " and types=$types";
			$sqldataid = " and aid=$id";
			$orderbysql = ' order by id desc,hots desc';
			$sqlstr = "select * ".$sqlfrom. $sqlwhere. $sqladd. $sqldataid . $orderbysql;
			$query = $db->query($sqlstr);
			$data = array();
			while ($row = $db->fetch_array($query)) {
				if($row['ctype']){
					$val = $db->getOneInfo("select title from cg_class where id=" . $row['ctype']);
					$row['tname'] = $val['title'];
				}else{
					$row['tname'] = '';
				}
				$data[] = $row;
			}
			$smarty->assign('data',$data);
		}
	break;
	case "sight"://门票
		$sqlfrom = "from cg_scenic";
		$types = 3;
		if (empty ($id)){
			$smarty->assign('data',wx_product($perpage,$optype,$types,0,0,$start));
			
			$sqladd = " and types=$types";
			$sqldataid = '';
			$totalnum = $db->result($db->query("select count(*) " . $sqlfrom. $sqlwhere. $sqladd. $sqldataid), 0); //总数
			$totalnum = $totalnum==''?0:$totalnum;
			$pagecount = ceil($totalnum/8); //页数
			$smarty->assign('pagecount',$pagecount);
			$smarty->assign('totalnum',$totalnum);
		}else{
			$sqlstr = "select * $sqlfrom where id=" . $id;
			$info = $db->getOneInfo($sqlstr);
			if (!empty ($info)) {
				$query = $db->query("select title,url from cg_hotel where id=".$id." and ctype=1 and url is not Null and url!='' order by hid asc");
				while ($data = $db->fetch_array($query)) {
					$pic[] = $data;
				}
				$smarty->assign('pic', $pic);
			}else{
				$smarty->assign('error', "请正确访问！");
			}
			$smarty->assign('product', 'product');
			$smarty->assign('info', $info);
			$smarty->display(V_ROOT.'./'.$template.'/details.html');exit;
		}
	break;
	case "blog"://攻略
		$sqlfrom = "from cg_scenic";$types = 5;
		if (empty ($id)){
			$smarty->assign('news',wx_product($perpage,$optype,$types,98,0,$start));
			
			$sqladd = " and types=$types";
			$sqldataid = ' and ctype=98';
			$totalnum = $db->result($db->query("select count(*) " . $sqlfrom. $sqlwhere. $sqladd. $sqldataid), 0); //总数
			$totalnum = $totalnum==''?0:$totalnum;
			$pagecount = ceil($totalnum/8); //页数
			$smarty->assign('pagecount',$pagecount);
			$smarty->assign('totalnum',$totalnum);
		}else{
			$sqlstr = "select * $sqlfrom where id=" . $id;
			$info = $db->getOneInfo($sqlstr);
			if (!empty ($info)) {
				$smarty->assign('info', $info);
			}else{
				$smarty->assign('info', '');$smarty->assign('error', "请正确访问！");
			}
			$smarty->assign('product', 'blog');
			
			$smarty->display(V_ROOT.'./'.$template.'/blog-detail.html');exit;
		}
	break;
	case "contact"://联系
		if($_SERVER['REQUEST_METHOD']=="POST"){
			if(empty($_POST['contactNameField'])||empty($_POST['contactEmailField'])||empty($_POST['contactMessageTextarea'])){//anyone is empty
				echo "信息输入不全，请输入！";
			}else{
				if(!i_isEmail($_POST['contactEmailField'])){
					echo "请输入可用的Email！";
				}else{
					$name = $_POST['contactNameField'];
					$emails = $_POST['contactEmailField'];
					$content = $_POST['contactMessageTextarea'];
					$subject = "用户留言";
					echo sendmail2($emails,$subject,$content,$name);
				}
			}
			
			
		}
	break;
	case "user"://用户
		if (isset($_SESSION['user'])) {//had login
			$smarty->display(V_ROOT.'./'.$template.'/'.$enname.'.html');
		}else{
			$smarty->display(V_ROOT.'./'.$template.'/login.html');
		}
	break;
	case "sigin":
		$smarty->display(V_ROOT.'./'.$template.'/sigin.html');
	break;
	case "forgot":
		$smarty->display(V_ROOT.'./'.$template.'/forgot.html');
	break;
	case "cart"://购物
		if (empty ($id)) {
			
		}else{
			
		}
	break;
	default://默认，首页？
		$enname = 'index';
		$smarty->assign('banner',selectdatabanner(1,3));
		//当季最热
		$smarty->assign('hots',wx_route(6,"2"));
		//自由行
		$smarty->assign('cjfree',wx_route(6,"3",117));
		//专题
		$smarty->assign('project',array());
		//游记
		$smarty->assign('news',wx_product(8,"3",5,98));
		//门票
		$smarty->assign('scenic',wx_product(8,"3",3));
	break;
}
//display smarty templates
$smarty->display(V_ROOT.'./'.$template.'/'.$enname.'.html');
?>