<?php


/* 初始化Smarty */
function startSmarty() {
	global $smarty, $siteurl, $picserver, $uploaddir;
	if (empty ($smarty)) {
		@ ini_set('include_path', V_ROOT . './includes/Smarty');
		include_once (V_ROOT . './includes/Smarty/Smarty.class.php');
		$smarty = new Smarty;
		$smarty->template_dir = V_ROOT;
		$smarty->config_dir = V_ROOT . './data/smarty_config';
		$smarty->cache_dir = V_ROOT . './data/smarty_cache';
		$smarty->compile_dir = V_ROOT . './data/smarty_compile';
		$smarty->left_delimiter = '<{';
		$smarty->right_delimiter = '}>';
		$smarty->assign('siteurl', $siteurl);
		$smarty->assign('picserver', $picserver);
		$smarty->assign('uploaddir', $uploaddir);
	}
}


/*****************************
 函数功能：生成分页URL地址集合
 参    数：num>>记录总数
 perpage>>每页记录数
 curpage>>当前记页数
 mpurl>>URL参数集合
 maxpages>>
 *****************************/
function multi($num, $perpage, $curpage, $mpurl) {
	$multipage = '';
	$mpurl .= (strpos($mpurl, '?') !== false) ? '&amp;' : '?';
	$realpages = 1;
	$realpages = @ ceil($num / $perpage);

	$multipage = "<div>现有<font class=red>" . $num . "</font>条记录，" . "页次：<font class=red>" .
	$curpage . "</font>/<font class=red>" . $realpages . "</font>";
	if ($realpages > 1) {
		$multipage .= "分页：<a href=" . $mpurl . "page=1>首页</a> | <a href=" . $mpurl .
		"page=" . (($curpage > 1) ? ($curpage -1) : 1) . ">上页</a> | <a href=" . $mpurl . "page=" . (($curpage < $realpages) ? ($curpage +1) : $realpages) . ">下页</a> " .
		"| <a href=" . $mpurl . "page=" . $realpages . ">尾页</a></div>";
	}
	return $multipage;
}

/*************************
 函数功能：url跳转
 参    数：url>>要跳转到的
 url地址.
 *************************/
function vheader($url) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: $url");
	exit ();
}

/**
 * 得到上级类别名称
 *
 * @param 整型 $parentid
 * @return 类别名称
 */
//权限ID以,分开
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

//权限ID以,分开
function splitarry2($arr) {
	global $db;
	if (!empty ($arr)) {
		return join(',', $arr);
	}

}
/******************
 * 菜单表结构重构
 * **********************/

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

/*******************************
 菜单tree
 *****************************/
function display_tree() {
	global $db;
	$treelist = array ();
	$query = $db->query("SELECT lft, rgt FROM con_menuitems WHERE classid='1' ");
	$row = $db->fetch_array($query);
	$querysql = "SELECT * FROM con_menuitems WHERE lft>=" . $row['lft'] . " AND lft<=" . $row['rgt'] . "   ORDER BY lft ASC";
	$query = $db->query($querysql);
	while ($row = $db->fetch_array($query)) {
		$row['quto'] = str_repeat("　", $row['layer'] - 1);
		$treelist[] = $row;
	}
	return $treelist;
}

function display_treeadmin() {
	global $db;
	$treelist = array ();
	$query = $db->query("SELECT lft, rgt FROM con_menuitems WHERE classid='1' ");
	$row = $db->fetch_array($query);
	$querysql = "SELECT * FROM con_menuitems WHERE lft>=" . $row['lft'] . " AND lft<=" . $row['rgt'] . "   ORDER BY lft ASC";
	$query = $db->query($querysql);
	while ($row = $db->fetch_array($query)) {
		$treelist[] = $row['classid'];
	}
	return $treelist;
}
/**
 * 菜单列表html
 * ***/
function getmenu($splitstr = '') {
	global $siteurl, $adminid;
	$treelist = display_tree();
	$arr = explode(',', $splitstr);
	if ($adminid && !empty ($_SESSION["allowstr"])) {
		$arradmin = explode(',', $_SESSION["allowstr"]);
	} else {
		$arradmin = display_treeadmin();
	}
	$i = 0;
	$selstr = '<li><input type=button value=全部选定 onclick=checkall(this.form)> <input type=button value=全部清空 onclick=checkother(this.form)></li>';
	foreach ($treelist as $key => $tl) {
		if ($tl['classid'] != '1' && in_array($tl['classid'], $arradmin)) {
			$checked = (in_array($tl['classid'], $arr)) ? "checked=checked" : "";
			if ($tl['parentid'] == '1') {
				if ($key > 1) {
					$selstr .= "</li></ul></li>";
				}
				$m = intval(($tl['rgt'] - $tl['lft']) / 2);
				$n = 'A' . $tl['classid'];
				unset ($i);
				$i = 0;
				$selstr .= "<li><input id='$n' type='checkbox' " . $checked . " name='allowstr[]' value=" . $tl['classid'] . " onclick=checker('$n','$m')  />" . $tl['classname'] . "<ul class=clearfix><li>";
			} else {
				$i++;
				$nm = $n . ($i -1);
				$selstr .= " <input id='$nm' " . $checked . " type=checkbox onclick=checkerd('$n') name='allowstr[]' value=" . $tl['classid'] . ">" . str_repeat("　", $tl['layer'] - 3) . $tl['classname'];
			}
		}
	}

	$selstr .= "</li></ul></li>";
	return $selstr;
}

/**
 * 菜单列表html
 * ***/
function getLeftTree($allowstr) {
	$treelist = display_tree();
	$selstr = '';
	$i = 0;
	$j = 0;
	$arr = explode(',', $allowstr);
	foreach ($treelist as $key => $tl) {
		if ($tl['classid'] != '1') {
			if (in_array($tl['classid'], $arr)) {
				if ($tl['parentid'] == '1') {
					$i++;
					$j = 0;
					if ($i > 1) {
						$selstr .= "</ul></div>";
					}
					$selstr .= "<div class=menu_title><strong onClick=show('config" . $key . "');>$tl[classname]</strong><ul id='config" . $key . "' style='display:;'>";
				} else {
					if ($j % 2 == 0) {
						$selstr .= "<li>&nbsp;";
						$selstr .= "<a href=" . $tl['url'] . " target=right>" . $tl['classname'] . "</a>";
					} else {
						$selstr .= " | <a href=" . $tl['url'] . " target=right>" . $tl['classname'] . "</a></li>";
					}
					$j++;
				}
			}
		}
	}
	if (!empty ($selstr)) {
		$selstr .= "</ul></div>";
	}
	return $selstr;
}
?>
