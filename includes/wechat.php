<?php
define("TOKEN", "8x9p4s4e6x");
define("APPID", "wx2ffdfc53824c5430");
define("SECRET", "8b501b4335e6559840dbdac18cf0da85");

class wechatCallbackapiTest
{
    /* 
    *  私有成员变量 存token值
    *  因为//access_token是公众号的全局唯一票据，公众号调用各接口时都需使用access_token。
    *  正常情况下access_token有效期为7200秒，重复获取将导致上次获取的access_token失效。
    */
	private $_token;
	
	//构造函数，获取Access Token
    public function __construct($appid = NULL, $appsecret = NULL)
    {
        if(APPID && SECRET){
            $this->token = TOKEN;
			$this->appid = APPID;
            $this->appsecret = SECRET;
        }
		//$this->checkSignature();
       
		// 读取XML文件中的数据
		if (file_exists(V_ROOT . "/data/XMLToken.xml")){
			$xml = @simplexml_load_file(V_ROOT . "/data/XMLToken.xml");
			$now  = time();
			$AccessExpires = strtotime($xml->AccessExpires)+7200;
			//echo strtotime($xml->AccessExpires).','.$AccessExpires.','.$now;
			if ($now >= $AccessExpires){//access_token过期
				$this->_token = $this->get_access_token();
				
			}else{
				$this->_token = $xml->AccessToken;
			}
		}else{
			$this->_token = $this->get_access_token();
			$AccessExpires = date('Y-m-d H:i:s');
		}
    }
	public function get_access_token(){
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
		$res = $this->http_request($url);
		$result = json_decode($res, true);
		$content = '<?xml version="1.0" encoding="utf-8"?>
<xml>
<AccessToken>'.$result["access_token"].'</AccessToken>
<AccessExpires>'.date("Y-m-d H:i:s").'</AccessExpires>
</xml>';
		file_put_contents(V_ROOT . "/data/XMLToken.xml",$content);
		return $result["access_token"];
	}
    //HTTP请求（支持HTTP/HTTPS，支持GET/POST）
    protected function http_request($url, $data = null, $needToken = false)
    {
        if($needToken){
			$this -> _token = $this -> get_access_token();
		}
		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
		$strjson=json_decode($output);
		//var_dump($strjson);//开启可调试
        if (!empty($strjson-> errcode))  
        {echo $strjson-> errcode;
            switch ($strjson-> errcode){
                  
                case 40001:  
                    $this -> http_request($url, $data, true); //重新取值，可能是过期导致   
                    break;
                case 41001:  
                    throw new Exception("缺少access_token参数:".$strjson->errmsg); 
                    break;
				case 42001://重新取值，可能是access_token过期导致 
                    $this -> http_request($url, $data, true);
                    break;
                default:  
                    throw new Exception($strjson->errmsg); //其他错误，抛出   
                    break;
            }
        }
        return $output;
    }
	//默认函数
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
	//检测函数
    private function checkSignature()
    {
		// you must define TOKEN by yourself
		if (!defined("TOKEN")) {
			throw new Exception('TOKEN is not defined!');
		}
		$signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
	//接收用户消息
    public function responseMsg()
    {
        global $db;
		#$postStr = $_POST["HTTP_RAW_POST_DATA"];
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
			$id = $db->inserttable("cg_wx_msg", XML2Array($postStr), 1);
            switch ($RX_TYPE)
            {
                case "text":
                    $resultStr = $this->receiveText($postObj);
					//$xmlcontent = array('ToUserName' => $postObj->FromUserName, 'FromUserName' => $postObj->ToUserName,'CreateTime' => $time,'MsgType' => 'news','Content' => $resultStr);
					$db->inserttable("cg_wx_msg", array('ToUserName' => $postObj->FromUserName, 'FromUserName' => $postObj->ToUserName,'CreateTime' => $time,'MsgType' => 'news','Content' => $resultStr), 0);
                    break;
                case "event":
                    $resultStr = $this->receiveEvent($postObj);
					$db->inserttable("cg_wx_msg", array('ToUserName' => $postObj->FromUserName, 'FromUserName' => $postObj->ToUserName,'CreateTime' => $time,'MsgType' => 'event','Content' => $resultStr), 0);
                    break;
                default:
                    $resultStr = "";
                    break;
            }
			#var_dump($resultStr);
            echo $resultStr;
        }else {
            echo "";
        }
		exit;
    }
	//得到用户发送的文本
	private function receiveText($object)
	{
		global $db,$picserver,$siteurl;
		$keyword = trim($object->Content);
		$lnum = 9;
		$req = $db->getOneInfo("select content from cg_wx_request where title='".$keyword);
		if(!empty($req)){
			return $req;
		}
		
		$sql = "select id,pid from cg_area where locate(title,'{$keyword}')";
		$res = $db->getOneInfo($sql);
		$data = array();
		if(empty($res)){//未查询到国家，尝试其他查询
			$detail = 'tour';
			$limitsql = ' limit 0,' .$lnum;
			$sql = "select * from cg_product_route where locate(title,'{$keyword}')" . $limitsql;
			$query = $db->query($sql);
			while ($value = $db->fetch_array($query)) {
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
		}else{
			if(strpos($keyword, "线路") || strpos($keyword, "旅游") || strpos($keyword, "之旅") || strpos($keyword, "游")){
				$keyword = str_replace("线路","",$keyword);
				$keyword = str_replace('旅游','',$keyword);
				$keyword = str_replace("之旅","",$keyword);
				$keyword = str_replace("游","",$keyword);
				$sqlwhere = " where 1=1 and locate('1',op_type) and url is not null";
				$sstr = $res['pid']==0?"aid2=".$res['id']."":"city2=".$res['id']."";
				$sqladd = " and (locate('{$keyword}',title) or {$sstr})";
				$orderbysql = ' order by id desc';
				$limitsql = ' limit 0,' .$lnum;
				$sql = "select * from cg_product_route " . $sqlwhere. $sqladd . $orderbysql . $limitsql;
				$query = $db->query($sql);
				while ($value = $db->fetch_array($query)) {
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
			}elseif(strpos($keyword, "签证") || strpos($keyword, "通行证") || strpos($keyword, "赴台证")){
				$sqlwhere = " where types=2 and locate('1',op_type)";
				$keyword = str_replace("签证","",$keyword);
				$keyword = str_replace('通行证','',$keyword);
				
				$sstr = $res['pid']==0?"aid=".$res['id']."":"city=".$res['id']."";
				$sqladd = " and (locate('{$keyword}',title) or {$sstr})";
				// $sqladd = $res['pid']==0?" and aid=".$res['id']."":" and city=".$res['id']."";
				$orderbysql = ' order by id desc';
				$limitsql = ' limit 0,' .$lnum;
				$sql = "select * from cg_scenic" . $sqlwhere . $sqladd . $orderbysql . $limitsql;
				$query = $db->query($sql);
				//$totalnum = $db->result($db->query("select count(*) from cg_scenic" . $sqlwhere. $sqladd), 0); //总数;
				while ($row = $db->fetch_array($query)) {
					if (!empty ($row['url'])) {
						$row['url'] = (stristr($row["url"], "http://") == '') ? $picserver . replaceSeps($row["url"]) : $row["url"];
					}
					$row['word'] = cut_utf8(str_replace("&nbsp;", "", strip_tags($row['word'])), 39, '...');
					$data[] = $row;
				}
				$detail = 'visa';
			}elseif(strstr($keyword, "门票") || strstr($keyword, "景点") || strstr($keyword, "风光")){
				$sqlwhere = " where types=3 and locate('1',op_type)";

				$keyword = str_replace("门票","",$keyword);
				$keyword = str_replace('景点','',$keyword);
				$keyword = str_replace('风光','',$keyword);
				$sstr = $res['pid']==0?"aid=".$res['id']."":"city=".$res['id']."";
				$sqladd = " and (locate('{$keyword}',title) or {$sstr})";
				$orderbysql = ' order by id desc';
				$limitsql = ' limit 0,' .$lnum;
				$sql = "select * from cg_scenic" . $sqlwhere . $sqladd .$orderbysql . $limitsql;
				
				$query = $db->query($sql);
				while ($row = $db->fetch_array($query)) {
					if (!empty ($row['url'])) {
						$row['url'] = (stristr($row["url"], "http://") == '') ? $picserver . replaceSeps($row["url"]) : $row["url"];
					}
					$row['word'] = cut_utf8(str_replace("&nbsp;", "", strip_tags($row['word'])), 39, '...');
					$data[] = $row;
				}
				$detail = 'sight';
			}else{
				$sql = "empty";
				$detail = 'cgbt';
			}
			//file_put_contents("./log.html",date("Y-m-d H:m:s").$sql."<br>",FILE_APPEND);
		}
		
		if (count($data)==1){
			$content = array();
			if($detail!='tour'){
				$content[] = array("Title"=>$data[0]["title"],  "Description"=>$data[0]["word"], "PicUrl"=>$data[0]["url"], "Url" =>"http://weixin.cgbt.net/$detail/".$data[0]['id']);
			}else{
				$content[] = array("Title"=>$data[0]["title"],  "Description"=>$data[0]["feature"], "PicUrl"=>$data[0]["url"], "Url" =>"http://weixin.cgbt.net/tour/".$data[0]['id']);
			}
		}else if (count($data)>1){
			$content = array();
			foreach($data as $val){
				if($detail!='tour'){
					$content[] = array("Title"=>$val["title"],  "Description"=>$val["word"], "PicUrl"=>$val["url"], "Url" =>"http://weixin.cgbt.net/$detail/".$val['id']);
				}else{
					$content[] = array("Title"=>$val["title"],  "Description"=>$val["feature"], "PicUrl"=>$val["url"], "Url" =>"http://weixin.cgbt.net/tour/".$val['id']);
				}
			}
		}else{
			$content = $detail."欢迎使用金桥自动回复机器人！\n我的能力有限，请试试按“地区+产品”的方式来咨询吧！\n比如：厦门景点、日本签证或巴尔干线路\n".date("Y-m-d H:i:s",time())."\n金桥旅游";
		}
		if(is_array($content)){
			if (isset($content[0]['PicUrl'])){
				$result = $this->transmitNews($object, $content);
			}else if (isset($content['MusicUrl'])){
				$result = $this->transmitMusic($object, $content);
			}
		}else{
			$result = $this->transmitText($object, $content);
		}
		return $result;
	}
	//得到用户发送的事件
    private function receiveEvent($object)
    {
        $contentStr = "";
        switch ($object->Event)
        {
            case "subscribe":
                $contentStr = "欢迎关注金桥旅游";
            case "unsubscribe":
                break;
            case "CLICK":
                switch ($object->EventKey)
                {
                    case "company":
                        $contentStr[] = array("Title" =>"公司简介", 
                        "Description" =>"金桥旅游提供旅游相关的产品及服务", 
                        "PicUrl" =>"http://weixin.cgbt.net/images/logo.jpg", 
                        "Url" =>"weixin://addfriend/pondbaystudio");
                        break;
                    default:
                        $contentStr[] = array("Title" =>"默认菜单回复", 
                        "Description" =>"您正在使用的是金桥旅游的自定义菜单接口", 
                        "PicUrl" =>"http://weixin.cgbt.net/images/logo.jpg", 
                        "Url" =>"http://weixin.cgbt.net/");
                        break;
                }
                break;
            default:
                break;    

        }
        if (is_array($contentStr)){
            $resultStr = $this->transmitNews($object, $contentStr);
        }else{
            $resultStr = $this->transmitText($object, $contentStr);
        }
        return $resultStr;
    }
	public function cont2xml($object, $content){
        if (is_array($content)){
            $resultStr = $this->transmitNews($object, $content);
        }else{
            $resultStr = $this->transmitText($object, $content);
        }
		return $resultStr;
	}
	//翻译内容
    private function transmitText($object, $content, $funcFlag = 0)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>%d</FuncFlag>
</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $funcFlag);
        return $resultStr;
    }
	//翻译图文信息
    private function transmitNews($object, $arr_item, $funcFlag = 0)
    {
        //首条标题28字，其他标题39字
        if(!is_array($arr_item))
            return;

        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        $newsTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<Content><![CDATA[]]></Content>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
<FuncFlag>%s</FuncFlag>
</xml>";

        $resultStr = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item), $funcFlag);
        return $resultStr;
    }
	//向用户发送消息
    private function sendMsg($object)
    {
        global $db, $picserver, $siteurl, $bid;
		$funcFlag = 0;
        $contentStr = "你发送的内容为：".$object->Content;
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this -> _token;
		$res = $this->http_request($url);
		$result = json_decode($res, true);
    }
	//群发消息
	private function sendall($object){
		$url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=".$this -> _token;
		$res = $this->http_request($url);
		$result = json_decode($res, true);
		return $result;
	}
	//得到用户分组
	public function GetGroups()  
    {
        $url = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token=".$this -> _token;
		$res = $this->http_request($url);
		$result = json_decode($res, true);
        //$openidarr= $result['data']['openid'];
        //var_dump($openidarr);//调试
        return $result;
    }
	//得到用户列表
	public function GetUserList($next_openid='')  
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$this -> _token."&next_openid=$next_openid";
		$res = $this->http_request($url);
		$result = json_decode($res, true);
        //$openidarr= $result['data']['openid'];
        //var_dump($openidarr);//调试
        return $result;
    }
    //得到订阅用户详情（返回对象）   
    public function GetUserDetail($openid)  
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this -> _token."&openid={$openid}";
        $res = $this->http_request($url);
		$strjson = json_decode($res,true);
        return $strjson;
    }
	//获取菜单
	public function GetMenu()  
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$this -> _token;
        $res = $this->http_request($url);
		$strjson = json_decode($res,true);
        return $strjson;
    }
	//上传临时素材
	public function UploadImg($filedata,$picname='',$groupid=1){//jpg|png
		
		$url = "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=".$this -> _token;
		$res = $this->http_request($url,$filedata);
		$strjson = json_decode($res,true);
        return $strjson;
	}
	//上传永久素材
	public function Uploadss($filedata,$type='image'){
		$url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".$this -> _token."&type=$type";
		$res = $this->http_request($url,$filedata);
		$strjson = json_decode($res,true);
        return $strjson;
	}
	
}
/*获取访问信息*/
function traceHttp(){
	logger("REMOTE_ADDR:".$_SERVER['REMOTE_ADDR'].((strpos($_SERVER['REMOTE_ADDR'],"101.226"))?"weixin IP":"unknown IP"));
	logger("QUERY-STRING:".$_SERVER['QUERY_STRING']);
	if(isset($GLOBALS["HTTP_RAW_POST_DATA"])) logger("HTTP_RAW_POST_DATA:".$GLOBALS["HTTP_RAW_POST_DATA"]);
}
/*记录访问信息*/
function logger($content){
	file_put_contents("./log.html",date("Y-m-d H:m:s").$content."<br>",FILE_APPEND);
	//echo $content;
}
//要求的是封面图 360*200，多图文的非首个图文是200*200
?>