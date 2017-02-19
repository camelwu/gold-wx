<?php
/* 初始化数组参数 */
$op_ships = array(1=>"上水",2=>"下水");//顺逆流
$op_types = array ("关闭","开通","热门","推荐","专题","微信","销售");
$ro_types = array('团体包价','特殊旅游','散客拼团','自驾游');
$go_types = array('每天发团','每周发团','每月发团','输入团期');
$eats = array(1=>'早餐',2=>'午餐',3=>'晚餐');
$traffics = array('null'=>'无','plane'=>'飞机','train'=>'火车','ship'=>'轮船','car'=>'汽车');
$r2 = array('否','是');
/*城市 查询 酒店、餐厅、景点、新闻等*/
function cg_sight($city = '',$types=3) {
	global $db;
	//$sqlstr = "select id,title from cg_scenic where find_in_set($city,id)";
	$sqlstr = "Select cg_scenic.id,cg_scenic.title from cg_scenic Left JOIN cg_area ON (cg_scenic.aid = cg_area.id or cg_scenic.city = cg_area.id) where cg_scenic.types=$types and locate(cg_area.title, '".$city."')>0 $sql order by cg_scenic.hots";
	$query = $db->query($sqlstr);
	$data = $res = array ();
	while ($row = $db->fetch_array($query)) {
		$res[$row['id']] = $row['title'];
	}
	return $res;
}
/*根据id,table查到title*/
function cg_gettit($myid=0,$tab = '') {
	global $db;
	if($myid==0||$tab==''){
		return '无';
	}else{
		$res = $db->getOneInfo("select title from ".DBFIX.$tab." where id=$myid");
		return $res['title'];
	}
}
/*根据库表字段字符串,查到选中内容*/
function cg_getval($str='',$arr=array()) {
	global $db;
	$val = '';
	while (list($key, $value) = each($arr)) {//foreach ($arr as $key => $value) {
    	if(strpos($str, (string)$key)>-1) $val .= $value.',';//echo "Key: $key; Value: $value<br />\n";
	}
	if(substr($val,-1)===',') $val = substr($val,0,-1);
	return $val;
}
?>
