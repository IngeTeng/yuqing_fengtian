<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微信数据添加</title>
<link href="styles/global.css" rel="stylesheet" type="text/css" />
<link href="styles/style.css" rel="stylesheet" type="text/css" />
<style>
.div li{
text-align: right;
margin-right: 55px;
margin-top:15px;
}
</style>
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
</head>
<body>
<?php

include_once("lib.net.function.php");
define(property_host, "10.132.58.171");
define(property_port, 5040);

function getXmlValue($content, $start, $end)
{
	$pstart = strpos($content, $start);
        if ($pstart > 0 || $pstart === 0)
        {
                $pstart += strlen($start);
                $sub_content = substr($content, $pstart);
                $pend = strpos($sub_content, $end);
                if ($pend > 0 || $pend === 0)
                {
                        $a = substr($sub_content, 0, $pend);
                        return $a;
                }
        }
        return "";
	
}

function article_property($host, $port, $title, $content="")
{
        $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        if (false === socket_connect($socket, $host, $port)) {
                echo 'Can not connect to Server [' . $host . ':' . $port . '].';
                return 0;
        }
        $body = "<title><![CDATA[".$title."]]></title>\r\n";
        $body .= "<content><![CDATA[".$content."]]></content>\r\n";
        if (false === eget_send($socket, $body, strlen($body))) {
                echo 'Can not write body info to Server [' . $host . ':' . $port . '].';
                return 0;
        }
        $contentLength = 0;
	$head = '';
        if (false === eget_read_head($socket, $head, $contentLength)) {
                echo 'Can not read body info HEAD of Server.';
                return 0;
        }

        $data = '';
        if (false === eget_read($socket, $data, $contentLength)) {
                echo 'Can not read info BODY of Server.';
                return 0;
        }
        socket_close($socket);
        
		$result = strstr($data, '<title');
        $pstart = strpos($result, '<title');
        $title_array = array();
        $i=0;
		$property=0;
        while ($pstart > 0 || $pstart === 0){
			$pend = strpos($result, '</title>');
			$record = substr($result, $pstart, $pend - $pstart); 
			$property = getXmlValue($record, '<cid>', "</cid>");
			if($property==2){  
			    return $property;
			}
			$result = substr($result, $pend+ strlen('</title>'));
			$pstart = strpos($result, '<title');
			$title_array[$i]=$property;
			$i++;
        }
		if(count($title_array)>0){ 
		    $property=1;
		    return $property;
	    }
		return 0;
}

function parse_space($str)
{
	$str = str_replace("&nbsp;"," ", $str);
	$str = trim($str);
	return $str;
}
function get_info($url)
{
echo($url);
	$content = html_get($url);
	$info = array();
	$info["author"] = getXmlValue($content, 'nickname = "', '";');
	$info["title"] = parse_space(getXmlValue($content, 'msg_title = "', '";'));
	$info["content"] = parse_space(getXmlValue($content, 'msg_desc = "', '";'));
	$info["date"] = getXmlValue($content, 'ct = "', '";');
	$info["url"] = $url;
	$info["time_str"] = date("Y-m-d H:i", $info["date"]);
	return $info;
}

function get_weibo_info($content, &$info)
{
	$json = "{\"a\":\"" . $content ."\"}";
//echo($json);
	$j = json_decode($json);
//print_r($j);
	$content = $j->a;
	//$content = iconv("UTF-8", "GBK", $content);
//echo($content);
	//mid
	preg_match_all('/mid=\"([^\"]+)\"/', $content, $a);
	if ($a == null)
	{
		return false;
	}
	$info["mid"] = $a[1][0];

	//user name
	unset($a);
	preg_match_all('/title=\"([^\"]+)\"/', $content, $a);
	if ($a == null)
	{
		return false;
	}
	$info["uname"] = $a[1][0];
	
	//head_img
	unset($a);
	preg_match_all('/<img src=\"([^\"]+)\"/', $content, $a);
	if ($a == null)
	{
		return false;
	}
	$info["head_img"] = $a[1][0];
	
	
	//url
	unset($a);
	preg_match_all('/<a href=\"([^\"]+)\" title=\"([^\"]+)\" date=/', $content, $a);
//print_r($a);
	if ($a == null)
	{
		return false;
	}
	$info["url"] = $a[1][0];
	if ($info["url"] == "")
	{
		preg_match_all('/<a href=\"([^\"]+)\" target="_blank" title=\"([^\"]+)\" date=/', $content, $a);
		$info["url"] = $a[1][0];
	}
	$ptmp = strpos($info["url"], "?");
	if ($ptmp > 0)
	{
		$info["url"] = substr($info["url"], 0, $ptmp);
	}
	
	
	//user id
	unset($a);
	preg_match_all('/http:\/\/weibo.com\/([^\/]+)\//', $info["url"], $a);
	if ($a == null)
	{
		return false;
	}
	$info["uid"] = $a[1][0];
	
	//date
	unset($a);
	preg_match_all('/date=\"([^\"]+)\"/', $content, $a);
	if ($a == null)
	{
		return false;
	}
	$info["date"] = $a[1][0] / 1000;
	
	//text
	unset($a);
	$s = '<p class="comment_txt" node-type="feed_list_content"';
	$pstart = strpos($content, $s);
	$sub = "";
	if ($pstart > 0)
	{
		$pstart += strlen($s);
		$sub = substr($content, $pstart);
		$pstart = strpos($sub, "</p>");
		$sub2 = substr($sub, 0, $pstart);
		$p = strpos($sub2, '">');
		if ($p > 0)
		{
			$sub2 = substr($sub2, $p + 2);
		}
		$info["text"] = trim(strip_tags($sub2));
		
	}
	else
	{
		return false;
	}

	
	// pic_url
	unset($a);
	preg_match_all('/<img class=\"bigcursor\" src=\"([^\"]+)\"/', $content, $a);
	if ($a == null)
	{
		$info["image_url"] = "";
	}
	else
	{
		$info["image_url"] = $a[1][0];
	}
	
	unset($a);
    preg_match_all('/verify\" title= \"([^\"]+)\"/', $content, $a);
    if ($a == null || count($a) < 1)
    {
        $info["isv"] = "";
    }
    else
    {
        $info["isv"] = $a[1][0];
   	}
	return true;
}


if(!isset($_COOKIE['user_id'])){
   echo "<script>alert('您尚未登陆！');location.href='index.php';</script>";
   return;
}else{
  require_once('adminv/inc_dbconn.php');
  require_once('header.php');
  $user_id=$_COOKIE['user_id'];
  
  $title = "请先选择对应关键字，并粘贴微博对应搜索结果HTML";
  if ($_POST["k_id"] != "")
  {
  	 $content = $_POST["content"];
  	 $k_id = $_POST["k_id"];
  	 if ($content != "")
  	 {
  	 	preg_match_all('/<div class=\\\"WB_cardwrap S_bg2 clearfix(.*)<div class=\\\"feed_action/iU', $content, $dls);


		for ($i = 0; $i < count($dls[0]); $i++)
		{
			get_weibo_info($dls[0][$i], $info);
			$info["time_str"] = date("Y-m-d H:i", $info["date"]);
//print_r($info);	
//return;
//			if ($info["date"] < $task_lst_time)
//			{
//				unset($info);
//				break;
//			}
			
			$text = iconv("utf8","gbk", $info["text"]);
			$property = article_property(property_host, property_port,$text);//分类

			$table_change = array("'"=>"\\'");
        	$text =  strtr($info["text"], $table_change);
        	if ($text == "")
        	{
        		continue;
        	}
        	
        	$text2 = strtolower($text);
        	$sql = "select * from keyword where k_id = ".$k_id;
        	$res = mysql_query($sql);
        	$row = mysql_fetch_array($res);
        	$keyword = strtolower($row["keyword"]);
        	if (!strstr($text2, $keyword))
        	{
        		continue;
        	}
        	
        	$sql = "select article_id from weibo_article where article_url = '".$info["url"]."'";
        	$res = mysql_query($sql);
        	$aid = 0;
        	if (mysql_num_rows($res) > 0)
        	{
        		$row = mysql_fetch_array($res);
        		$aid = $row["article_id"];
        	}
        	else
        	{
        		$isV = 0;
        		if ($info["isv"] != "")
        		{
        			$isV = 1;
        		}
        		$sql = 	"insert into weibo_article(article_url, article_title, article_pubtime, article_addtime, article_comment, article_repost, author, isV, rz_info, fans,media,mid)".
        				" values('". $info["url"] ."','". $text ."',". $info["date"] .",".time() .",0,0,".
        				"'". $info["uname"] . "',".$isV.",'".$info["isv"]."',0,'新浪','".$info["mid"]."')";
				mysql_query($sql);
//print_r($sql);
//return;
				$aid = mysql_insert_id();
			}
			mysql_free_result($res);

			$sql = "select * from user_keywords where user_id = ".$user_id . " and k_id = ". $k_id;
    //   echo($sql);
        	$res = mysql_query($sql);
        	if ($row = mysql_fetch_array($res))
        	{
        		$uk_id = $row["uk_id"];
        		$c_id = $row["c_id"];
        		$sql = "select id from weibo_key where user_id = $user_id and article_id = $aid and c_id = $c_id";
	//echo($sql);
				$res = mysql_query($sql);
		    	$num = mysql_num_rows($res);
		    	mysql_free_result($res);
		    	if ($num == 0)
		    	{
					$sql = 	"insert into weibo_key(uk_id, article_id, article_property, article_pubtime,user_id,article_addtime,c_id) values(".
							$uk_id. "," . $aid . ",". $property . ",". $info["date"].",".$user_id.",".time().",".$c_id.")";
					echo($sql);
					mysql_query($sql);
				}
			}
        		
		}
        $title = "[添加成功]";
  	 }
  }
?>
<div class="Navigation">
	<ul>
    	<li><a href="ordinary_home.php">首页</a></li>
        <li><a href="#">微博数据添加</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
  <form action="" method="post" name="keywordForm">
  <div style="margin-left:300px"><?php echo $title ?></div>
    <div class="div" style="width:800px;height:600px">
	  <ul>
	  	<li style="text-align: left;margin-left:10px;">
	      <label for="keyword" class="label">选择关键字：</label>
		  <select name="k_id">
		  	<?php
				$sql = "select * from keyword where k_id in (select k_id from user_keywords where user_id = ".$user_id.")";	
				$res = mysql_query($sql);
				while ($row = mysql_fetch_array($res))
				{
			?>
					<option value="<?php echo $row["k_id"]?>" <?php  if ($row["k_id"] == $k_id) echo "selected" ?>><?php echo $row["keyword"] ?></option>
			<?php
				}
		  	?>
		  </select>
		</li>
	    <li style="text-align: left;margin-left:10px;">
	      <label for="keyword" class="label">微博搜索结果HTML：</label>
		  <textarea name="content" style="width:600px;height:300px;" /></textarea>
		</li>
		<li style="text-align: left;margin-left:10px;">
		  <font color="#FF0000"><p>微博搜索结果HTML 步骤：</p></font>
		  <p>1. 请先登录新浪微博</p>
		  <p>2. 替换该网址中关键字和翻页参数 http://s.weibo.com/weibo/%E5%87%AF%E7%BE%8E%E7%91%9E&scope=ori&nodup=1&xsort=time&page=2</p>
		  <p>3. 鼠标右键查看网页源文件，全选，复制，粘贴</p>
		  
		  
		  <input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
		  <input type="submit" value="提交" id="submit" style="margin-left:80px;width:100px" />
		</li>
	  </ul>
	</div>
  </form>
</div>
<div class="Content_bottom"><img src="images/content_bottom.png" /></div>
<?php include_once('footer.php');?>
</body>
</html>
<?php
}
?>
<script>
$('#submit').click(function(){
    var content=$('#content').val();
    if(content==""){
	       alert('微博内容HTML不能为空');
		   return false;  
    }
	return true;
})
</script>