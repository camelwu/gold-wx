<?php


/**
 * 验证登陆
 * user pass
 * 
 * ***/
function checkpass($user, $pass) {
	global $db, $ip;
	$query = $db->query("select * from con_members where username= '" . $user . "' ");
	if ($row = $db->fetch_array($query)) {
		if ($pass != $row['password']) {
			return 1;
		} else {
			$_SESSION['allowstr'] = $row['allowstr'];
			$_SESSION['ismember'] = $row['ismember'];
			$_SESSION['ispower'] = $row['ispower'];
			$_SESSION['username'] = $row['username'];
			$_SESSION['userid'] = $row['userid'];
			$_SESSION['groupid'] = $row['groupid'];
			//最后登录时间
			$db->query("update con_members set ts=now(),lastloginip=loginip,loginip='$ip' where userid='{$row['userid']}'");
			return 0;
		}

	} else {
		unset ($query);
		unset ($row);
		return 2;
	}
}
//密码强度检查
function pass_check($pass, $length = 6) {
	if (strlen($pass) < $length) {
		return false;
	}
	if (!preg_match("/[a-zA-Z]/", $pass)) {
		return false;
	}
	if (!preg_match("/[0-9]/", $pass)) {
		return false;
	}
	return true;
}

function display_tree($ismeta = '0') {

	global $db;
	$treelist = array ();
	if ('1' == $ismeta) {
		$sqlwhere = ' and ismeta=1 ';
	}
	elseif ('2' == $ismeta) {
		$sqlwhere = ' and ismeta=0 ';
	}
	$query = $db->query("SELECT lft, rgt FROM con_menuitems WHERE classid='1' ");
	$row = $db->fetch_array($query);
	$right = array ();
	if ($isgroup == 1) {
		$isgroupstr = " and iscp=1";
	}
	elseif ($isgroup == 2) {
		$isgroupstr = " and issp=1";
	}
	elseif ($isgroup == 3) {
		$isgroupstr = " and isidc=1";
	}
	elseif ($isgroup == 4) {
		$isgroupstr = " and isterminal=1";
	}
	$querysql = "SELECT * FROM con_menuitems WHERE lft>=" . $row[lft] . " AND lft<=" . $row[rgt] . " $isgroupstr $sqlwhere ORDER BY lft ASC";
	//echo $querysql;exit;
	$query = $db->query($querysql);
	while ($row = $db->fetch_array($query)) {

		if ($row['classid'] == '152') {
			if ($_SESSION['tcid'] != '' && $_SESSION['tcid'] != 'skyworth') {
				//TODO 非创维厂商无创维支付明细页面
				continue;
			}
		}
		if ($row['parentid'] == '91' || $row['classid'] == '91') {
			if ($_SESSION['username'] == 'gdmonitor') {
				//TODO gdmonitor无运营统计权限
				continue;
			}
		}
		//		$endnote=0;
		$row['quto'] = str_repeat("　", $row[layer] - 1);
		//		if (count($right)>0)
		//		{
		//			while ($right[count($right)-1]<$row['rgt'])
		//			{
		//				array_pop($right);
		//				$endnote=1;
		//			}
		//		}
		//$row['endnote']=$endnote;
		$treelist[] = $row;
		//$right[] = $row['rgt'];
		//var_dump($row);
	}
	//var_dump($treelist);exit;
	return $treelist;
}
function rebuild_tree($classid = 1, $left = 1) {
	global $db;
	$right = $left +1;
	$query = $db->query("SELECT classid FROM con_menuitems WHERE parentid='" . $classid . "' order by classid");
	while ($row = $db->fetch_array($query)) {
		$right = rebuild_tree($row['classid'], $right);
	}
	$db->query("UPDATE con_menuitems SET lft=$left, rgt=$right WHERE classid='" . $classid . "'");
	return $right +1;
}

function rebulayer() {
	global $db;
	$query = $db->query("SELECT * FROM con_menuitems");
	while ($row = $db->fetch_array($query)) {
		$query2 = $db->query("SELECT count(classid)+1 as cd FROM con_menuitems WHERE lft < " . $row[lft] . " AND rgt > " . $row[rgt]);
		$row2 = $db->fetch_array($query2);
		$db->query("update con_menuitems set layer='" . $row2[cd] . "' where classid='" . $row[classid] . "'");
	}

}
function resetpower() {
	global $db;
	$query = $db->query("SELECT classid,lft,rgt FROM con_menuitems WHERE parentid='1'");
	while ($row = $db->fetch_array($query)) {
		$query2 = $db->query("SELECT count(classid) FROM con_menuitems WHERE iscp=1 and lft > " . $row[lft] . " AND rgt < " . $row[rgt]);
		$row2 = $db->result($query2, 0);
		if ($row2) {
			$db->query("update con_menuitems set iscp=1 where classid='" . $row['classid'] . "'");
		}

		$query3 = $db->query("SELECT count(classid) FROM con_menuitems WHERE issp=1 and lft > " . $row[lft] . " AND rgt < " . $row[rgt]);
		$row3 = $db->result($query3, 0);
		if ($row3) {
			$db->query("update con_menuitems set issp=1 where classid='" . $row['classid'] . "'");
		}

		$query4 = $db->query("SELECT count(classid) FROM con_menuitems WHERE isidc=1 and lft > " . $row[lft] . " AND rgt < " . $row[rgt]);
		$row4 = $db->result($query4, 0);
		if ($row4) {
			$db->query("update con_menuitems set isidc=1 where classid='" . $row['classid'] . "'");
		}

		$query5 = $db->query("SELECT count(classid) FROM con_menuitems WHERE isterminal=1 and lft > " . $row[lft] . " AND rgt < " . $row[rgt]);
		$row5 = $db->result($query5, 0);
		if ($row5) {
			$db->query("update con_menuitems set isterminal=1 where classid='" . $row['classid'] . "'");
		}

		$query6 = $db->query("SELECT count(classid) FROM con_menuitems WHERE ismeta=1 and lft > " . $row[lft] . " AND rgt < " . $row[rgt]);
		$row6 = $db->result($query6, 0);
		if ($row6) {
			$db->query("update con_menuitems set ismeta=1 where classid='" . $row['classid'] . "'");
		}
	}
}

function getselstrtreed($treelist, $classid = 1) {
	$selstr = '';
	foreach ($treelist as $tl) {
		$checked = "";
		if ($tl[classid] == $classid)
			$checked = "selected=\"selected\"";
		$selstr .= "<option value=$tl[classid] $checked>" . str_repeat("　", $tl[layer] - 1) . "$tl[classname]</option>";
	}
	return $selstr;
}

function getselstrtree($treelist) {
	global $siteurl;
	$selstr = '';
	foreach ($treelist as $key => $tl) {
		if ($tl['classid'] != '1') {
			if ($tl['parentid'] == '1') {
				if ($key > 1) {
					$selstr .= "</ul></li>";
				}
				$selstr .= "<li id=ChildMenu" . $key . " class=collapsed><a href='#Menu=ChildMenu" . $key . "'  onclick=DoMenu('ChildMenu" . $key . "') class=expanded_on>$tl[classname]</a><ul class=clearfix>";
			} else {
				if (stristr($tl["url"], "http://") == '') {
					$selstr .= "<li class=memutext_style1 id=memutext0><a href=" . $siteurl . "/" . $tl['url'] . " target=right>" . str_repeat("　", $tl[layer] - 3) . $tl[classname] . "</a></li>";
				} else {
					$selstr .= "<li class=memutext_style1 id=memutext0><a href=" . $tl['url'] . " target=right>" . str_repeat("　", $tl[layer] - 3) . $tl[classname] . "</a></li>";
				}
			}
		}
	}
	if (!empty ($selstr)) {
		$selstr .= "</ul></li>";
	}
	return $selstr;
}
function getselstrtrees($treelist, $allowstr) {
	global $siteurl;
	//var_dump($treelist);exit;
	$selstr = '';
	$i = 0;
	$arr = explode(',', $allowstr);
	foreach ($treelist as $key => $tl) {
		if ($tl['classid'] != '1') {
			if (in_array($tl['classid'], $arr)) {
				if ($tl['parentid'] == '1') {
					$i++;
					if ($i > 1) {
						$selstr .= "</ul></li>";
					}
					$selstr .= "<li id=ChildMenu" . $key . " class=collapsed><a href='#Menu=ChildMenu" . $key . "'  onclick=DoMenu('ChildMenu" . $key . "') class=expanded_on>$tl[classname]</a><ul class=clearfix>";
				} else {
					if (stristr($tl["url"], "http://") == '') {
						$selstr .= "<li class=memutext_style1 id=memutext0><a href=" . $siteurl . "/" . $tl['url'] . " target=right>" . str_repeat("　", $tl[layer] - 3) . $tl[classname] . "</a></li>";
					} else {
						$selstr .= "<li class=memutext_style1 id=memutext0><a href=" . $tl['url'] . " target=right>" . str_repeat("　", $tl[layer] - 3) . $tl[classname] . "</a></li>";
					}
				}
			}
		}
	}
	if (!empty ($selstr)) {
		$selstr .= "</ul></li>";
	}
	return $selstr;
}

//左侧菜单
function getdptree() {

	$treelist = display_tree();
	$tempstr .= getselstrtree($treelist);
	return $tempstr;

}
function getdptrees($allowstr, $ismeta = '0') {

	$treelist = display_tree($ismeta);
	$tempstr .= getselstrtrees($treelist, $allowstr);
	return $tempstr;

}
//权限分配
function getallowtree($isgroup = '', $splitstr = '') {
	$treelist = display_tree();
	if ($isgroup == '1') {
		$tempstr .= getstrtreegroupcp($treelist, $isgroup, $splitstr);
	}
	elseif ($isgroup == '2') {
		$tempstr .= getstrtreegroupsp($treelist, $isgroup, $splitstr);
	}
	elseif ($isgroup == '3') {
		$tempstr .= getstrtreegroupidc($treelist, $isgroup, $splitstr);
	}
	elseif ($isgroup == '4') {
		$tempstr .= getstrtreegroupterminal($treelist, $isgroup, $splitstr);
	} else {
		$tempstr .= getstrtree($treelist, $splitstr);
	}
	return $tempstr;
}

function getstrtreegroupterminal($treelist, $isgroup, $splitstr) {
	global $siteurl;
	$arr = explode(',', $splitstr);
	$i = 0;
	$selstr = '<li><input type=button value=全部选定 onclick=checkall(this.form)> <input type=button value=全部清空 onclick=checkother(this.form)></li>';
	foreach ($treelist as $key => $tl) {
		if ($tl['isterminal'] == '1') {
			if ($tl['classid'] != '1') {
				$checked = (in_array($tl['classid'], $arr)) ? "checked=checked" : "";
				if ($tl['parentid'] == '1') {
					if ($key > 1) {
						$selstr .= "</li></ul></li>";
					}
					$m = intval(($tl['rgt'] - $tl['lft']) / 2);
					$n = 'A' . $tl['classid'];
					unset ($i);
					$i = 0;
					$selstr .= "<li><input id='$n' type='checkbox' " . $checked . " name='allowstr[]' value=" . $tl[classid] . " onclick=checker('$n','$m')  />" . $tl[classname] . "<ul class=clearfix><li>";
				} else {
					$i++;
					$nm = $n . ($i -1);
					$selstr .= " <input id='$nm' " . $checked . " type=checkbox onclick=checkerd('$n') name='allowstr[]' value=" . $tl[classid] . ">" . str_repeat("　", $tl[layer] - 3) . $tl[classname];
				}
			}
		}
	}

	$selstr .= "</li></ul></li>";
	return $selstr;
}

function getstrtreegroupidc($treelist, $isgroup, $splitstr) {
	global $siteurl;
	$arr = explode(',', $splitstr);
	$i = 0;
	$selstr = '<li><input type=button value=全部选定 onclick=checkall(this.form)> <input type=button value=全部清空 onclick=checkother(this.form)></li>';
	foreach ($treelist as $key => $tl) {
		if ($tl['isidc'] == '1') {
			if ($tl['classid'] != '1') {
				$checked = (in_array($tl['classid'], $arr)) ? "checked=checked" : "";
				if ($tl['parentid'] == '1') {
					if ($key > 1) {
						$selstr .= "</li></ul></li>";
					}
					$m = intval(($tl['rgt'] - $tl['lft']) / 2);
					$n = 'A' . $tl['classid'];
					unset ($i);
					$i = 0;
					$selstr .= "<li><input id='$n' type='checkbox' " . $checked . " name='allowstr[]' value=" . $tl[classid] . " onclick=checker('$n','$m')  />" . $tl[classname] . "<ul class=clearfix><li>";
				} else {
					$i++;
					$nm = $n . ($i -1);
					$selstr .= " <input id='$nm' " . $checked . " type=checkbox onclick=checkerd('$n') name='allowstr[]' value=" . $tl[classid] . ">" . str_repeat("　", $tl[layer] - 3) . $tl[classname];
				}
			}
		}
	}

	$selstr .= "</li></ul></li>";
	return $selstr;
}

function getstrtreegroupsp($treelist, $isgroup, $splitstr) {
	global $siteurl;
	$arr = explode(',', $splitstr);
	$i = 0;
	$selstr = '<li><input type=button value=全部选定 onclick=checkall(this.form)> <input type=button value=全部清空 onclick=checkother(this.form)></li>';
	foreach ($treelist as $key => $tl) {
		if ($tl['issp'] == '1') {
			if ($tl['classid'] != '1') {
				$checked = (in_array($tl['classid'], $arr)) ? "checked=checked" : "";
				if ($tl['parentid'] == '1') {
					if ($key > 1) {
						$selstr .= "</li></ul></li>";
					}
					$m = intval(($tl['rgt'] - $tl['lft']) / 2);
					$n = 'A' . $tl['classid'];
					unset ($i);
					$i = 0;
					$selstr .= "<li><input id='$n' type='checkbox' " . $checked . " name='allowstr[]' value=" . $tl[classid] . " onclick=checker('$n','$m')  />" . $tl[classname] . "<ul class=clearfix><li>";
				} else {
					$i++;
					$nm = $n . ($i -1);
					$selstr .= " <input id='$nm' " . $checked . " type=checkbox onclick=checkerd('$n') name='allowstr[]' value=" . $tl[classid] . ">" . str_repeat("　", $tl[layer] - 3) . $tl[classname];
				}
			}
		}
	}

	$selstr .= "</li></ul></li>";
	return $selstr;
}
function getstrtreegroupcp($treelist, $isgroup, $splitstr) {
	global $siteurl;
	$arr = explode(',', $splitstr);
	$i = 0;
	$selstr = '<li><input type=button value=全部选定 onclick=checkall(this.form)> <input type=button value=全部清空 onclick=checkother(this.form)></li>';
	foreach ($treelist as $key => $tl) {
		if ($tl['iscp'] == '1') {
			if ($tl['classid'] != '1') {
				$checked = (in_array($tl['classid'], $arr)) ? "checked=checked" : "";
				if ($tl['parentid'] == '1') {
					if ($key > 1) {
						$selstr .= "</li></ul></li>";
					}
					$m = intval(($tl['rgt'] - $tl['lft']) / 2);
					$n = 'A' . $tl['classid'];
					unset ($i);
					$i = 0;
					$selstr .= "<li><input id='$n' type='checkbox' " . $checked . " name='allowstr[]' value=" . $tl[classid] . " onclick=checker('$n','$m')  />" . $tl[classname] . "<ul class=clearfix><li>";
				} else {
					$i++;
					$nm = $n . ($i -1);
					$selstr .= " <input id='$nm' " . $checked . " type=checkbox onclick=checkerd('$n') name='allowstr[]' value=" . $tl[classid] . ">" . str_repeat("　", $tl[layer] - 3) . $tl[classname];
				}
			}
		}
	}

	$selstr .= "</li></ul></li>";
	return $selstr;
}
function getstrtree($treelist, $splitstr) {
	global $siteurl;
	$arr = explode(',', $splitstr);
	$selstr = '<li><input type=button value=全部选定 onclick=checkall(this.form)> <input type=button value=全部清空 onclick=checkother(this.form)></li>';
	$i = 0;
	foreach ($treelist as $key => $tl) {
		if ($tl['classid'] != '1') {
			$checked = (in_array($tl['classid'], $arr)) ? "checked=checked" : "";
			if ($tl['parentid'] == '1') {
				if ($key > 1) {
					$selstr .= "</li></ul></li>";
				}
				$m = intval(($tl['rgt'] - $tl['lft']) / 2);
				$n = 'A' . $tl['classid'];
				unset ($i);
				$i = 0;
				$selstr .= "<li><input id='$n' type='checkbox' " . $checked . " name='allowstr[]' value=" . $tl[classid] . " onclick=checker('$n','$m')  />" . $tl[classname] . "<ul class=clearfix><li>";
			} else {
				$i++;
				$nm = $n . ($i -1);
				$selstr .= " <input id='$nm' " . $checked . " type=checkbox onclick=checkerd('$n') name='allowstr[]' value=" . $tl[classid] . ">" . str_repeat("　", $tl[layer] - 3) . $tl[classname];
			}
		}
	}

	$selstr .= "</li></ul></li>";
	return $selstr;
}
function getprem($allowstr, $splitstr = '', $ismeta = '0') {
	$treelist = display_tree($ismeta);
	$tempstr .= getstrtreesd($treelist, $allowstr, $splitstr);
	return $tempstr;

}
function getstrtreesd($treelist, $allowstr, $splitstr) {
	global $siteurl;
	$arr = explode(',', $splitstr);
	$allow = explode(',', $allowstr);
	$i = 0;
	$selstr = '<li><input type=button value=全部选定 onclick=checkall(this.form)> <input type=button value=全部清空 onclick=checkother(this.form)></li>';
	foreach ($treelist as $key => $tl) {
		if (in_array($tl['classid'], $allow)) {
			if ($tl['classid'] != '1') {
				$checked = (in_array($tl['classid'], $arr)) ? "checked=checked" : "";
				if ($tl['parentid'] == '1') {
					if ($key > 1) {
						$selstr .= "</li></ul></li>";
					}
					$m = intval(($tl['rgt'] - $tl['lft']) / 2);
					$n = 'A' . $tl['classid'];
					unset ($i);
					$i = 0;
					$selstr .= "<li><input id='$n' onclick=checker('$n','$m') type='checkbox' " . $checked . " name='allowstr[]' value=" . $tl[classid] . "  />" . $tl[classname] . "<ul class=clearfix><li>";
				} else {
					$i++;
					$nm = $n . ($i -1);
					$selstr .= " <input id='$nm' " . $checked . " type=checkbox onclick=checkerd('$n') name='allowstr[]' value=" . $tl[classid] . ">" . str_repeat("　", $tl[layer] - 3) . $tl[classname];
				}
			}
		}
	}
	$selstr .= "</li></ul></li>";
	return $selstr;
}
function gettreename($classid) {
	global $db;
	$query = $db->query("select classname from con_menuitems where classid='" . $classid . "'");
	$dbq_rec = $db->fetch_array($query);
	return $dbq_rec[classname];
}

function get_allow_oemid($groupid, $contentid, $allowoemid) {
	global $db;
	$sql = "select oemid,concat(CAST(oemid as char),'-',name) as name from dim_oem where 1 ";
	$contentids =  implode(',',$contentid); 
	if ($groupid == '2') {
		$sql .= " and dim_isp_id in (" . $contentids.")";
	}
	elseif ($groupid == '4') {
		$sql .= " and dim_provider_id in (" . $contentids.")";
	}
	$sql .= " order by oemid ";
	$check = str_to_array($allowoemid);
	$query = $db->query($sql);
	while ($dbq_rec1 = $db->fetch_array($query)) {
		if ($check[$dbq_rec1['oemid']])
			$isselect = "checked=\"checked\"";
		else
			$isselect = "";
		$shop_select .= "<li><input $isselect type=\"checkbox\" name=\"oemid[]\" value=\"$dbq_rec1[oemid]\"/><span>$dbq_rec1[name]</span></li>";
	}
	return $shop_select;
}

function str_to_array($allowoemid) {
	$result = array ();
	$arr = explode(',', $allowoemid);
	foreach ($arr as $key => $value) {
		$result[$value] = true;
	}
	return $result;
}
/**
 * 得到上级类别名称
 *
 * @param 整型 $parentid
 * @return 类别名称
 */
//权限ID以|分开
function splitarry($arr) {
	global $db;
	$str = '';
	$common = '';
	if (is_array($arr)) {
		//添加父类ID
		foreach ($arr as $classid) {
			$query = $db->query("select parentid from con_menuitems where classid='$classid'");
			$re2 = $db->fetch_array($query);
			if ($re2['parentid'] != '1') {
				$arr[] = $re2['parentid'];
			}
		}
		$arr = array_unique($arr);
		foreach ($arr as $key) {
			$str .= $common . $key;
			$common = ',';
		}
	}
	return $str;
}
//权限
function getadminallow($str) {
	global $db;
	$arr = explode(',', $str);
	$string = '';
	$query = $db->query('select classid,classname from con_menuitems where parentid > 1');
	while ($row = $db->fetch_array($query)) {
		if (in_array($row['classid'], $arr)) {
			$string .= $row['classname'] . ',';
		}
	}
	return $string;
}
function getuptreename($parentid) {
	global $db;
	$query = $db->query("select classname from con_menuitems where classid='" . $parentid . "'");
	$dbq_rec = $db->fetch_array($query);
	return $dbq_rec[classname];
}

function getsubclassid($classid) {
	global $db;
	$classid = intval($classid);
	$query = $db->query("select lft,rgt from con_menuitems where classid='" . $classid . "'");
	$tempa = $db->fetch_array($query);
	$sqlstr = "SELECT classid FROM con_menuitems WHERE lft BETWEEN " . $tempa[lft] . " AND " . $tempa[rgt] . " ORDER BY lft ASC";
	$query = $db->query($sqlstr);
	$tempstr = "(-1";
	while ($row = $db->fetch_array($query)) {
		$tempstr .= ",$row[classid]";
	}
	$tempstr .= ")";
	return $tempstr;

}

//CP
function getcpstr() {
	global $db;
	$query = $db->query("select id, name from dim_cp order by name");
	$shop = array ();
	while ($row = $db->fetch_array($query)) {
		$shop[$row['id']] = $row['name'];
	}
	return $shop;
}
//sp
function getspstr() {
	global $db;
	$query = $db->query("select * from dim_isp order by name");
	$shop = array ();
	while ($row = $db->fetch_array($query)) {
		$shop[$row['id']] = $row['name'];
	}
	return $shop;
}

function getprovider() {
	global $db;
	$query = $db->query("select * from dim_provider where name<>'' order by name");
	$shop = array ();
	while ($row = $db->fetch_array($query)) {
		$shop[$row['id']] = $row['name'];
	}
	return $shop;
}
/***
 * 数据字典
 * $type ：parentkey
 * ***/
function get_dim_dictionary($type) {
	global $db;
	$sql = " select * from dim_dictionary where parentkey = '" . $type . "'";
	$shop = array ();
	$query = $db->query($sql);
	while ($row = $db->fetch_array($query)) {
		$shop[$row['keyvalue']] = $row['name'];
	}
	return $shop;
}
?>