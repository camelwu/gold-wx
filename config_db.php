<?php
require_once('includes/init.php');
require_once('includes/checklogin.php');
include_once('admin_tpl_head.php');

if(!empty($_GET['action'])){
$action=$_GET['action'];
}else{
$action="";
}
//
switch ($action){
case "config":
	$db0 = $_POST['dbHost'];
	$db1 = $_POST['dbUser'];
	$db2 = $_POST['dbPassword'];
	$db3 = $_POST['dbDatabase'];
	$db4 = $_POST['dbCharset'];
	$setfile = './includes/config.php';
	if (is_readable($file) == false) {
		die('文件不存在或者无法读取');
	} else {
		$data = file_get_contents($file);
		$data = preg_replace("/db_host = \'(.*?)\'/","db_host = '".$db0."'",$data);
		$data = preg_replace("/db_name = \'(.*?)\'/","db_name = '".$db3."'",$data);
		$data = preg_replace("/db_user = \'(.*?)\'/","db_user = '".$db1."'",$data);
		$data = preg_replace("/db_pw = \'(.*?)\'/","db_pass = '".$db2."'",$data);
		//$data = preg_replace("/dbname = \'(.*?)\'/","dbname = '".$db3."'",$data);
		file_put_contents ($file, $data);
		echo"<script language=javascript>alert('成功执行！');location.href='?action=';</script>";
	}
break;
default:
?>
<table cellspacing=1 cellpadding=4 class=table>
<tr class=bg height=0><td width='20%'></td><td width='80%'></td></tr>
<tr><td class=td colspan=2>数据库配置</td></tr>
<form action="?action=handle" method="post">
<tr>
<td>数据库地址：</td>
<td><input type="text" class="text" name="dbHost" value="localhost"/></td>
</tr>
<tr>
<td>数据库名：</td>
<td><input type="text" class="text" name="dbDatabase" value="dbs"/></td>
</tr>
<tr>
<td>数据库用户名：</td>
<td><input type="text" class="text" name="dbUser" value=""/></td>
</tr>
<tr>
<td>数据库密码：</td>
<td><input type="text" class="text" name="dbPassword" value=""/></td>
</tr>
<tr>
<td>数据库编码：</td>
<td><input type="text" class="text" name="dbCharset" value="utf-8"/></td>
</tr>
<tr>
<td></td>
<td><input type=submit value='提 交'>　　<input type=reset value='重 填'></td>
</tr>
</form>
</table>
<?php
}
include_once('admin_tpl_foot.php');
?>