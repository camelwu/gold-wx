<?php
$continent = array (
	"港澳台",
	"亚洲",
	"欧洲",
	"北美洲",
	"南美洲",
	"非洲",
	"大洋洲",
	"南极洲",
	"中国大陆"
);
$act= isset($_GET['act'])?$_GET['act']:"";
if("outjs"==$act){
	require_once('./init.php');
	header('Content-type: application/x-javascript; charset=utf-8');
	$sqladd = ' where 1';
	$table=DBFIX."area";
	$sqlfrom = " from " . $table . $sqladd;
	$sql="select id,title,classid,pid from ".$table." order by id asc";
	$totalnum = $db->result($db->query("select count(*) " . $sqlfrom), 0); //总数;	
	echo "c_aid = new Array();c_city = new Array();";
	if($totalnum>0){
		$subcat=0;$city=0;
		$query = $db->query("select id,title,classid,pid" . $sqlfrom." order by id desc");
		while($data=$db->fetch_array($query)){
			if($data['pid']==0){
				echo "c_aid[$subcat] = new Array('".strtoupper(getFirstCharter($data['title'])).$data['title']."',".$data['classid'].",".$data['id'].");";
				$subcat++;
			}else{
				echo "c_city[$city] = new Array('".strtoupper(getFirstCharter($data['title'])).$data['title']."',".$data['pid'].",".$data['id'].");";
				$city++;
			}
		}
	}
	$db->free_result($query);
	echo "c_aid.sort(function(x, y){
  return x[0].localeCompare(y[0]);
});
c_city.sort(function(x, y){
  return x[0].localeCompare(y[0]);
});";
}
//php获取中文字符拼音首字母
function getFirstCharter($str){
    if(empty($str)){return '';}
    $fchar=ord($str{0});
    if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    $s1=iconv('UTF-8','gb2312',$str);
    $s2=iconv('gb2312','UTF-8',$s1);
    $s=$s2==$str?$s1:$str;
    $asc=ord($s{0})*256+ord($s{1})-65536;
    if($asc>=-20319&&$asc<=-20284) return 'A';
    if($asc>=-20283&&$asc<=-19776) return 'B';
    if($asc>=-19775&&$asc<=-19219) return 'C';
    if($asc>=-19218&&$asc<=-18711) return 'D';
    if($asc>=-18710&&$asc<=-18527) return 'E';
    if($asc>=-18526&&$asc<=-18240) return 'F';
    if($asc>=-18239&&$asc<=-17923) return 'G';
    if($asc>=-17922&&$asc<=-17418) return 'H';
    if($asc>=-17417&&$asc<=-16475) return 'J';
    if($asc>=-16474&&$asc<=-16213) return 'K';
    if($asc>=-16212&&$asc<=-15641) return 'L';
    if($asc>=-15640&&$asc<=-15166) return 'M';
    if($asc>=-15165&&$asc<=-14923) return 'N';
    if($asc>=-14922&&$asc<=-14915) return 'O';
    if($asc>=-14914&&$asc<=-14631) return 'P';
    if($asc>=-14630&&$asc<=-14150) return 'Q';
    if($asc>=-14149&&$asc<=-14091) return 'R';
    if($asc>=-14090&&$asc<=-13319) return 'S';
    if($asc>=-13318&&$asc<=-12839) return 'T';
    if($asc>=-12838&&$asc<=-12557) return 'W';
    if($asc>=-12556&&$asc<=-11848) return 'X';
    if($asc>=-11847&&$asc<=-11056) return 'Y';
    if($asc>=-11055&&$asc<=-10247) return 'Z';
    return null;
}
//echo getFirstCharter('张');
/*生成select菜单*/
function sel_area($str,$n=''){
	global $continent,$db;
	$selstr="";
	
	if($str==""){
		$str=0;
	}
	if(strpos($str,',')){
		$arg = explode(',',$str);
	}else{
		$arg = $str;
	}
	//输出洲际
	$selstr = "<select name='c_id$n' id='c_id$n' onChange='changecid(this.options[this.selectedIndex].value,$n);'>";
	while (list($key,$value) = each($continent)) {
		$sel = intval($arg[0])==intval($key+1)?" selected":"";
		$selstr .= "<option value='".intval($key+1)."'$sel>$value</option>";
	}
	$selstr .= "</select>";
	if(is_array($arg)&&count($arg)>1){
		$selstr .= "<select name='aid$n' id='aid$n' onChange='changeaid(this.options[this.selectedIndex].value,$n);'><option value='0'>一级地区</option>";
		if($arg[0]!=""){
			$query="select id,title,classid,pid,region from ".DBFIX."area where classid=$arg[0] and pid=0 order by region asc";
			#return $query;
			$result=$db->query_select($query);
			if($db->num_rows($result)>0){
				while($data=mysql_fetch_array($result)){
					$sel = intval($arg[1])==intval($data[0])?" selected":"";
					
					$selstr .= "<option value='".$data[0]."'$sel>".strtoupper(substr($data[4],0,1)).$data[1]."</option>";
				}
			}
		}
		$selstr .= "</select>";
	//
	if(count($arg)>2){
		$selstr .= "<select name='city$n' id='city$n'><option value='0'>二级地区</option>";
		if($arg[1]!=""){
			$query="select id,title,classid,pid,region from ".DBFIX."area where classid=$arg[0] and pid=$arg[1] order by region asc";
			$result=$db->query_select($query);
			if($db->num_rows($result)>0){
				while($data=mysql_fetch_array($result)){
					$sel = intval($arg[1])==intval($data[0])?" selected":"";
					
					$selstr .= "<option value='".$data[0]."'$sel>".strtoupper(substr($data[4],0,1)).$data[1]."</option>";
				}
			}
		}
		$selstr .= "</select>";
	}
	//
	}
	//$selstr .= '';
	return $selstr;
}
?>