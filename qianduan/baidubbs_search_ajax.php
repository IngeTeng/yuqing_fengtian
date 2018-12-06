<?php
include_once('adminv/inc_dbconn.php');
include_once("spider_function.php");

$user_id=$_COOKIE['user_id'];
$keyword = $_POST["w"];


function get_infos($keyword)
{
	$url = "http://www.baidu.com/s?tn=baidurt&rtt=4&wd=".urlencode($keyword)."&pbs=1&bsst=1&ie=utf-8&rn=100";
//print_r($url."\n");
	for ($i = 0; $i < 5; $i++)
	{
		$content = html_get($url);
		if ($content != "")
		{
			break;
		}
	}
//print_r($content);
	$content = str_replace("\n","", $content);
	$p = strpos($content, '<div class="content">');
	if ($p > 0)
	{
		$content = substr($content, $p);
	}
	$start_str = '<table ';
	$pstart = strpos($content, $start_str);
	$i = 0;
	while ($pstart > 0 || $pstart === 0)
	{
		$content = substr($content, $pstart + strlen($start_str));
		$pstart = strpos($content, $start_str);
		if ($pstart > 0)
		{
			$div = substr($content, 0, $pstart);
		}
		else
		{
			$div = $content;
		}
		$url  = getXmlValue($div, 'href="','"');
		$infos[$i]["url"]= str_replace("&amp;", "&", $url);
		preg_match('/<h3 class=\"t\">(.*?)<\/h3>/',$div,$data);//截取标题
		$infos[$i]["title"]=strip_tags($data[1]);
		$pc = getXmlValue($div, '</div>', '<br>');
		$infos[$i]["content"]=strip_tags($pc);
		
		$bloginfo = getXmlValue($div, '<div class="realtime">', '</div>');
		if ($bloginfo == "")
		{
			continue;
		}
		$reply_num = 0; 
		$p = strpos($bloginfo, '&nbsp;-&nbsp;');
		if ($p > 0)
		{
			$d = substr($bloginfo, $p+13);
			$infos[$i]["media"] = substr($bloginfo, 0, $p);
		}
		else
		{
			$infos[$i]["media"] = "";
			$d = $bloginfo;
		}
		
		$p = strpos($d, "分");
		$sec = 60;
		if (!$p)
		{
			$p = strpos($d, "小时");
			$sec = 3600;
		}
		if (!$p)
		{
			$p = strpos($d, "天");
			$sec = 86400;
		}
		if (!$p)
		{
			$p = strpos($d, "月");
			$sec = 86400 * 30;
		}
		if (!$p)
		{
			$p = strpos($d, "年");
			$sec = 86400 * 365;
		}
		if ($p > 0)
		{
			$dd = substr($d, 0, $p);
			$infos[$i]["date"] = time() - $dd * $sec;
		}
		else
		{
			$infos[$i]["date"]= time();		
		}
		
		
		$infos[$i]["bloginfo"] = $bloginfo;
		$infos[$i]["reply_num"] = $reply_num;
		$infos[$i]["time_str"] = $d;
		
		$i++;
	}
	return $infos;	
}

$query="select domain,media_name from media_list";
$res=mysql_query($query);
$i=0;
$media_list=array();
while($row=mysql_fetch_array($res))
{
	$media_list[$i]['domain']=$row['domain'];
	$media_list[$i]['media_name']=$row['media_name'];
	$i++;
}
mysql_free_result($res);

$infos = get_infos($keyword);

$result = array();
for ($i = 0; $i < count($infos); $i++)
{
	$info = $infos[$i];
	$title = addslashes($info["title"]);
	$content = addslashes($info["content"]);
	$media = get_media($info["url"], $media_list);
    if ($media == "")
    {
        $media = addslashes($info["media"]);
    }
    $sql = "insert into news_search(article_url,article_title,article_content,article_pubtime,".
        	   							"article_addtime,article_summary,media,article_source,article_channel,user_id,keyword,a_type,search_engine)".
        	" values('".$info["url"]."','".$title."','".$content."',".$info["date"].",".time().",'".$content."',".
        	   	"'".$media."','','',".$user_id.",'".$keyword."',2,'百度论坛')";
    if(mysql_query($sql))
    {
    	$info["media"] = $media;
    	$info["search_engine"] = '百度论坛';
    	$info["id"] = mysql_insert_id();
    	$info["time_str"] = date("Y-m-d H:i:s", $info["date"]);
    	$info["title"] = str_replace($keyword,"<font color='#FF0000'>$keyword</font>", $info["title"]);

    	$result[] = $info;
    }
    else
    {
    	//$result[] = array("error"=>mysql_error());
    }
}
echo(json_encode($result));

?>