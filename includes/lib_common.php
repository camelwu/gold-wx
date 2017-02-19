<?php
/**
 * 公共函数库
 * $Author: shiyanqiu $
 * $Date: 2009/07 $
 * 
 */

//模拟请求数据 
function request($url,$postfields,$cookie_jar,$referer,$cookie=''){ 
	//$hFile =  FOpen( $sTxtfile, 'w' );
	$header = array(
		//'SOAPAction' 	=> 'http://messenger.live.com/ws/2006/09/oim/Store2',
		//'Content-Type' 	=> 'text/xml',
		'User-Agent' 	=> 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0',    
	 	//'Referer'    	=> 'http://www.163.com',
		//'Accept'    	=> 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
		//'Cache-Control' => 'max-age=0',
		//'Connection'    => 'keep-alive',
		//'Keep-Alive'    => '300',
		//'Accept-Charset'=> 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
		//'Accept-Language'=> 'en-us,en;q=0.5',
		//'Pragma'		=> '', //browsers keep this blank.
		//'Cookie'		=>	'HID=8c7094a020bc284d',
    );
	$ch = curl_init(); 
	$options = array(
		CURLOPT_URL => $url,
		CURLOPT_HEADER => 1, //设定是否显示头信息。	      
		//CURLOPT_NOBODY => 0, //设定是否输出页面内容
		//CURLOPT_PORT => 80,
		//CURLOPT_POST => 1, //这个POST是普通的 application/x-www-from-urlencoded 类型，多数被HTML表单使用
		//CURLOPT_POSTFIELDS => $postfields, //传递一个作为HTTP “POST”操作的所有数据的字符串。	      
		CURLOPT_RETURNTRANSFER => true, //设定返回的数据是否自动显示
		CURLOPT_FOLLOWLOCATION => 0,
		CURLOPT_MAXREDIRS => 5,
		//CURLOPT_COOKIEJAR => $cookie_jar, //把返回来的cookie信息保存在$cookie_jar文件中	      
		//CURLOPT_COOKIEFILE =>$cookie_jar,
		//CURLOPT_REFERER => $referer, //在HTTP请求中包含一个”referer”头的字符串
		CURLOPT_COOKIE => $cookie, //传递一个包含HTTP cookie的头连接
		CURLOPT_HTTPHEADER => $header,
		CURLOPT_CONNECTTIMEOUT => 120,
		//CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0",
		//CURLOPT_HEADERFUNCTION => 'xx',
		/*
		CURLOPT_TIMEOUT => 60, //最大延续多少秒
		CURLOPT_ENCODING => 'gzip,deflate', 
		CURLOPT_COOKIESESSION =>  true,//关闭连接时，将服务器端返回的cookie保存在以下文件中
		CURLOPT_FILE => $hFile, //上传输出文件
		CURLOPT_INFILE => $hFile, //下传输出文件
		*/
	);
	curl_setopt_array($ch, $options);
	$code = curl_exec($ch);
	$info = curl_getinfo($ch);	
	
	$response = parse_response($code);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$http_code = $response[0];
	//print_r($response);	
	if(empty($http_code)) {
		die("No HTTP code was returned");
	}elseif($http_code==200) {
		//echo('返回成功!<br>');
	}elseif($http_code==301 || $http_code==302){
		$Location = $response[1]['Location'];
        if(!empty($Location)) return request($Location,$postfields,$cookie_jar,$cookie);
		//echo("返回成功，页面被重定向:{$http_code}({$Location})<br><br>");
	}else{		
		echo("返回失败:{$http_code}");
	}	
	$codes = split("\r\n\r\n",$code);
	$code = $codes[1];
	curl_close($ch);
	return $code;
}

function parse_response($response){    
    /*   
    ***original code extracted from examples at   
    ***http://www.webreference.com/programming/php/cookbook/chap11/1/3.html   
 
    ***returns an array in the following format which varies depending on headers returned   
 
        [0] => the HTTP error or response code such as 404   
        [1] => Array   
        (   
            [Server] => Microsoft-IIS/5.0   
            [Date] => Wed, 28 Apr 2004 23:29:20 GMT   
            [X-Powered-By] => ASP.NET   
            [Connection] => close   
            [Set-Cookie] => COOKIESTUFF   
            [Expires] => Thu, 01 Dec 1994 16:00:00 GMT   
            [Content-Type] => text/html   
            [Content-Length] => 4040   
        )   
        [2] => Response body (string)   
*/    
  
    list($response_headers,$response_body) = explode("\r\n\r\n",$response,2);    
    $response_header_lines = explode("\r\n",$response_headers);    
  
    // first line of headers is the HTTP response code    
    $http_response_line = array_shift($response_header_lines);    
    if (preg_match('/^HTTP\/[0-9]\.[0-9a-z] ([0-9]{3})/i',$http_response_line,$matches)) {    
        $response_code = $matches[1];    
    }
    // put the rest of the headers in an array    
    $response_header_array = array();    
    foreach ($response_header_lines as $header_line) {    
        list($header,$value) = explode(': ',$header_line,2);    
        $response_header_array[$header] = $value;    
    }    
  
    return array($response_code,$response_header_array,$response_body);    
} 
 
function cut($file,$from,$end){
    $message=explode($from,$file); 
    $message=explode($end,$message[1]);
	return  $message[0];
}

function getAdUrl($code){ 
	preg_match('/<a href="(.*)" target="_blank" /',$code,$adUrl_tmp); 
	$adurl = $adUrl_tmp[1]; 
	return $adurl; 
} 

function myencode($pass){
	return md5(crypt($pass,substr($pass,0,2)));
}

function IsEmail($email)
{
	if(preg_match("/^[a-z]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/", $email)){ return TRUE; } else{ return FALSE; }
}


//兼容有前导零的ip，如058.99.011.1 http://blog.csdn.net/hjue/archive/2009/09/24/4587209.aspx
function myip2long($ip){   
   $ip_arr = split('\.',$ip);   
   $iplong = (16777216 * intval($ip_arr[0])) + (65536 * intval($ip_arr[1])) + (256 * intval($ip_arr[2])) + intval($ip_arr[3]);   
   return $iplong;   
} 

// 统计中字符串长度
function countStr($str, $handle, $onlyCh=0)
{
	//$handle = 1 按照mb_strlen算 一个中文按照1个字算
	//$handle = 2 按照mb_strwidth算 一个中文按照2个字算
	//$handle = 3 按照strlen算 一个中文按照3个字算
	//$onlyCh = 1 只统计中文
	$length = strlen($str);
	if ($handle == 3 && $onlyCh==0) {
		return $length;
	}else {
		$i = 0;
		if ($onlyCh == 1) { $k = 0; } else { $k = 1; }
		while($i < $length) {
			if(preg_match("/^[" . chr(0xa1) . "-" . chr(0xff) . "]+$/", $str[$i])) {
				$i += 3;
				$n += $handle;
			}
			else {
				$i += 1;
				$n += $k;
			}
		}
		return $n;
	}
}
function strReformat($str){
	//$str = strip_tags($str, '<p><a>');
	$str = str_replace("\r\n",HTTP_CRLF,$str);
	$str = str_replace("<br>",HTTP_CRLF,$str);
	$str = strip_tags($str);
	
	return $str;
}
function strformat0($str,$charset=''){
	if($charset=='' || $charset=='gb2312' ) $str = iconv("UTF-8","gb2312",$str);
	$str = str_replace("\r\n",'<br>',$str);
	$str = str_replace("\t\n",'',$str);
	$str = str_replace("\t",'',$str);
	$str = str_replace("\n",'',$str);
	//$str = str_replace("&",' ',$str);
	//$str = strstrip0($str);
	//$str = strip_tags($str);
	//$str = htmlspecialchars($str);
	//$str = htmlentities($str);
	return $str;
}

function strstrip0($str){
	$counts = substr_count($str,'"');
	$counts = $counts % 2;
	//if(is_odd($counts)) $str = addslashes($str);
	if(is_odd($counts)) $str = str_replace('"','',$str);
	return $str;
}

function strformat($str){
	$str = str_replace("\\r\\n",'<br>',$str);
	$str = str_replace(HTTP_CRLF,"<br>",$str);
	$str = str_replace(chr(10),"<br>",$str);
	//$str = str_replace("\t\n",'',$str);
	//$str = str_replace("\t",'',$str);
	//$str = str_replace("\n",'',$str);
	//$str = htmlentities($str);
	return $str;
}
function strstrip($str){
	$counts = substr_count($str,'"');
	$counts = $counts % 2;
	//if(is_odd($counts)) $str = addslashes($str);
	if(is_odd($counts)) $str = str_replace('"','',$str);
	return $str;
}
//判断奇数，是返回TRUE，否返回FALSE
function is_odd($num){ return (is_numeric($num)&($num&1)); }
 //判断偶数，是返回TRUE，否返回FALSE
function is_even($num){ return (is_numeric($num)&(!($num&1))); }
function get_file_ext($url){	
	$ext1 = basename($url); //方法1
	$ext2 = array_pop(explode(".", $url));   //方法2
	$ext3 = substr(strrchr($sourceFileName, "."),1); //方法3

	return $ext1;
}
function arrayToUrl($arr){	
	$result = '';
	foreach($arr as $key => $value){ $result .= "&{$key}={$value}"; }
	return $result;
}
/* 下载文件
 * 例：vWritePageToFile( 'http://60.28.13.230:8011/www/pic/poster/31.jpg', './tmp/31.jpg' );
 */
function vWritePageToFile( $sHTMLpage, $sTxtfile ) {
	$sh =          curl_init( $sHTMLpage );
	$hFile =       FOpen( $sTxtfile, 'w' );
	curl_setopt( $sh, CURLOPT_FILE, $hFile );
	curl_setopt( $sh, CURLOPT_HEADER, 0 );
	curl_exec  ( $sh );
	$sAverageSpeedDownload = curl_getInfo( $sh, CURLINFO_SPEED_DOWNLOAD );
	$sAverageSpeedUpload  = curl_getInfo( $sh, CURLINFO_SPEED_UPLOAD );
	echo '<pre>';
	echo 'Average speed download == ' . $sAverageSpeedDownload . '<br>';
	echo 'Average Speed upload    == ' . $sAverageSpeedUpload  . '<br>';
	echo '<br>';
	$aCURLinfo = curl_getInfo( $sh );
	print_r( $aCURLinfo );
	echo '</pre>';
	curl_close(  $sh );
	FClose    (  $hFile );
	echo '(<b>See the file  "'.$sTxtfile.'"  in the same path of the hosting to where this script PHP</b>).<br>';
}
function downfile($FileTruePath,$FileName=''){
	//$FileName = iconv("UTF-8","GBK",$FileName);
	//$FileTruePath = iconv("UTF-8","GBK",$FileTruePath);
	$http_pos = strpos('http://',$FileTruePath);
	if(!empty($http_pos)){
		if (DIRECTORY_SEPARATOR =='/')$FileTruePath = DIRECTORY_SEPARATOR.$FileTruePath;
		$FileTruePath = $_SERVER['DOCUMENT_ROOT'].$FileTruePath;
	}
	if($fp=@fopen($FileTruePath,"r")){
	//if(file_exists($FileTruePath)){
		//ob_start();
		//$fp = fopen($FileTruePath, 'rb');
		$fp = @ fopen($FileTruePath,"r");
		ob_end_clean();
		if(empty($FileName))$FileName = basename($FileTruePath);
		/*下载rar,jpg,gif,txt,xls 成功;中文名失败	*/	
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length: ".filesize($FileTruePath));
		//Header("Accept-Length: 102400");
		Header("Content-Disposition: attachment; filename=".$FileName);
		if(!empty($http_pos)){
		header("Content-Description: File Transfer");
		header("Content-Transfer-Encoding: binary ");
		}
		fpassthru($fp);
		//while (!feof ($fp)) { echo fread($fp,50000); }
		fclose ($fp);
		//ob_flush(); flush(); ob_end_flush();
		//die("文件存在 ={$FileTruePath}:{$FileName}:".filesize($FileTruePath));

		/*
		 header("Pragma: public");
		 header("Expires: 0");
		 header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		 header("Content-Type: application/force-download");
		 header("Content-Type: application/download");
		 header("Content-Disposition: attachment;filename=$FileName");
		 header("Content-Transfer-Encoding: binary ");
		 */
		/*
		 Header("Content-type:application/vnd.ms-excel;charset='utf8");
		 header("Content-Disposition:attachment;filename=$FileName");
		 */
		/*
		 header("Pragma: public");
		 header("Expires: 0");
		 header("Content-Type: application/force-download");
		 header("Content-Type: application/download");
		 header("Content-Disposition: attachment;filename=$FileName");
		 header("Content-Transfer-Encoding: binary ");//二进制
		 */
	}else{
		die("错误：文件不存在！{$FileTruePath}:{$FileName}:");
	}
}
function get_current_charset(){
	return $charset = (strlen('好')==2) ? 'gbk':'utf8';
}
function XML2Array ( $xml , $recursive = false )
{
    if ( ! $recursive )
    {
        $array = simplexml_load_string ( $xml, 'SimpleXMLElement', LIBXML_NOCDATA ) ;
    }
    else
    {
        $array = $xml ;
    }
    
    $newArray = array () ;
    $array = ( array ) $array ;
    foreach ( $array as $key => $value )
    {
        $value = ( array ) $value ;
        if ( isset ( $value [ 0 ] ) )
        {
            $newArray [ $key ] = trim ( $value [ 0 ] ) ;
        }
        else
        {
            $newArray [ $key ] = XML2Array ( $value , true ) ;
        }
    }
    return $newArray ;
}
//获取XML文件内容
function get_movie_xml($nodeName,$fileName=MOVIECONFIG_XML_FILE)
{

    if(empty($fileName)) $fileName = MOVIECONFIG_XML_FILE;
	//echo($fileName.'<br>');
	$xml = simplexml_load_file($fileName);
	$tmpArr = array();
	$i = 0;
	if($nodeName != ''){
		foreach ($xml->xpath("//{$nodeName}") as $node) {
			$tmpArr[$i]['id'] = (string) $node[@id] ;
			$tmpArr[$i]['value'] = (string) $node[@value] ;
			$i++;
		}
	}
//        echo "<pre>";print_r($tmpArr);exit;
	return $tmpArr;
}
function filterArrayBlank($var){ return ($var == '')? false:true; } 

function echoArray($arr,$info){
	$count = count($arr);
	if(!empty($arr)){ $msg = "<br>{$info}({$count})：".implode(",", $arr); }
	return $msg;
}
function prefixArr($data,$prefix){
	$arr = array();
	foreach ($data as $key => $value) {
		if(stristr($key,$prefix)){$arr[$key] = addslashes($value);}
	}
	return $arr;
}
function writeTofile($FileName,$FileContent)
{
	//$document_root = $_SERVER['DOCUMENT_ROOT'];
	if (!$fp=fopen($FileName,'w')) { echo "Can not open the file ".$FileName."<br>"; }
	
	fwrite($fp,$FileContent,strlen($FileContent));
	fclose($fp);
	return true;
}

function MD5Hash($str) // 0.050
{
	$hash = md5($str);
	$hash = $hash[0] | ($hash[1] <<8 ) | ($hash[2] <<16) | ($hash[3] <<24) | ($hash[4] <<32) | ($hash[5] <<40) | ($hash[6] <<48) | ($hash[7] <<56);
	$hash = $hash % 701819;
	
	return $hash%50;
}

function createFolder($path)
{
   if (!file_exists($path))
   {
    	createFolder(dirname($path));
    	mkdir($path, 0777);
   }
}

function asc2bin ($temp) { 
  $len = strlen($temp); 
  for ($i=0; $i<$len; $i++) $data.=sprintf("%08b",ord(substr($temp,$i,1))); 
  return $data; 
}
function getRandKey()
{
	$rand = getRandom(20);
	$ip = real_ip();
	$timestamp = gmtime();
	$mac = new GetMacAddr(PHP_OS);
	$macAddress = $mac->mac_addr;
	$md51 = md5($rand.$ip.$timestamp);
	$md52 = md5($rand.$macAddress.$timestamp);	
	return $md51.$md52;
}
/*
 * 功能：生成（随机数）
 */
function getRandom($length)
{
	$hash = '';
	$chars = 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
	$max = strlen($chars) - 1;
	//mt_srand((double)microtime() * 1000000);
	for($i = 0; $i < $length; $i++) { $hash .= $chars[mt_rand(0, $max)]; }
	return $hash;
}

/**
 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 +----------------------------------------------------------
 * @param string $len 长度 
 * @param string $type 字串类型 
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符 
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function rand_string($len=6,$type='',$addChars='') {
	$str ='';
	switch($type) {
		case 0:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars; break;
		case 1:
			$chars= str_repeat('0123456789',3); break;
		case 2:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars; break;
		case 3:
			$chars='abcdefghijklmnopqrstuvwxyz'.$addChars; break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars; break;
	}
	if($len>10 ) {//位数过长重复字符串一定次数
		$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
	}
	if($type!=4) {
		$chars   =   str_shuffle($chars);
		$str     =   substr($chars,0,$len);
	}else{
		// 中文随机字
		for($i=0;$i<$len;$i++){
			$str.= substr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
		}
	}
	return $str;
}

//生成唯一标识符
function create_unique() {
	$data = $_SERVER['HTTP_USER_AGENT'].'-'. $_SERVER['REMOTE_ADDR'].'-'.time().'-'. rand();
	return sha1($data);
	//return md5(time().$data);
}

function uniDecode($str,$charcode){
	$text = preg_replace_callback("/%u[0-9A-Za-z]{4}/",toUtf8,$str);
	return mb_convert_encoding($text, $charcode, 'utf-8');
}
function toUtf8($ar){
	foreach($ar as $val){
		$val = intval(substr($val,2),16);
		if($val < 0x7F){        // 0000-007F
			$c .= chr($val);
		}elseif($val < 0x800) { // 0080-0800
			$c .= chr(0xC0 | ($val / 64));
			$c .= chr(0x80 | ($val % 64));
		}else{                // 0800-FFFF
			$c .= chr(0xE0 | (($val / 64) / 64));
			$c .= chr(0x80 | (($val / 64) % 64));
			$c .= chr(0x80 | ($val % 64));
		}
	}
	return $c;
}

//// 时间相关begin ////////////////////////////////////////////////////////////////////////////////
function exceltime($time){
	$jd1900 = GregorianToJD(1, 1, 1900)-2;
	$myJd = $time+$jd1900;
	$myDate = JDToGregorian($myJd);
	$myDate = explode('/',$myDate);
	$myDateStr = str_pad($myDate[2],4,'0', STR_PAD_LEFT)."-".str_pad($myDate[0],2,'0', STR_PAD_LEFT)."-".str_pad($myDate[1],2,'0', STR_PAD_LEFT)." 00:00:00";

	return $myDateStr;
}
/**
 * 获得当前格林威治时间的时间戳
 * @return  integer
 */
function gmtime(){ return time() - date("Z"); }

/**
 * 获得服务器的时区
 *
 * @return  integer
 */
function server_timezone()
{
    if (function_exists('date_default_timezone_get'))
    {
        return date_default_timezone_get();
    }
    else
    {
        return date("Z") / 3600;
    }
}


/**
 *  生成一个用户自定义时区日期的GMT时间戳
 *
 * @access  public
 * @param   int     $hour
 * @param   int     $minute
 * @param   int     $second
 * @param   int     $month
 * @param   int     $day
 * @param   int     $year
 *
 * @return void
 */
function local_mktime($hour = NULL , $minute= NULL, $second = NULL,  $month = NULL,  $day = NULL,  $year = NULL)
{
    $timezone = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : $GLOBALS['_CFG']['timezone'];
    /**
    * $time = mktime($hour, $minute, $second, $month, $day, $year) - date('Z') + (date('Z') - $timezone * 3600)
    * 先用mktime生成时间戳，再减去date('Z')转换为GMT时间，然后修正为用户自定义时间。以下是化简后结果
    **/
    $time = mktime($hour, $minute, $second, $month, $day, $year) - $timezone * 3600;
    return $time;
}


/**
 * 将GMT时间戳格式化为用户自定义时区日期
 *
 * @param  string       $format
 * @param  integer      $time       该参数必须是一个GMT的时间戳
 *
 * @return  string
 */

function local_date($format, $time = NULL)
{
    $timezone = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : $GLOBALS['_CFG']['timezone'];
    if ($time === NULL) { $time = gmtime(); } elseif ($time <= 0) { return ''; }
    $time += ($timezone * 3600);
    return date($format, $time);
}


/**
 * 转换字符串形式的时间表达式为GMT时间戳
 *
 * @param   string  $str
 *
 * @return  integer
 */
function gmstr2time($str)
{
    $time = strtotime($str);
    if ($time > 0) { $time -= date('Z'); }
    return $time;
}

/**
 *  将一个用户自定义时区的日期转为GMT时间戳
 *
 * @access  public
 * @param   string      $str
 *
 * @return  integer
 */
function local_strtotime($str)
{
    $timezone = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : $GLOBALS['_CFG']['timezone'];
    /**
    * $time = mktime($hour, $minute, $second, $month, $day, $year) - date('Z') + (date('Z') - $timezone * 3600)
    * 先用mktime生成时间戳，再减去date('Z')转换为GMT时间，然后修正为用户自定义时间。以下是化简后结果
    **/
    $time = strtotime($str) - $timezone * 3600;
    return $time;
}

/**
 * 获得用户所在时区指定的时间戳
 *
 * @param   $timestamp  integer     该时间戳必须是一个服务器本地的时间戳
 *
 * @return  array
 */
function local_gettime($timestamp = NULL)
{
    $tmp = local_getdate($timestamp);
    return $tmp[0];
}

/**
 * 获得用户所在时区指定的日期和时间信息
 *
 * @param   $timestamp  integer     该时间戳必须是一个服务器本地的时间戳
 *
 * @return  array
 */
function local_getdate($timestamp = NULL)
{
    $timezone = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : $GLOBALS['_CFG']['timezone'];
    /* 如果时间戳为空，则获得服务器的当前时间 */
    if ($timestamp === NULL){ $timestamp = time(); }
    $gmt        = $timestamp - date('Z');       // 得到该时间的格林威治时间
    $local_time = $gmt + ($timezone * 3600);    // 转换为用户所在时区的时间戳
    return getdate($local_time);
}
//// 时间相关end ////////////////////////////////////////////////////////////////////////////////
/**
 * 创建一个JSON格式的数据
 *
 * @access  public
 * @param   string      $content
 * @param   integer     $error
 * @param   string      $message
 * @param   array       $append
 * @return  void
 */
function make_json_response($content='', $error="0", $message='', $append=array())
{
   // include_once(ROOT_PATH.'includes/cls_json.php');
   // $json = new JSON;
    $res = array('error' => $error, 'message' => $message, 'content' => $content);

    if (!empty($append))
    {
        foreach ($append AS $key => $val) { $res[$key] = $val; }
    }
    $val = $json->encode($res);
    exit($val);
}

/**
 *
 *
 * @access  public
 * @param
 * @return  void
 */
function make_json_result($content, $message='', $append=array())
{ make_json_response($content, 0, $message, $append); }

/**
 * 创建一个JSON格式的错误信息
 *
 * @access  public
 * @param   string  $msg
 * @return  void
 */
function make_json_error($msg){ make_json_response('', 1, $msg); }
/**
 * 分页的信息加入条件的数组
 *
 * @access  public
 * @return  array
 */
function page_and_size($filter)
{
    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    { 
    	$filter['page_size'] = intval($_REQUEST['page_size']); 
    }
    elseif (isset($_COOKIE['VRM']['page_size']) && intval($_COOKIE['VRM']['page_size']) > 0)
    { $filter['page_size'] = intval($_COOKIE['VRM']['page_size']); }
    else
    { $filter['page_size'] = 50; }
	//$filter['page_size'] = 8;
    /* 每页显示 */
    $filter['page'] = (empty($_REQUEST['page']) || intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);
    /* page 总数 */
    $filter['page_count'] = (!empty($filter['record_count']) && $filter['record_count'] > 0) ? ceil($filter['record_count'] / $filter['page_size']) : 1;
    /* 边界处理 */
    if ($filter['page'] > $filter['page_count'])
    {
        $filter['page'] = $filter['page_count'];
    }
    $filter['start'] = ($filter['page'] - 1) * $filter['page_size'];
	//计算分页排列字符串
	$page_count = $filter['page_count'];
	$page = $filter['page'];
	if($page_count>10){
		//$pageStr_start = floor($page/10)*10+1;
		//$pageStr_end = $pageStr_start + 9;
		$pageStr_start = $page-5;		
		$pageStr_start = ($pageStr_start<1) ? 1 : $pageStr_start;
		$pageStr_end = $pageStr_start + 9 ;
		$pageStr_end =  ($pageStr_end>$page_count) ? $page_count : $pageStr_end;		
	}else{
		$pageStr_start = 1;
		$pageStr_end = $page_count;
	}
	$filter['pageStr'] = range($pageStr_start,$pageStr_end);
	setcookie('current_page',  $filter['page'], time() + 600);
    return $filter;
}
/**
 * 保存过滤条件
 * @param   array   $filter     过滤条件
 * @param   string  $sql        查询语句
 * @param   string  $param_str  参数字符串，由list函数的参数组成
 */
function set_filter($filter, $sql, $param_str = '')
{
    $filterfile = basename($_SERVER['PHP_SELF'], '.php');
    if ($param_str) { $filterfile .= $param_str; }
    setcookie('VRM[lastfilterfile]', sprintf('%X', crc32($filterfile)), time() + 600);
    setcookie('VRM[lastfilter]',     urlencode(serialize($filter)), time() + 600);
    setcookie('VRM[lastfiltersql]',  urlencode($sql), time() + 600);
}

/**
 * 取得上次的过滤条件
 * @param   string  $param_str  参数字符串，由list函数的参数组成
 * @return  如果有，返回array('filter' => $filter, 'sql' => $sql)；否则返回false
 */
function get_filter($param_str = '')
{
    $filterfile = basename($_SERVER['PHP_SELF'], '.php');
    if ($param_str)
    {
        $filterfile .= $param_str;
    }
    if (isset($_GET['uselastfilter']) && isset($_COOKIE['VRM']['lastfilterfile'])
        && $_COOKIE['VRM']['lastfilterfile'] == sprintf('%X', crc32($filterfile)))
    {
        return array(
            'filter' => unserialize(urldecode($_COOKIE['VRM']['lastfilter'])),
            'sql'    => urldecode($_COOKIE['VRM']['lastfiltersql'])
        );
    }
    else
    {
        return false;
    }
}
/**
 * 判断管理员权限。
 * @return true/false
 */
function admin_priv()
{
    if(empty($_SESSION['admin_id']) || !isset($_SESSION['admin_name'])) {
    	header("Location: ./admins_login.php");
    	return false;
    } else {
        return true;
    }
}

function isLocalIP(){
	$ip = myip2long(real_ip());
	$ip_local1 = myip2long("127.0.0.1");
	$ip_local2_beg = myip2long("10.10.1.1");
	$ip_local2_end = myip2long("10.10.200.1");
	$ip_local3 = myip2long("202.106.92.98");
	//die("ip:{$ip}| ip_local1:{$ip_local1} | ip_local_beg:{$ip_local2_beg}| ip_local_end:{$ip_local2_end}");
	if(($ip==$ip_local1)||($ip >$ip_local2_beg && $ip<$ip_local2_end)||$ip==$ip_local3) return true; 
	else return false;
}
/**
 * 生成编辑器
 * @param   string  input_name  输入框名称
 * @param   string  input_value 输入框值
 */
function create_html_editor($input_name, $input_value = 'testttt')
{
	global $smarty;
	$sBasePath = "/includes/fckeditor/";
	$editor = new FCKeditor($input_name) ;
	$editor->BasePath	= $sBasePath ;
	//$editor->ToolbarSet = 'Normal';
    $editor->Width      = '90%';
    $editor->Height     = '280';
	$editor->Value		= $input_value ;
	$FCKeditor = $editor->CreateHtml() ;

	$smarty->assign('FCKeditor', $FCKeditor);
}

function iconv_str($source_string = '',$source_lang='UTF-8', $target_lang='GBK')
{
	if(function_exists("iconv")) { 
		return iconv($source_lang,$target_lang,$source_string); 
	}
	else{ return $source_string; }
}

/**
 * 系统提示信息
 *
 * @access      public
 * @param       string      msg_detail      消息内容
 * @param       int         msg_type        消息类型， 0消息，1错误，2询问
 * @param       array       links           可选的链接
 * @param       boolen      $auto_redirect  是否需要自动跳转
 * @param       int      	$auto_second  自动跳转时间
 * @return      void
 */
function sys_msg($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = true,$auto_second=5)
{
    if (count($links) == 0)
    {
    	$links[0]['text'] = '返回上一页';
        $links[0]['href'] = 'javascript:history.back();window.location.reload();';
        //$links[0]['href'] = 'javascript:history.go(-1)';        
    }
    $lang_auto_redirection = sprintf($GLOBALS['_LANG']['auto_redirection'],$auto_second);

    $GLOBALS['smarty']->assign('ur_here',     "系统提示");
    $GLOBALS['smarty']->assign('msg_detail',  $msg_detail);
    $GLOBALS['smarty']->assign('msg',  		  $msg_detail);
    $GLOBALS['smarty']->assign('msg_type',    $msg_type);
    $GLOBALS['smarty']->assign('links',       $links);
    $GLOBALS['smarty']->assign('default_url', $links[0]['href']);
    $GLOBALS['smarty']->assign('auto_redirect', $auto_redirect);
    $GLOBALS['smarty']->assign('auto_second', $auto_second);
    $GLOBALS['smarty']->assign('lang_auto_redirection', $lang_auto_redirection);

    $GLOBALS['smarty']->display('message.htm');
    exit;
}

/**
     * 获得 当前环境的 HTTP 协议方式
     *
     * @access  public
     *
     * @return  void
*/
function http()
{
	return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
}
/**
     * 取得当前的域名
     *
     * @access  public
     *
     * @return  string      当前的域名
*/
function get_domain()
{
	/* 协议 */
        $protocol = http();

        /* 域名或IP地址 */
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
        {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        }
        elseif (isset($_SERVER['HTTP_HOST']))
        {
            $host = $_SERVER['HTTP_HOST'];
        }
        else
        {
            /* 端口 */
            if (isset($_SERVER['SERVER_PORT']))
            {
                $port = ':' . $_SERVER['SERVER_PORT'];

                if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol))
                {
                    $port = '';
                }
            }
            else
            {
                $port = '';
            }

            if (isset($_SERVER['SERVER_NAME']))
            {
                $host = $_SERVER['SERVER_NAME'] . $port;
            }
            elseif (isset($_SERVER['SERVER_ADDR']))
            {
                $host = $_SERVER['SERVER_ADDR'] . $port;
            }
        }

        return $protocol . $host;
}

/**
 *  将含有单位的数字转成字节
 *
 * @access  public
 * @param   string      $val        带单位的数字
 *
 * @return  int         $val
 */
function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last)
    {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}
/**
 * 截取UTF-8编码下字符串的函数
 *
 * @param   string      $str        被截取的字符串
 * @param   int         $length     截取的长度
 * @param   bool        $append     是否附加省略号
 *
 * @return  string
 */
function sub_str($str, $length = 0, $append = true)
{
    $str = trim($str);
    $strlength = strlen($str);

    if ($length == 0 || $length >= $strlength)
    {
        return $str;
    }
    elseif ($length < 0)
    {
        $length = $strlength + $length;
        if ($length < 0)
        {
            $length = $strlength;
        }
    }

    if (function_exists('mb_substr'))
    {
        $newstr = mb_substr($str, 0, $length, 'UTF-8');
    }
    elseif (function_exists('iconv_substr'))
    {
        $newstr = iconv_substr($str, 0, $length, 'UTF-8');
    }
    else
    {
        $newstr = trim_right(substr($str, 0, $length));
    }

    if ($append && $str != $newstr)
    {
        $newstr .= '...';
    }

    return $newstr;
}

/**
 * 去除字符串右侧可能出现的乱码
 *
 * @param   string      $str        字符串
 *
 * @return  string
 */
function trim_right($str)
{
    $length = strlen(preg_replace('/[\x00-\x7F]+/', '', $str)) % 3;
    if ($length > 0) { $str = substr($str, 0, 0 - $length); }
    return $str;
}

/**
 * 计算字符串的长度（汉字按照两个字符计算）
 *
 * @param   string      $str        字符串
 *
 * @return  int
 */
function str_len($str)
{
    $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));

    if ($length) { return strlen($str) - $length + intval($length / 3) * 2; }
    else { return strlen($str); }
}

/**
 * 获得用户操作系统的换行符
 *
 * @access  public
 * @return  string
 */
function get_crlf()
{
/* LF (Line Feed, 0x0A, \N) 和 CR(Carriage Return, 0x0D, \R) */
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'Win')) { $the_crlf = '\r\n'; }
    elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'Mac')) { $the_crlf = '\r'; } // for old MAC OS 
    else { $the_crlf = '\n'; }

    return $the_crlf;
}

/**
 * 创建像这样的查询: "IN('a','b')";
 *
 * @access   public
 * @param    mix      $item_list      列表数组或字符串
 * @param    string   $field_name     字段名称
 * @author   Xuan Yan
 *
 * @return   void
 */
function db_create_in($item_list, $field_name = '',$toInt = false)
{
    if (empty($item_list))
    {
        return $field_name . " IN ('') ";
    }
    else
    {
        if (!is_array($item_list))
        {
            $item_list = explode(',', $item_list);
        }
        $item_list = array_unique($item_list);
        $item_list_tmp = '';

        foreach ($item_list AS $item)
        {
        	if ($item !== '')
            {
            	if($toInt){
            	   $item_list_tmp .= $item_list_tmp ? ",$item" : "$item";
            	}else{
                   $item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
            	}
            }
        }
        if (empty($item_list_tmp))
        {
            return $field_name . " IN ('') ";
        }
        else
        {
            return $field_name . ' IN (' . $item_list_tmp . ') ';
        }
    }
}

/**
 * 获得用户的真实IP地址
 *
 * @access  public
 * @return  string
 */
function real_ip()
{
    static $realip = NULL;
    if ($realip !== NULL) { return $realip; }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr AS $ip)
            {
                $ip = trim($ip);
                if ($ip != 'unknown') { $realip = $ip; break; }
            }
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP']))
        { $realip = $_SERVER['HTTP_CLIENT_IP']; }
        else
        {
            if (isset($_SERVER['REMOTE_ADDR'])) { $realip = $_SERVER['REMOTE_ADDR']; }
            else { $realip = '0.0.0.0'; }
        }
    }
    else
    {
        if (getenv('HTTP_X_FORWARDED_FOR')) { $realip = getenv('HTTP_X_FORWARDED_FOR'); }
        elseif (getenv('HTTP_CLIENT_IP')) { $realip = getenv('HTTP_CLIENT_IP'); }
        else { $realip = getenv('REMOTE_ADDR'); }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

    return $realip;
}

/**
 * 验证输入的邮件地址是否合法
 *
 * @access  public
 * @param   string      $email      需要验证的邮件地址
 *
 * @return bool
 */
function is_email($user_email)
{
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false)
    {
        if (preg_match($chars, $user_email)){return true;} else {return false;}
    } 
    else{ return false;}
}

/**
 * 检查是否为一个合法的时间格式
 *
 * @access  public
 * @param   string  $time
 * @return  void
 */
function is_time($time)
{
    $pattern = '/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/';
    return preg_match($pattern, $time);
}

/**
 * 获得查询时间和次数，并赋值给smarty
 *
 * @access  public
 * @return  void
 */
function assign_query_info()
{
    if ($GLOBALS['db']->queryTime == '')
    {
        $query_time = 0;
    }
    else
    {
        if (PHP_VERSION >= '5.0.0')
        {
            $query_time = number_format(microtime(true) - $GLOBALS['db']->queryTime, 6);
        }
        else
        {
            list($now_usec, $now_sec)     = explode(' ', microtime());
            list($start_usec, $start_sec) = explode(' ', $GLOBALS['db']->queryTime);
            $query_time = number_format(($now_sec - $start_sec) + ($now_usec - $start_usec), 6);
        }
    }

    $GLOBALS['smarty']->assign('query_info', sprintf('共执行 %d 个查询，用时 %s 秒', $GLOBALS['db']->queryCount, $query_time, 0));
	
    /* 内存占用情况 */
    if ('，内存占用 %0.3f MB' && function_exists('memory_get_usage'))
    {
        $GLOBALS['smarty']->assign('memory_info', sprintf('，内存占用 %0.3f MB', memory_get_usage() / 1048576));
    }
    /* 是否启用了 gzip */
    $gzip_enabled = gzip_enabled() ? '，Gzip 已启用' : '，Gzip 已禁用';
    $GLOBALS['smarty']->assign('gzip_enabled', $gzip_enabled);
}

/**
 * 创建地区的返回信息
 *
 * @access  public
 * @param   array   $arr    地区数组 *
 * @return  void
 */
function region_result($parent, $sel_name, $type)
{
    global $cp;

    $arr = get_regions($type, $parent);
    foreach ($arr AS $v)
    {
        $region      =& $cp->add_node('region');
        $region_id   =& $region->add_node('id');
        $region_name =& $region->add_node('name');

        $region_id->set_data($v['region_id']);
        $region_name->set_data($v['region_name']);
    }
    $select_obj =& $cp->add_node('select');
    $select_obj->set_data($sel_name);
}
/**
 * 获得服务器上的 GD 版本
 *
 * @access      public
 * @return      int         可能的值为0，1，2
 */
function gd_version()
{
    include_once(ROOT_PATH . 'includes/cls_image.php');
    return cls_image::gd_version();
}

/**
 * 载入配置信息
 *
 * @access  public
 * @return  array
 */
function load_config()
{
    global $db;
    $prefix = "DEF_"; //define的前缀

    $config_init_variable_global = array('AllowedExtensions', 'FtpConfig', 'account_pubnetwork_access'); //特殊定义的全局变量
    $config_init_variable_define = array();
    foreach($config_init_variable_global as $v) global $$v;
    
    $sql = "SELECT s.* FROM vrm_settings s";
    $row = $db->getAll($sql);

    //print_r($row);exit;
    foreach($row as $k => $v){
        if(strpos($v['variable'], $prefix) === false){
            global $$v['variable'];
            $$v['variable']=$v['value'];
            $arr_ck[$v['variable']]=$v['value'];
        }else {
            $def_init_str = str_replace($prefix, '', $v['variable']);
            define($def_init_str, $v['value']);
        }
    }

    //除以下变量序列化外，其他生成全局变量直接使用
    $FtpConfig          = unserialize($ftp_sets);
    $AllowedExtensions  = unserialize($files_pics_set);
    $account_pubnetwork_access = unserialize($account_pubnetwork_access);
        
    $arr = array();
    if (!isset($GLOBALS['_CFG']['vrm_version']))
    {
        /* 如果没有版本号则默认为 beta 1.0.1 */
        $GLOBALS['_CFG']['vrm_version'] = 'beta 1.0.1';
    }
    if (empty($arr['lang'])){ $arr['lang'] = 'zh_cn'; }
    return $arr;
}

/**
 * 邮件发送
 *
 * @param: $name[string]        接收人姓名
 * @param: $email[string]       接收人邮件地址
 * @param: $subject[string]     邮件标题
 * @param: $content[string]     邮件内容
 * @param: $type[int]           0 普通邮件， 1 HTML邮件
 * @param: $notification[bool]  true 要求回执， false 不用回执
 *
 * @return boolean
 */

function send_mail($name, $email, $subject, $content, $type = 0, $notification=false)
{
    /* 如果邮件编码不是utf8，创建字符集转换对象，转换编码 */
    if ($GLOBALS['_CFG']['mail_charset'] != 'UTF8')
    {
        $name      = ecs_iconv('UTF8', $GLOBALS['_CFG']['mail_charset'], $name);
        $subject   = ecs_iconv('UTF8', $GLOBALS['_CFG']['mail_charset'], $subject);
        $content   = ecs_iconv('UTF8', $GLOBALS['_CFG']['mail_charset'], $content);
        $GLOBALS['_CFG']['shop_name'] = ecs_iconv('UTF8', $GLOBALS['_CFG']['mail_charset'], $GLOBALS['_CFG']['shop_name']);
        $charset   = $GLOBALS['_CFG']['mail_charset'];
    }
    else
    {
        $charset = 'UTF-8';
    }

    /**
     * 使用mail函数发送邮件
     */
    if ($GLOBALS['_CFG']['mail_service'] == 0 && function_exists('mail'))
    {
        /* 邮件的头部信息 */
        $content_type = ($type == 0) ?
            'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;


        $headers = array();
        $headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($GLOBALS['_CFG']['shop_name']) . '?='.'" <' . $GLOBALS['_CFG']['smtp_mail'] . '>';
        $headers[] = $content_type . '; format=flowed';
        if ($notification)
        {
            $headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($GLOBALS['_CFG']['shop_name']) . '?='.'" <' . $GLOBALS['_CFG']['smtp_mail'] . '>';
        }

        $res = @mail($email, '=?' . $charset . '?B?' . base64_encode($subject) . '?=', $content, implode("\r\n", $headers));

        if (!$res)
        {
            $GLOBALS['err'] ->add($GLOBALS['_LANG']['sendemail_false']);
            return false;
        }
        else
        {
            return true;
        }
    }
    /**
     * 使用smtp服务发送邮件
     */
    else
    {
        /* 邮件的头部信息 */
        $content_type = ($type == 0) ?
            'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
        $content   =  base64_encode($content);

        $headers = array();
        $headers[] = 'Date: ' . gmdate('D, j M Y H:i:s') . ' +0000';
        $headers[] = 'To: "' . '=?' . $charset . '?B?' . base64_encode($name) . '?=' . '" <' . $email. '>';
        $headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($GLOBALS['_CFG']['shop_name']) . '?='.'" <' . $GLOBALS['_CFG']['smtp_mail'] . '>';
        $headers[] = 'Subject: ' . '=?' . $charset . '?B?' . base64_encode($subject) . '?=';
        $headers[] = $content_type . '; format=flowed';
        $headers[] = 'Content-Transfer-Encoding: base64';
        $headers[] = 'Content-Disposition: inline';
        if ($notification)
        {
            $headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($GLOBALS['_CFG']['shop_name']) . '?='.'" <' . $GLOBALS['_CFG']['smtp_mail'] . '>';
        }

        /* 获得邮件服务器的参数设置 */
        $params['host'] = $GLOBALS['_CFG']['smtp_host'];
        $params['port'] = $GLOBALS['_CFG']['smtp_port'];
        $params['user'] = $GLOBALS['_CFG']['smtp_user'];
        $params['pass'] = $GLOBALS['_CFG']['smtp_pass'];

        if (empty($params['host']) || empty($params['port']))
        {
            // 如果没有设置主机和端口直接返回 false
            $GLOBALS['err'] ->add($GLOBALS['_LANG']['smtp_setting_error']);
            return false;
        }
        else
        {
            // 发送邮件
            if (!function_exists('fsockopen'))
            {
                //如果fsockopen被禁用，直接返回
                $GLOBALS['err']->add($GLOBALS['_LANG']['disabled_fsockopen']);
                return false;
            }

            include_once(ROOT_PATH . 'includes/cls_smtp.php');
            static $smtp;

            $send_params['recipients'] = $email;
            $send_params['headers']    = $headers;
            $send_params['from']       = $GLOBALS['_CFG']['smtp_mail'];
            $send_params['body']       = $content;

            if (!isset($smtp))
            {
                $smtp = new smtp($params);
            }

            if ($smtp->connect() && $smtp->send($send_params))
            {
                return true;
            }
            else
            {
                $err_msg = $smtp->error_msg();
                if (empty($err_msg))
                {
                    $GLOBALS['err']->add('Unknown Error');
                }
                else
                {
                    if (strpos($err_msg, 'Failed to connect to server') !== false)
                    {
                        $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['smtp_connect_failure'], $params['host'] . ':' . $params['port']));
                    }
                    else if (strpos($err_msg, 'AUTH command failed') !== false)
                    {
                        $GLOBALS['err']->add($GLOBALS['_LANG']['smtp_login_failure']);
                    }
                    elseif (strpos($err_msg, 'bad sequence of commands') !== false)
                    {
                        $GLOBALS['err']->add($GLOBALS['_LANG']['smtp_refuse']);
                    }
                    else
                    {
                        $GLOBALS['err']->add($err_msg);
                    }
                }
                return false;
            }
        }
    }
}
/**
 * 文件或目录权限检查函数
 *
 * @access          public
 * @param           string  $file_path   文件路径
 * @param           bool    $rename_prv  是否在检查修改权限时检查执行rename()函数的权限
 *
 * @return          int     返回值的取值范围为{0 <= x <= 15}，每个值表示的含义可由四位二进制数组合推出。
 *                          返回值在二进制计数法中，四位由高到低分别代表
 *                          可执行rename()函数权限、可对文件追加内容权限、可写入文件权限、可读取文件权限。
 */
function file_mode_info($file_path)
{
    /* 如果不存在，则不可读、不可写、不可改 */
    if (!file_exists($file_path)) { return false; }

    $mark = 0;
    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
    {
        /* 测试文件 */
        $test_file = $file_path . '/cf_test.txt';
        /* 如果是目录 */
        if (is_dir($file_path))
        {
            /* 检查目录是否可读 */
            $dir = @opendir($file_path);
            if ($dir === false)
            {
                return $mark; //如果目录打开失败，直接返回目录不可修改、不可写、不可读
            }
            if (@readdir($dir) !== false)
            {
                $mark ^= 1; //目录可读 001，目录不可读 000
            }
            @closedir($dir);

            /* 检查目录是否可写 */
            $fp = @fopen($test_file, 'wb');
            if ($fp === false)
            {
                return $mark; //如果目录中的文件创建失败，返回不可写。
            }
            if (@fwrite($fp, 'directory access testing.') !== false)
            {
                $mark ^= 2; //目录可写可读011，目录可写不可读 010
            }
            @fclose($fp);
            @unlink($test_file);

            /* 检查目录是否可修改 */
            $fp = @fopen($test_file, 'ab+');
            if ($fp === false)
            {
                return $mark;
            }
            if (@fwrite($fp, "modify test.\r\n") !== false)
            {
                $mark ^= 4;
            }
            @fclose($fp);

            /* 检查目录下是否有执行rename()函数的权限 */
            if (@rename($test_file, $test_file) !== false)
            {
                $mark ^= 8;
            }
            @unlink($test_file);
        }
        /* 如果是文件 */
        elseif (is_file($file_path))
        {
            /* 以读方式打开 */
            $fp = @fopen($file_path, 'rb');
            if ($fp)
            {
                $mark ^= 1; //可读 001
            }
            @fclose($fp);

            /* 试着修改文件 */
            $fp = @fopen($file_path, 'ab+');
            if ($fp && @fwrite($fp, '') !== false)
            {
                $mark ^= 6; //可修改可写可读 111，不可修改可写可读011...
            }
            @fclose($fp);

            /* 检查目录下是否有执行rename()函数的权限 */
            if (@rename($test_file, $test_file) !== false)
            {
                $mark ^= 8;
            }
        }
    }
    else
    {
        if (@is_readable($file_path))
        {
            $mark ^= 1;
        }

        if (@is_writable($file_path))
        {
            $mark ^= 14;
        }
    }

    return $mark;
}

function log_write($arg, $file = '', $line = '')
{
    if ((DEBUG_MODE & 4) != 4) { return; }

    $str = "\r\n-- ". date('Y-m-d H:i:s'). " --------------------------------------------------------------\r\n";
    $str .= "FILE: $file\r\nLINE: $line\r\n";

    if (is_array($arg))
    {
        $str .= '$arg = array(';
        foreach ($arg AS $val)
        {
            foreach ($val AS $key => $list)
            {
                $str .= "'$key' => '$list'\r\n";
            }
        }
        $str .= ")\r\n";
    }
    else
    {
        $str .= $arg;
    }

    file_put_contents(ROOT_PATH . 'data/log.txt', $str);
}
/**
 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
 *
 * @access      public
 * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
 *
 * @return      bool
 */
function make_dir($folder)
{
    $reval = false;

    if (!file_exists($folder))
    {
        /* 如果目录不存在则尝试创建该目录 */
        @umask(0);

        /* 将目录路径拆分成数组 */
        preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

        /* 如果第一个字符为/则当作物理路径处理 */
        $base = ($atmp[0][0] == '/') ? '/' : '';

        /* 遍历包含路径信息的数组 */
        foreach ($atmp[1] AS $val)
        {
            if ('' != $val)
            {
                $base .= $val;

                if ('..' == $val || '.' == $val)
                {
                    /* 如果目录为.或者..则直接补/继续下一个循环 */
                    $base .= '/';
                    continue;
                }
            }
            else
            {
                continue;
            }

            $base .= '/';

            if (!file_exists($base))
            {
                /* 尝试创建目录，如果创建失败则继续循环 */
                if (@mkdir($base, 0777))
                {
                    @chmod($base, 0777);
                    $reval = true;
                }
            }
        }
    }
    else
    {
        /* 路径已经存在。返回该路径是不是一个目录 */
        $reval = is_dir($folder);
    }

    clearstatcache();

    return $reval;
}

/**
 * 获得系统是否启用了 gzip
 *
 * @access  public
 *
 * @return  boolean
 */
function gzip_enabled()
{
    static $enabled_gzip = NULL;

    if ($enabled_gzip === NULL)
    {
        $enabled_gzip = (function_exists('ob_gzhandler'));
    }

    return $enabled_gzip;
}

/**
 * 递归方式的对变量中的特殊字符进行转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function addslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('addslashes_deep', $value) : daddslashes($value);
    }
}

/**
 * 将对象成员变量或者数组的特殊字符进行转义
 *
 * @access   public
 * @param    mix        $obj      对象或者数组
 * @author   Xuan Yan
 *
 * @return   mix                  对象或者数组
 */
function addslashes_deep_obj($obj)
{
    if (is_object($obj) == true)
    {
        foreach ($obj AS $key => $val)
        {
            $obj->$key = addslashes_deep($val);
        }
    }
    else
    {
        $obj = addslashes_deep($obj);
    }

    return $obj;
}

function daddslashes($string, $force = 0) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force);
			}
		} else {
			$string = addslashes($string);
		}
	}
	return $string;
}

/**
 * 递归方式的对变量中的特殊字符去除转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function stripslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    }
}

/**
 *  清除指定后缀的模板缓存或编译文件
 *
 * @access  public
 * @param  bool       $is_cache  是否清除缓存还是清出编译文件
 * @param  string     $ext       需要删除的文件名，不包含后缀
 *
 * @return int        返回清除的文件个数
 */
function clear_tpl_files($is_cache = true, $ext = '')
{
    $dirs = array();

    if ($is_cache)
    {
        $dirs[] = ROOT_PATH . 'data/smarty_caches/';
    }
    else
    {
        $dirs[] = ROOT_PATH . 'data/smarty_compiled/';
        $dirs[] = ROOT_PATH . 'data/smarty_compiled/admin/';
    }

    $str_len = strlen($ext);
    $count   = 0;

    foreach ($dirs AS $dir)
    {
        $folder = @opendir($dir);

        if ($folder == false)
        {
            continue;
        }

        while ($file = readdir($folder))
        {
            if ($file == '.' || $file == '..' || $file == 'index.htm' || $file == 'index.html')
            {
                continue;
            }
            if (is_file($dir . $file))
            {
                /* 如果有文件名则判断是否匹配 */
                $pos = ($is_cache) ? strrpos($file, '_') : strrpos($file, '.');

                if ($str_len > 0 && $pos !== false)
                {
                    $ext_str = substr($file, 0, $pos);
                    if ($ext_str == $ext)
                    {
                        if (@unlink($dir . $file)) {$count++; }
                    }
                }
                else
                {
                    if (@unlink($dir . $file)) { $count++; }
                }
            }
        }
        closedir($folder);
    }

    return $count;
}

/**
 * 清除模版编译文件
 *
 * @access  public
 * @param   mix     $ext    模版文件名， 不包含后缀
 * @return  void
 */
function clear_compiled_files($ext = null) { return clear_tpl_files(false, $ext); }

/**
 * 清除缓存文件
 *
 * @access  public
 * @param   mix     $ext    模版文件名， 不包含后缀
 * @return  void
 */
function clear_cache_files($ext = null) { return clear_tpl_files(true, $ext); }

/**
 * 清除模版编译和缓存文件
 *
 * @access  public
 * @param   mix     $ext    模版文件名后缀
 * @return  void
 */
function clear_all_files($ext = null)
{
    return clear_tpl_files(false, $ext) + clear_tpl_files(true,  $ext);
}

/**
 * 页面上调用的js文件
 *
 * @access  public
 * @param   string      $files
 * @return  void
 */
function smarty_insert_scripts($args)
{
    static $scripts = array();
    $arr = explode(',', str_replace(' ','',$args['files']));
    $str = '';
    foreach ($arr AS $val)
    {
        if (in_array($val, $scripts) == false)
        {
            $scripts[] = $val;
            if ($val{0} == '.')
            {
                $str .= '<script type="text/javascript" src="' . $val . '"></script>';
            }
            else
            {
                $str .= '<script type="text/javascript" src="js/' . $val . '"></script>';
            }
        }
    }

    return $str;
}

/**
 * 创建分页的列表
 *
 * @access  public
 * @param   integer $count
 * @return  string
 */
function smarty_create_pages($params)
{
    extract($params);

    $str = '';
    $len = 10;

    if (empty($page)) { $page = 1; }

    if (!empty($count))
    {
        $step = 1;
        $str .= "<option value='1'>1</option>";

        for ($i = 2; $i < $count; $i += $step)
        {
            $step = ($i >= $page + $len - 1 || $i <= $page - $len + 1) ? $len : 1;
            $str .= "<option value='$i'";
            $str .= $page == $i ? " selected='true'" : '';
            $str .= ">$i</option>";
        }

        if ($count > 1)
        {
            $str .= "<option value='$count'";
            $str .= $page == $count ? " selected='true'" : '';
            $str .= ">$count</option>";
        }
    }

    return $str;
}
/**
 *  将一个字串中含有全角的数字字符、字母、空格或'%+-()'字符转换为相应半角字符
 *
 * @access  public
 * @param   string       $str         待转换字串
 *
 * @return  string       $str         处理后字串
 */
function make_semiangle($str)
{
    $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
                 '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
                 'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
                 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
                 'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
                 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
                 'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
                 'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
                 'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
                 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
                 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
                 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
                 'ｙ' => 'y', 'ｚ' => 'z',
                 '（' => '(', '）' => ')', '［' => '[', '］' => ']', '【' => '[',
                 '】' => ']', '〖' => '[', '〗' => ']', '「' => '[', '」' => ']',
                 '『' => '[', '』' => ']', '｛' => '{', '｝' => '}', '《' => '<',
                 '》' => '>',
                 '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
                 '：' => ':', '。' => '.', '、' => ',', '，' => ',', '、' => '.',
                 '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
                 '＂' => '"', '＇' => '`', '｀' => '`', '｜' => '|', '〃' => '"',
                 '　' => ' ');

    return strtr($str, $arr);
}

/**
 * 检查文件类型
 *
 * @access      public
 * @param       string      filename            文件名
 * @param       string      realname            真实文件名
 * @param       string      limit_ext_types     允许的文件类型
 * @return      string
 */
function check_file_type($filename, $realname = '', $limit_ext_types = '')
{
    if ($realname)
    {
        $extname = strtolower(substr($realname, strrpos($realname, '.') + 1));
    }
    else
    {
        $extname = strtolower(substr($filename, strrpos($filename, '.') + 1));
    }

    $str = $format = '';

    $file = @fopen($filename, 'rb');
    if ($file)
    {
        $str = @fread($file, 0x400); // 读取前 1024 个字节
        @fclose($file);
    }
    else
    {
        if (stristr($filename, ROOT_PATH) === false)
        {
            if ($extname == 'jpg' || $extname == 'jpeg' || $extname == 'gif' || $extname == 'png' || $extname == 'doc' ||
                $extname == 'xls' || $extname == 'txt'  || $extname == 'zip' || $extname == 'rar' || $extname == 'ppt' ||
                $extname == 'pdf' || $extname == 'rm'   || $extname == 'mid' || $extname == 'wav' || $extname == 'bmp' ||
                $extname == 'swf' || $extname == 'chm'  || $extname == 'sql' || $extname == 'cert')
            { $format = $extname; }
        }
        else { return ''; }
    }

    if ($format == '' && strlen($str) >= 2 )
    {
        if (substr($str, 0, 4) == 'MThd' && $extname != 'txt') { $format = 'mid'; }
        elseif (substr($str, 0, 4) == 'RIFF' && $extname == 'wav') { $format = 'wav'; }
        elseif (substr($str ,0, 3) == "\xFF\xD8\xFF") { $format = 'jpg'; }
        elseif (substr($str ,0, 4) == 'GIF8' && $extname != 'txt'){ $format = 'gif'; }
        elseif (substr($str ,0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") { $format = 'png'; }
        elseif (substr($str ,0, 2) == 'BM' && $extname != 'txt') { $format = 'bmp'; }
        elseif ((substr($str ,0, 3) == 'CWS' || substr($str ,0, 3) == 'FWS') && $extname != 'txt'){ $format = 'swf'; }
        elseif (substr($str ,0, 4) == "\xD0\xCF\x11\xE0")
        {   // D0CF11E == DOCFILE == Microsoft Office Document
            if (substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'doc') { $format = 'doc'; }
            elseif (substr($str,0x200,2) == "\x09\x08" || $extname == 'xls') { $format = 'xls'; } 
            elseif (substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'ppt') { $format = 'ppt'; }
        } 
        elseif (substr($str ,0, 4) == "PK\x03\x04") { $format = 'zip';  } 
        elseif (substr($str ,0, 4) == 'Rar!' && $extname != 'txt') { $format = 'rar'; } 
        elseif (substr($str ,0, 4) == "\x25PDF") { $format = 'pdf'; } 
        elseif (substr($str ,0, 3) == "\x30\0x82\0x0a") { $format = 'cert'; } 
        elseif (substr($str ,0, 4) == 'ITSF' && $extname != 'txt') { $format = 'chm'; } 
        elseif (substr($str ,0, 4) == "\x2ERMF") { $format = 'rm'; } 
        elseif ($extname == 'sql') { $format = 'sql'; } 
        elseif ($extname == 'txt') { $format = 'txt'; }
    }

    if ($limit_ext_types && stristr($limit_ext_types, '|' . $format . '|') === false) { $format = ''; }

    return $format;
}

/**
 * 对 MYSQL LIKE 的内容进行转义
 *
 * @access      public
 * @param       string      string  内容
 * @return      string
 */
function mysql_like_quote($str)
{
    return strtr($str, array("\\\\" => "\\\\\\\\", '_' => '\_', '%' => '\%'));
}

/**
 * 获取服务器的ip
 *
 * @access      public
 *
 * @return string
 **/
function real_server_ip()
{
    static $serverip = NULL;
    if ($serverip !== NULL) { return $serverip; }

    if (isset($_SERVER))
    {
        if (isset($_SERVER['SERVER_ADDR'])) { $serverip = $_SERVER['SERVER_ADDR']; }
        else { $serverip = '0.0.0.0'; }
    }
    else { $serverip = getenv('SERVER_ADDR'); }

    return $serverip;
}

/**
 * 自定义 header 函数，用于过滤可能出现的安全隐患
 *
 * @param   string  string  内容
 *
 * @return  void
 **/
function ecs_header($string, $replace = true, $http_response_code = 0)
{
    $string = str_replace(array("\r", "\n"), array('', ''), $string);

    if (preg_match('/^\s*location:/is', $string))
    {
        @header($string . "\n", $replace); exit();
    }

    if (empty($http_response_code) || PHP_VERSION < '4.3') { @header($string, $replace); }
    else { @header($string, $replace, $http_response_code); }
}

function ecs_iconv($source_lang, $target_lang, $source_string = '')
{
    static $chs = NULL;

    /* 如果字符串为空或者字符串不需要转换，直接返回 */
    if ($source_lang == $target_lang || $source_string == '' || preg_match("/[\x80-\xFF]+/", $source_string) == 0)
    {
        return $source_string;
    }
    if ($chs === NULL)
    {
        include_once(ROOT_PATH . 'includes/cls_iconv.php');
        $chs = new Chinese(ROOT_PATH);
    }
    return $chs->Convert($source_lang, $target_lang, $source_string);
}

function ecs_geoip($ip)
{
    static $fp = NULL, $offset = array(), $index = NULL;

    $ip    = gethostbyname($ip);
    $ipdot = explode('.', $ip);
    $ip    = pack('N', ip2long($ip));

    $ipdot[0] = (int)$ipdot[0];
    $ipdot[1] = (int)$ipdot[1];
    if ($ipdot[0] == 10 || $ipdot[0] == 127 || ($ipdot[0] == 192 && $ipdot[1] == 168) || ($ipdot[0] == 172 && ($ipdot[1] >= 16 && $ipdot[1] <= 31)))
    {
        return 'LAN';
    }

    if ($fp === NULL)
    {
        $fp     = fopen(ROOT_PATH . 'includes/codetable/qqwry.dat', 'rb');
        if ($fp === false)
        {
            return 'Invalid IP data file';
        }
        $offset = unpack('Nlen', fread($fp, 4));
        $index  = fread($fp, $offset['len'] - 4);
    }

    $length = $offset['len'] - 1028;
    $start  = unpack('Vlen', $index[$ipdot[0] * 4] . $index[$ipdot[0] * 4 + 1] . $index[$ipdot[0] * 4 + 2] . $index[$ipdot[0] * 4 + 3]);
    for ($start = $start['len'] * 8 + 1024; $start < $length; $start += 8)
    {
        if ($index{$start} . $index{$start + 1} . $index{$start + 2} . $index{$start + 3} >= $ip)
        {
            $index_offset = unpack('Vlen', $index{$start + 4} . $index{$start + 5} . $index{$start + 6} . "\x0");
            $index_length = unpack('Clen', $index{$start + 7});
            break;
        }
    }

    fseek($fp, $offset['len'] + $index_offset['len'] - 1024);
    $area = fread($fp, $index_length['len']);

    fclose($fp);
    $fp = NULL;

    return $area;
}
function ip_sys($st){//	'0=ip	1=sys
	if($st==1){
		return $_SERVER["http_user_agent"];
    }else{
		return $_SERVER["remote_addr"];
	}
}
//
function get_broswer($sys){
	 //$sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
     if (stripos($sys, "Firefox/") > 0) {
         preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
         $exp[0] = "Firefox";
         $exp[1] = $b[1];  //获取火狐浏览器的版本号
     } elseif (stripos($sys, "Maxthon") > 0) {
         preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
         $exp[0] = "傲游";
         $exp[1] = $aoyou[1];
     } elseif (stripos($sys, "MSIE") > 0) {
         preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
         //$exp = "Internet Explorer ".$ie[1];
         $exp[0] = "IE";
         $exp[1] = $ie[1];  //获取IE的版本号
     } elseif (stripos($sys, "OPR") > 0) {
		 preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
         $exp[0] = "Opera";
         $exp[1] = $opera[1];  //获取opera浏览器版本号,今天下载一个opera浏览器做测试，发现opera竟然也换成谷歌的内核了，囧
     } elseif (stripos($sys, "Chrome") > 0) {
		 preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
         $exp[0] = "Chrome";
         $exp[1] = $google[1];  //获取google chrome的版本号
     } elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
         preg_match("/rv:([\d\.]+)/", $sys, $IE);//判断IE11非兼容模式
         $exp[0] = "IE";
         $exp[1] = $IE[1];
     }else {
		$exp[0] = "未知浏览器";
        $exp[1] = ""; 
	 }
     return $exp[0].'('.$exp[1].')';
}
//
function get_os($agent){
	// = $_SERVER['HTTP_USER_AGENT'];
	$os = false;
 
	if (preg_match('/win/i', $agent) && strpos($agent, '95'))
	{
		$os = 'Windows 95';
	}
	else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90'))
	{
		$os = 'Windows ME';
	}
	else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent))
	{
		$os = 'Windows 98';
	}
	else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent))
	{
		$os = 'Windows Vista';
	}
	else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent))
	{
		$os = 'Windows 7';
	}
	else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent))
	{
		$os = 'Windows 8';
	}
	else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent))
	{
		$os = 'Windows XP';
	}
	else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent))
	{
		$os = 'Windows 2000';
	}
	else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent))
	{
		$os = 'Windows NT';
	}
	else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent))
	{
		$os = 'Windows 32';
	}
	else if (preg_match('/linux/i', $agent))
	{
		$os = 'Linux';
	}
	else if (preg_match('/unix/i', $agent))
	{
		$os = 'Unix';
	}
	else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent))
	{
		$os = 'SunOS';
	}
	else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent))
	{
		$os = 'IBM OS/2';
	}
	else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent))
	{
		$os = 'Macintosh';
	}
	else if (preg_match('/PowerPC/i', $agent))
	{
		$os = 'PowerPC';
	}
	else if (preg_match('/AIX/i', $agent))
	{
		$os = 'AIX';
	}
	else if (preg_match('/HPUX/i', $agent))
	{
		$os = 'HPUX';
	}
	else if (preg_match('/NetBSD/i', $agent))
	{
		$os = 'NetBSD';
	}
	else if (preg_match('/BSD/i', $agent))
	{
		$os = 'BSD';
	}
	else if (preg_match('/OSF1/i', $agent))
	{
		$os = 'OSF1';
	}
	else if (preg_match('/IRIX/i', $agent))
	{
		$os = 'IRIX';
	}
	else if (preg_match('/FreeBSD/i', $agent))
	{
		$os = 'FreeBSD';
	}
	else if (preg_match('/teleport/i', $agent))
	{
		$os = 'teleport';
	}
	else if (preg_match('/flashget/i', $agent))
	{
		$os = 'flashget';
	}
	else if (preg_match('/webzip/i', $agent))
	{
		$os = 'webzip';
	}
	else if (preg_match('/offline/i', $agent))
	{
		$os = 'offline';
	}
	else
	{
		$os = '未知操作系统';
	}
	return $os; 
}
function randnum($str){
	$strchar=substr($str,0,1).Date("YmdHis").rand(1000,9999);
	return $strchar;
}
function killbad($strchar){
	if(empty($strchar)){
	return "";
	}else{
	$strchar = trim($strchar);
	$strchar = str_replace('"','&#34;',$strchar);
	$strchar = str_replace("'","&#39;",$strchar);
	$strchar = str_replace("<","&lt;",$strchar);
	$strchar = str_replace(">","&gt;",$strchar);
	return $strchar;
	}
}

//设置机构
function setBran($v,$id){
	//echo "begin";
	$str="";
	$sql="select id,title from ".DBFIX."branch where types='".$v."' order by id desc";
	$res=@mysql_query($sql);
	if(@mysql_num_rows($res)==0){
		$str="<option value='0'>暂无</option>";
	}else{
		while($rb=@mysql_fetch_array($res)){
			if($id==$rb['id']){
				$sel = " selected";
			}else{
				$sel = "";
			}
			$str .= "<option value='".$rb['id']."'".$sel.">".$rb['title']."</option>";
		}
	}
	
	return "<select name='bid' id='bid'><option value='0'>所属机构</option>".$str."</select>";
}
/**
 * 获得utf8编码的字符长度
 * 汉字,大写字母,全角标点等长度为1
 * 阿拉伯数字,半角标点 等长度为0.5
 * @author XuehuiHe
 * @param $str
 * @return 字符长度
 */
function strlen_utf8($str) {
	$i = 0;
	$tempLen = 0;
	$sourceBytes = strlen($str);
	$returnStr = '';
	while ($i < $sourceBytes) {
		$ascFirst = ord(substr($str, $i, 1));
		if ($ascFirst >= 224) { //3个字节
			$returnStr .= substr($str, $i, 3);
			$i += 3;
			$tempLen++;
		}
		elseif ($ascFirst >= 192) { //2个字节
			$returnStr .= substr($str, $i, 2);
			$i += 2;
			$tempLen++;
		}
		elseif ($ascFirst >= 65 && $ascFirst <= 90) { //大写字母,一个字节
			$returnStr .= substr($str, $i, 1);
			$i += 1;
			$tempLen++;
		} else { //其他半个字符长度,一个字节
			$returnStr .= substr($str, $i, 1);
			$i += 1;
			$tempLen += 0.5;
		}
	}
	return $tempLen;
}
/********************************
 函数功能：用post,get方法获得参数
 参    数：var>>要获得的参数值
 ********************************/
function postget($var) {
	$value = '';
	if (isset ($_POST[$var])) {
		$value = $_POST[$var];
	}
	elseif (isset ($_GET[$var])) {
		$value = $_GET[$var];
	}
	return $value;
}

function replaceSeps($str) {
	if (!empty ($str)) {
		$firstStr = substr($str, 0, 1);
		if ($firstStr == "/" || $firstStr == "\\") {
			return $str;
		} else
			return $str = "/" . $str;
		;
	} else {
		return "";
	}
}
function cut_utf8($sourceStr, $cutLen, $endStr = '..') {
	$sourceStrLen = strlen_utf8($sourceStr);
	if ($sourceStrLen <= $cutLen) {
		return $sourceStr;
	} else {
		$cutLen -= strlen_utf8($endStr);
	}
	$returnStr = '';
	$i = 0;
	$sourceBytes = strlen($sourceStr);
	$tmpCutLen = 0;
	while ($tmpCutLen <> $cutLen) {
		$tmpCutLen = $cutLen;
		$ascFirst = ord(substr($sourceStr, $i, 1));
		if ($ascFirst >= 224 && $cutLen >= 1) { //3个字节
			$returnStr .= substr($sourceStr, $i, 3);
			$i += 3;
			$cutLen -= 1;
		}
		elseif ($ascFirst >= 192 && $cutLen >= 1) { //2个字节
			$returnStr .= substr($sourceStr, $i, 2);
			$i += 2;
			$cutLen -= 1;
		}
		elseif ($ascFirst >= 65 && $ascFirst <= 90 && $cutLen >= 1) { //大写字母,一个字节
			$returnStr .= substr($sourceStr, $i, 1);
			$i += 1;
			$cutLen -= 1;
		}
		elseif ($ascFirst <= 127 && $cutLen >= 0.5) { //其他半个字符长度,一个字节
			$returnStr .= substr($sourceStr, $i, 1);
			$i += 1;
			$cutLen -= 0.5;
		}
	}
	return $returnStr . $endStr;
}
/**
* 判断参数字符串是否为密码格式（必须包含数字、字母的6至18位密码串）
* @param $password 需要被判断的字符串
* @return boolean
*/
function is_password($password) {
    if(strlen($password)>18 || strlen($password)<6) {return false;}
    return (preg_match('/\d{1,16}/',$password)===1 && preg_match('/[a-zA-Z]{1,16}/',$password)===1 && strlen($password)<=16);
}
/**
* 判断参数字符串是否为邮箱格式
* @param $mail 需要被判断的字符串
* @return boolean
*/
function is_mail($mail) {
    return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',$mail)===1;
}
/**
* 判断参数字符串是否为天朝手机号
* @param $phone 需要被判断的字符串或数字
* @return boolean
*/
function is_phone($phone) {
    return preg_match('/^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$|^170\d{8}$/',$phone)===1;
}
/**
* 判断参数字符串是否为数字账号[discuz、phpwind、qq、小米等数字账号格式判断] 4至11位的正整数
* @param $uid 需要被判断的字符串或数字
* @return boolean
*/
function is_uid($uid) {
    //is_numeric ctype_digit的参数必须是字符串格式的数字才会返回true
    //不用正则的判断方法 return strlen($uid)>=4 && strlen($uid)<=11 && ctype_digit((string)$uid); 
    return preg_match('/^[1-9]\d{3,10}$/',$uid)===1;
}
/**
* 判断参数字符串是否为天朝身份证号
* @param $uid 需要被判断的字符串或数字
* @return mixed false 或 array[有内容的array boolean为真]
*/
function is_id_card($id) {
    return is_citizen_id($id);
}
function is_citizen_id($id) {
    //长度效验  18位身份证中的X为大写
    $id  = strtoupper($id);
    if(!(preg_match('/^\d{17}(\d|X)$/',$id) || preg_match('/^\d{15}$/',$id))) {
      return false;
    }
    //15位老号码转换为18位 并转换成字符串
    $Wi          = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1); 
    $Ai          = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); 
    $cardNoSum   = 0;
    if(strlen($id)==16) {
        $id        = substr(0, 6).'19'.substr(6, 9); 
        for($i = 0; $i < 17; $i++) {
          $cardNoSum += substr($id,$i,1) * $Wi[$i];
        }  
        $seq       = $cardNoSum % 11; 
        $id        = $id.$Ai[$seq];
    }
    //效验18位身份证最后一位字符的合法性
    $cardNoSum   = 0;
    $id17        = substr($id,0,17);
    $lastString  = substr($id,17,1);
    for($i = 0; $i < 17; $i++) {
        $cardNoSum += substr($id,$i,1) * $Wi[$i];
    }  
    $seq         = $cardNoSum % 11;
    $realString  = $Ai[$seq];
    if($lastString!=$realString) {return false;}
    //地域效验
    $oCity       =  array(11=>"北京",12=>"天津",13=>"河北",14=>"山西",15=>"内蒙古",21=>"辽宁",22=>"吉林",23=>"黑龙江",31=>"上海",32=>"江苏",33=>"浙江",34=>"安徽",35=>"福建",36=>"江西",37=>"山东",41=>"河南",42=>"湖北",43=>"湖南",44=>"广东",45=>"广西",46=>"海南",50=>"重庆",51=>"四川",52=>"贵州",53=>"云南",54=>"西藏",61=>"陕西",62=>"甘肃",63=>"青海",64=>"宁夏",65=>"新疆",71=>"台湾",81=>"香港",82=>"澳门",91=>"国外");
    $City        = substr($id, 0, 2);
    $BirthYear   = substr($id, 6, 4);
    $BirthMonth  = substr($id, 10, 2);
    $BirthDay    = substr($id, 12, 2);
    $Sex         = substr($id, 16,1) % 2 ;//男1 女0
    //$Sexcn       = $Sex?'男':'女';
    //地域验证
    if(is_null($oCity[$City])) {return false;}
    //出生日期效验
    if($BirthYear>2078 || $BirthYear<1900) {return false;}
    $RealDate    = strtotime($BirthYear.'-'.$BirthMonth.'-'.$BirthDay);
    if(date('Y',$RealDate)!=$BirthYear || date('m',$RealDate)!=$BirthMonth || date('d',$RealDate)!=$BirthDay) {
        return false;
    }
    return array('id'=>$id,'location'=>$oCity[$City],'Y'=>$BirthYear,'m'=>$BirthMonth,'d'=>$BirthDay,'sex'=>$Sex);
}
/**
* 将指定时间戳转换为截止当前的xx时间前的格式  例如 return '3分钟前''
* @param view
* @return string
*/
function get_views($view) {
    if($view>=10000) {
        $view = (ceil($view/10000)).'万次';
    }else if($view>=1000) {
        $view = (ceil($view/1000)).'千次';
    }
    return $view;
}
/**
* 将指定时间戳转换为截止当前的xx时间前的格式  例如 return '3分钟前''
* @param string|int $timestamp unix时间戳
* @return string
*/
function time_ago($timestamp) {
    $etime = time() - $timestamp;
    if ($etime < 1) return '刚刚';     
    $interval = array (         
      12 * 30 * 24 * 60 * 60  =>  '年前 ('.date('Y-m-d', $timestamp).')',
      30 * 24 * 60 * 60       =>  '个月前 ('.date('m-d', $timestamp).')',
      7 * 24 * 60 * 60        =>  '周前 ('.date('m-d', $timestamp).')',
      24 * 60 * 60            =>  '天前',
      60 * 60                 =>  '小时前',
      60                      =>  '分钟前',
      1                       =>  '秒前'
    );
    foreach ($interval as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . $str;
        }
    };
}
/**
* 格式化字节大小
* @param  number $size      字节数
* @param  string $delimiter 数字和单位分隔符
* @return string            格式化后的带单位的大小
* @author 
*/
function format_bytes($size, $delimiter = '') {
  $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
  for ($i = 0; $size >= 1024 && $i < 6; $i++) $size /= 1024;
  return round($size, 2) . $delimiter . $units[$i];
}
?>