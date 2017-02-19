<?php
require_once('includes/init.php');
admin_priv();

$action=isset($_GET['action'])?$_GET['action']:"";
$templates = 'templates/';
switch ($action){
case "left":
	$id =  $_SESSION["admin_id"];
	$info = $db->getOneInfo("select * from cg_user where id=".$id);
	$smarty->assign('info',$info);
	$smarty->assign('tree',getLeftTree($info['allowstr']));
	$smarty->display('./'.$templates.'admins_left.html');
	break;
case "main":
 	$arr=array();
	$ary=array();
	/* 服务器参数 */
	$smarty->assign('SERVER_NAME',$_SERVER["SERVER_NAME"]);
	$smarty->assign('SERVER_ADDR',$_SERVER["SERVER_ADDR"]);
	$smarty->assign('SERVER_PORT',$_SERVER["SERVER_PORT"]);
	$smarty->assign('times',date("Y-m-d H:i:s"));
	$smarty->assign('SERVER_SOFTWARE',$_SERVER["SERVER_SOFTWARE"]);
	$smarty->assign('timeo','30 秒');
	$smarty->assign('DOCUMENT_ROOT',$_SERVER["DOCUMENT_ROOT"]);
	$smarty->assign('SERVER_ADMIN',$_SERVER["SERVER_ADMIN"]);
	/* 微信函数 */
	require_once ('includes/wechat.php');
	$wechatObj = new wechatCallbackapiTest();
	$user = $wechatObj->GetUserList();
	$usernum = $user['total'];
	$smarty->assign('usernum',$usernum);
	$smarty->display('./'.$templates.'admins_main.html');
	break;
case "top":
	$smarty->display('./'.$templates.'admins_top.html');
	break;
default:
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微信订阅号管理平台'.VER.'</title>
</head>

<frameset rows="*" cols="185,*" framespacing="0" frameborder="1" border="false" id="frame" scrolling="yes">
  <frame name="leftFrame" id="leftFrame" scrolling="yes" marginwidth="0" marginheight="0" src="admin.php?action=left">
  <frameset framespacing="0" border="false" rows="35,*" frameborder="0" scrolling="yes">
    <frame name="topFrame" scrolling="no" src="admin.php?action=top">
    <frame name="right" scrolling="yes" src="admin.php?action=main">
  </frameset>
</frameset>
<noframes>
  <body margin="2">
  <p>本页采用框架技术，您的浏览器不支持框架！</p>
  </body>
</noframes>';
	break;
}

//全部文件
function admin_frame(){
	

}//全部文件
?>
</html>