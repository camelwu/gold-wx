<?php
require_once('includes/init.php');
require_once('includes/wechat.php');
admin_priv();
$action=isset($_GET['action'])?$_GET['action']:"";
$templates = 'templates/';
$wechatObj = new wechatCallbackapiTest();
$tit="群发消息";

switch ($action){
	case "handle":
		//send
		;
		
	break;
	
	default:
		//用户分组
		//$url = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token=".$wechatObj->_token;
		$groups = $wechatObj->GetGroups();//var_dump($groups);
		//图文消息
		$smarty->assign('groups', $groups['groups']);
		$smarty->display('./'.$templates.'admin_send.html');
	break;
}
?>