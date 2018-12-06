<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>新闻数据添加</title>
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
function html_get($url, $cookie="", $referer="")
{   
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    if ($cookie != "")
    {
    	$coo = "Cookie: " . $cookie;
    	$headers[] = $coo;
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($referer != "")
    {
        curl_setopt($ch,CURLOPT_REFERER,$referer);
    }
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

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

function article_property($host, $port, $title, $content)
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

		$result = strstr($data, '<content');
        $pstart = strpos($result, '<content');
        $content_array = array();
        $i=0;
        while ($pstart > 0 || $pstart === 0){
			$pend = strpos($result, '</content>');
			$record = substr($result, $pstart, $pend - $pstart); 
			$property = getXmlValue($record, '<cid>', "</cid>");
			if($property==2){  
			    return $property;
			}
			$result = substr($result, $pend+ strlen('</content>'));
			$pstart = strpos($result, '<content');
			$content_array[$i]=$property;
			$i++;
        }

		if(count($content_array)>0){ 
		    $property=1;
		    return $property;

	    }		
		return $property;
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

if(!isset($_COOKIE['user_id'])){
   echo "<script>alert('您尚未登陆！');location.href='index.php';</script>";
}else{
  require_once('adminv/inc_dbconn.php');
  require_once('header.php');
  $user_id=$_COOKIE['user_id'];
  
  $title = "请先选择对应关键字，并输入新闻网址，标题等";
  if ($_POST["k_id"] != "")
  {
  	 $url = $_POST["url"];
  	 $k_id = $_POST["k_id"];
  	 $title = addslashes($_POST["title"]);
        
        $content = addslashes($_POST["content"]);
     	$media = addslashes($_POST["media"]);

		$date = strtotime($_POST["ptime"]);
  	 if ($url != "" && $date != 0 && $title != "")
  	 {
  	 	
		
//print_r($info);
//return;
 
		$title2 = iconv("utf8","gbk", $title);
        $content2 = iconv("utf8","gbk", $content);
        $h = "10.132.58.171";
        $p = 5040;
        $property = article_property($h, $p, $title2, $content2);
 
 
        $aid = 0;
        $sql = "select article_id from news_article where article_url = '".$url."'";
		$res = mysql_query($sql);
		if (mysql_num_rows($res) > 0)
		{
			$row = mysql_fetch_array($res);
			$aid = $row["article_id"];
		}
		else
		{
		
			
        		$sql = "insert into news_article(article_url,article_title,article_content,article_summary,article_pubtime,".
        	   							"article_addtime,media)".
        	   " values('".$url."','".$title."','".$content."','".$content."',".$date.",".time().",'".$media."')";
//echo($sql);
        		if (mysql_query($sql))
        		{
					$aid = mysql_insert_id();
	//				echo("insert url:".$info["url"]."\n");
				}
				else
				{
	//				echo("error:".mysql_error());
				}
			
        }
        $sql = "select * from user_keywords where user_id = ".$user_id . " and k_id = ". $k_id;
 //       echo($sql);
        $res = mysql_query($sql);
        if ($row = mysql_fetch_array($res))
        {
        	$uk_id = $row["uk_id"];
        	$c_id = $row["c_id"];
        	$sql = "select id from news_key where user_id = $user_id and article_id = $aid and c_id = $c_id";
		    $res = mysql_query($sql);
		    $num = mysql_num_rows($res);
		    mysql_free_result($res);
		    if ($num == 0)
		    {
		       $sql = 	"insert into news_key(uk_id, article_id, article_property, article_pubtime,user_id,article_addtime,c_id) values(".
					    $uk_id. "," . $aid . ",". $property . ",". $date.",".$user_id.",".time().",".$c_id.")";
			    mysql_query($sql);
		    }
				
        }
        $title = "[添加成功]".$title;
  	 }
  }
?>
<div class="Navigation">
	<ul>
    	<li><a href="ordinary_home.php">首页</a></li>
        <li><a href="#">新闻数据添加</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
  <form action="" method="post" name="keywordForm">
  <div style="margin-left:300px;"><?php echo $title ?></div>
    <div class="div" style="width:800px;height:400px">
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
	      <label for="keyword" class="label">新闻网址：</label>
		  <input type="text" id="url" name="url" style="width:600px;" /> <br>完整http://开头
		  
		</li>
		<li style="text-align: left;margin-left:10px;">
	      <label for="keyword" class="label">新闻标题：</label>
		  <input type="text" id="title" name="title" style="width:600px;" /> 
		</li>
		<li style="text-align: left;margin-left:10px;">
	      <label for="keyword" class="label">媒体名称：</label>
		  <input type="text" id="media" name="media" style="width:600px;" /> 
		</li>
		<li style="text-align: left;margin-left:10px;">
	      <label for="keyword" class="label">发布时间：</label>
		  <input type="text" id="ptime" name="ptime" style="width:600px;" /> <br>格式： 2016-03-09 09:00:00
		</li>
		<li style="text-align: left;margin-left:10px;">
	      <label for="keyword" class="label">内容摘要：</label>
		  <textarea id="content" name="content" style="width:600px;height:50px" ></textarea>
		</li>
		<li style="text-align: left;margin-left:10px;">
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
    var url=$('#url').val();
    var kong = /[ ]/ig;
	if(kong.test(url)){
           alert('微信网址不能含有空格');
		   return false;
    } 
    if(url==""){
	       alert('微信网址不能为空');
		   return false;  
    }
	return true;
})
</script>