<?php
/*
*function list
*/
function sel_detail($object){
	global $db, $picserver, $siteurl, $bid;
	$keyword = trim($object->Content);
	
		$lnum = 4;
		$sql = "select id,pid from cg_area where locate(title,'$keyword')";
		$res = $db->getOneInfo($sql);
		$data = array();
		$do = 1;//非线路产品
		if (strstr($keyword, "线路") || strstr($keyword, "旅游") || strstr($keyword, "之旅") || strstr($keyword, "游")){
			$do = 0;
			$keyword = str_replace("线路","",$keyword);
			$keyword = str_replace('旅游','',$keyword);
			$keyword = str_replace("之旅","",$keyword);
			$keyword = str_replace("游","",$keyword);
			$sqlwhere = " where 1=1 and locate('1',optype)";
			$sqladd = " and locate('$keyword',title)";
			$orderbysql = ' order by hid desc';
			$limitsql = ' limit 0,' .$lnum;
			$sql = "select * from (select p.id,p.title,p.info_id,p.classid,p.classid2,p.go_day,p.go_num,p.keywords,p.price2,p.url,p.city2,p.remark,p.feature,p.info,t.hid,t.title name,t.departure,t.price1 price_1,t.price2 price_2,t.userid uid,t.op_type optype,t.op_user opuser,t.bid from cg_product_route_sale t,cg_product_route p where t.id=p.id and p.id) result" . $sqlwhere. $sqladd . $orderbysql . $limitsql;
			$query = $db->query($sql);return $sql;
			$row = array ();
			while ($row = $db->fetch_array($query)) {return $row;
				if (!empty ($value['url'])) {
					$value['url'] = (stristr($value["url"], "http://") == '') ? $picserver . replaceSeps($value["url"]) : $value["url"];
				}
				$price = $db->getOneInfo("select price,pass from cg_product_route_do where id=" . $value['id'] . " and pass=0 order by price desc limit 0,1");
				if (!empty ($price)) {
					$value['price_2'] = $value['pass']?(int) $value['price_2'] + (int) $price['price']:(int) $value['price_2'] - (int) $price['price'];
				}
				$value['feature'] = cut_utf8(str_replace("&nbsp;", "", strip_tags($value['feature'])), 39, '...');
				$data[] = $value;
			}
			$detail = 'tour';
		}
	return $data;
}
/*
 *获取出团时间
*/
function go_tim($t,$v){
	switch ($t){
	case 0:
		return "每天发团";
		break;
	case 1:
		return "每周".$v;
		break;
	case 2:
		return "每月".$v;
		break;
	default:
		return $v;
		break;
	}
}
/*
 *获取每日餐饮
*/
function go_eat($v){
	if(empty($v)||$v===''){
		return '无餐';
	}else{
		$go_eat = '';
		if(strpos($v,'1')>-1){
			$go_eat .= '含早餐 ';
		}else{
			$go_eat .= '× ';
		}
		if(strpos($v,'2')){
			$go_eat .= '含午餐 ';
		}else{
			$go_eat .= '× ';
		}
		if(strpos($v,'3')){
			$go_eat .= '含晚餐 ';
		}else{
			$go_eat .= '× ';
		}
		return $go_eat;
	}
}
/*城市 查询 酒店、餐厅、景点、新闻等*/
function go_sight($c = '',$types = 3) {
	global $db;
	if(empty($c)) return '';
	if(strpos($c,"'")) $c = str_replace("'","",$c);
	if(preg_replace("/([u4e00-u9fa5])/","",$c)) return $c;
	$sqlstr = "select id,title,url from cg_scenic where types=$types and id in($c)";
	
	$query = $db->query($sqlstr);
	$res = array ();
	while ($row = $db->fetch_array($query)) {
		$res[$row['id']] = $row['title'];
	}
	return $res;
}
/*
 * 获取广告数据
 * */
function selectdatabanner($sectionID, $count) {
	global $db, $picserver, $siteurl, $bid;
	$orderbysql = ' order by hots ';
	if ($bid)
		$sqldataid = ' and bid=' . $bid;
	else
		$sqldataid = '';
	if ($sectionID)
		$sqldataid .= ' and mid=' . $sectionID;
	if ($count)
		$limitsql = ' limit ' . $count;
	$sql = 'select * from cg_banner where status=1' . $sqldataid . $orderbysql . $limitsql;
	$query = $db->query($sql);
	while ($value = $db->fetch_array($query)) {
		if (!empty ($value['url'])) {
			$value['url'] = (stristr($value["url"], "http://") == '') ? $picserver . replaceSeps($value["url"]) : $value["url"];
		}
		$re[] = $value;
	}
	return $re;
}
/*
 * 获取产品数据
 * */
function wx_product($count, $optype = "1", $types = 0, $ctype = 0, $ctype1 = 0, $start = 0) {
	global $db, $picserver, $siteurl, $bid;
	
	/*if ($bid)
		$sqlwhere = " where bid=$bid and locate('1',op_type)";
	else*/
		$sqlwhere = " where locate('1',op_type)";
	if ($types != 0 || !empty ($types)) {
		$sqlwhere .= " and types=" . $types;

	}
	if ($ctype) {
		$sqlwhere .= ' and ctype=' . $ctype;
	}
	if ($ctype1) {
		$sqlwhere .= ' and ctype1=' . $ctype1;
	}
	if (!empty($optype)&&"1"!==$optype) {
		$sqlwhere .= " and locate('" . $optype . "',op_type)";
	}
	if ($count == 1)
		$orderbysql = ' order by rand()';
	else
		$orderbysql = ' order by id desc';
	if ($count)
		$limitsql = ' limit '.$start.','.$count;

	$sql = "select * from cg_scenic" . $sqlwhere . $orderbysql . $limitsql;
	$query = $db->query($sql);
	while ($row = $db->fetch_array($query)) {
		if (!empty ($row['url'])) {
			$row['url'] = (stristr($row["url"], "http://") == '') ? $picserver . replaceSeps($row["url"]) : $row["url"];
		}
		$row['word'] = cut_utf8(str_replace("&nbsp;", "", strip_tags($row['word'])), 270, '...');
		$data[] = $row;
	}
	return $count == 1 ? $data[0] : $data;
}
/*
 * 获取线路数据
 * */
function wx_route($count, $type = '1', $classid = 0, $classid2 = 0, $start = 0) {
	global $db, $picserver, $siteurl, $bid, $enname;

	/*if ($bid)
		$sqladd = " where bid=$bid and locate('1',optype)";
	else*/
		$sqladd = " where 1=1 and locate('1',op_type)";
	if($type!=='1')
		$sqladd .= " and locate('" . $type . "',op_type)";
	if ($classid)
		$sqladd .= " and classid=" . $classid;
	if ($classid2)
		$sqladd .= " and classid2=" . $classid2;
	if ($type == '1,2,3' && $classid == 0 && $classid2 == 0)
		$orderbysql = ' order by rand()';
	else
		$orderbysql = ' order by id desc';
	if ($count)
		$limitsql = ' limit ' . $start . ',' .$count;
	$sql = "select * from cg_product_route " . $sqladd . $orderbysql . $limitsql;
	//return $sql;
	$query = $db->query($sql);
	while ($value = $db->fetch_array($query)) {
		if (!empty ($value['url'])) {
			$value['url'] = (stristr($value["url"], "http://") == '') ? $picserver . replaceSeps($value["url"]) : $value["url"];
		}
		$price = $db->getOneInfo("select price from cg_product_route_do where id=" . $value['id'] . " and pass=0 order by price desc limit 0,1");
		if (!empty ($price)) {
			$value['price_2'] = (int) $value['price_2'] - (int) $price['price'];
		}
		$value['feature'] = $enname==="index"?cut_utf8(str_replace("&nbsp;", "", strip_tags($value['feature'])), 50, '...'):$value['feature'];
		$re[] = $value;
	}
	return $count == 1 ? $re[0] : $re;
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
function sendmail2($emails = '',$subject='',$content='',$names='用户',$echo=1){
	include_once(V_ROOT."/includes/class.phpmailer.php");
	$configIni = './data/mail.ini';
	$config    =   parse_ini_file($configIni,true);
	$mailconfig = $config['mail1'];
	$mail2 = $config['mail2']['Username'];
	//print_r($mailconfig);
	$emailSuccess = $emailInvalid = $emailFail = array();
	$strSuccess = $strInvalid = $strFail = $strJump = '';

	if($emails != ''){
		$emailPubHead = "<FONT color='red'>%s</font>：<br><br>
			发送如下邮件<br><br><br>";
		$emailPubFoot = "<br><br>来自手机用户的邮件。";

		$cur_email = trim($emails);

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->IsHTML($mailconfig['IsHTML']);
		$mail->CharSet =  $mailconfig['CharSet']; // 设置字符集编码 
		$mail->Encoding = $mailconfig['Encoding'];//设置文本编码方式 						 
		$mail->Host = 	  $mailconfig['Host'];
		$mail->SMTPAuth = $mailconfig['SMTPAuth'];
		$mail->WordWrap = $mailconfig['WordWrap'];
		$mail->Username = $mailconfig['Username'];
		$mail->Password = $mailconfig['Password'];
		//$mail->From =	$mailconfig['From'];
		//$mail->FromName = $mail->Username;
		$mail->SetFrom($cur_email,$names);
		$subject = $subject != '' ? $subject : $mailconfig['Subject'];
		if (function_exists("iconv")) { $subject = iconv("UTF-8","GBK",$subject); }
		$subject = "=?GBK?B?".base64_encode($subject)."?=";

		$mail->Subject = $subject;
		$emailContent = $mailconfig['Body'];

		$emailContent = str_replace('_EMAIL',	$cur_email,	$emailContent);
		$emailContent = str_replace('_DATETIME',date('Y-m-d h:i'),	$emailContent);
		$emailContent = $content != '' ? $content : $emailContent;
		$emailContent = sprintf($emailPubHead, $cur_email).$emailContent.$emailPubFoot;

		//if (function_exists("iconv")) { $emailContent = iconv("UTF-8","GBK",$emailContent); }
		//die($emailContent);
		$mail->Body = $emailContent;
		$mail->AddAddress("xiaohua@xiaohua.net");
		if(!$mail->Send()){
			$result = "发送失败： " . $mail->ErrorInfo;
		}else{
			$result = "发送成功！ ";
		}

	}
	
	if($echo===1) return $result;
}
?>