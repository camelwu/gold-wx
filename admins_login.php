<?php
require_once ('includes/init.php');

$action=isset($_GET['action'])?$_GET['action']:"";
$template = "templates/";
switch ($action) {
	case "login" :
		/*if (killbad($_POST['code'])!=$_SESSION['num'] || empty($_SESSION['num'])){
			print "<script language=javascript>window.alert('验证码错误!');location.href='".$_SERVER['HTTP_REFERER']."';</script>";
			exit;
		}else{*/
		if (!empty ($_POST["username"])) {
			//$password = md5($posts["password"]);
			$username = killbad($_POST["username"]);
			$password = md5(killbad($_POST["password"]));
			$query = "SELECT * FROM " . DBFIX . "user WHERE username = '$username'";
			$result = $db->query_select($query);
			//$result=@mysql_query($query);
			if ($data = mysql_fetch_array($result)) {
				if ($data['password'] == $password) {
					$_SESSION["admin"] = true;
					$_SESSION["admin_id"] = $data['id'];
					$_SESSION["admin_name"] = $data['name'];
					$_SESSION["username"] = $data['username'];
					$_SESSION["password"] = $password;
					$_SESSION["bid"] = $data['bid'];
					$_SESSION["allowstr"] = $data['allowstr'];
					$sqlstr = "update " . DBFIX . "user set login_num=login_num+1,login_ip='" . $_SERVER["REMOTE_ADDR"] . "',time2='" . date("Y-m-d H:i:s") . "' where id=" . $data[0];
					$db->query($sqlstr);
					if ($_SERVER['HTTP_REFERER'] == "" || strpos($_SERVER['HTTP_REFERER'], "admins_login") > 1) {
						print "<script language=javascript>window.alert('登录成功！');location.href='admin.php';</script>";
					} else {
						print "<script language=javascript>window.alert('欢迎回来！');location.href='" . $_SERVER['HTTP_REFERER'] . "';</script>";
					}
					
				} else {
					print "<script language=javascript>window.alert('密码错误！');location.href='" . $_SERVER['HTTP_REFERER'] . "';</script>";
				}
			} else {
				print "<script language=javascript>window.alert('用户名错误！');location.href='" . $_SERVER['HTTP_REFERER'] . "';</script>";
			}
		}
		//}
		@ mysql_free_result($result);
		break;
	case "logout" :
		unset ($_SESSION['admin_id']);
		unset ($_SESSION['admin_name']);
		
		exit("<script language=javascript>window.alert('成功退出！');location.href='./';</script>");
	break;
	default:
		$smarty->assign('version',	VER);
		$smarty->display('./'.$template.'admins_login.html');
	break;
}?>