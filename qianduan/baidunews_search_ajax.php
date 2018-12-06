<?php
include_once('adminv/inc_dbconn.php');
include_once("spider_function.php");

$user_id=$_COOKIE['user_id'];
$keyword = $_POST["w"];


function get_baidu_same_news_page($suburl)
{
	$url = "http://news.baidu.com".$suburl;
//print_r($url."\n");
	$content = html_get($url);
//print_r($content);
	$content = str_replace("\n","", $content);

	$p = strpos($content, '<div id="content_left">');
	if ($p > 0)
	{
		$content = substr($content, $p);
	}
	$start_str = '<li class="result"';
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
		preg_match('/<h3 class=\"c-title\">(.*?)<\/h3>/',$div,$data);//截取标题
		$infos[$i]["title"]=strip_tags($data[1]);
		$pc = getXmlValue($div, '<div class="c-summary">', '<a href=');
		$infos[$i]["content"]=strip_tags($pc);
		
		$newsinfo = getXmlValue($div, '<span class="c-author">', 'span>');
		if ($newsinfo == "")
		{
			continue;
		}
		$reply_num = 0; 
		$infos[$i]["media"] = getXmlValue($newsinfo, '&nbsp;', '&nbsp;');
		$time_str = getXmlValue($newsinfo, $infos[$i]["media"].'&nbsp;', '</');
		
		$infos[$i]["newsinfo"] = $newsinfo;
		$infos[$i]["time_str"] = $time_str;
		$infos[$i]["date"] = strtotime($infos[$i]["time_str"]);
		$i++;
	}
	return $infos;	
}
function get_baidu_infos($keyword)
{
	if ($keyword == "")
	{
		return array();
	}
	$url = "http://news.baidu.com/ns?from=news&cl=2&q1=".urlencode($keyword)."&s=1&tn=newsdy&ct1=0&ct=0&rn=20&ie=utf-8";
//print_r($url."\n");
	for ($i = 0; $i < 5; $i++)
	{
		$content = html_get($url);
		if ($content != "")
		{
			break;
		}
	}
	$content_org = $content;
//print_r($content);
	$content = str_replace("\n","", $content);
	$p = strpos($content, '<div id="content_left">');
	if ($p > 0)
	{
		$content = substr($content, $p);
	}
	$start_str = '<li class="result"';
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
		preg_match('/<h3 class=\"c-title\">(.*?)<\/h3>/',$div,$data);//截取标题
		$infos[$i]["title"]=strip_tags($data[1]);
		$pc = getXmlValue($div, '<div class="c-summary">', '<a href=');
		$infos[$i]["content"]=strip_tags($pc);
		
		$newsinfo = getXmlValue($div, '<span class="c-author">', 'span>');
		if ($newsinfo == "")
		{
			continue;
		}
		$reply_num = 0; 
		$infos[$i]["media"] = getXmlValue($newsinfo, '&nbsp;', '&nbsp;');
		$time_str = getXmlValue($newsinfo, $infos[$i]["media"].'&nbsp;', '</');
		
		$infos[$i]["newsinfo"] = $newsinfo;
		$infos[$i]["time_str"] = $time_str;
		$infos[$i]["date"] = strtotime($infos[$i]["time_str"]);
		$i++;
	}
	
	preg_match_all('/href=\"([^\"]+)\"/iU',$content_org,$out);
	for ($k = 0; $k < count($out[1]); $k++)
	{
		if (!strstr($out[1][$k], "+cont:"))
		{
			continue;
		}
		$infos_2 = get_baidu_same_news_page($out[1][$k]);
//print_r($infos_2);
		for ($m = 0; $m < count($infos_2); $m++)
		{
			$infos[$i] = $infos_2[$m];
			$i++;
		}
		
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

$infos = get_baidu_infos($keyword);

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
        	   	"'".$media."','','',".$user_id.",'".$keyword."',1,'百度新闻')";
    if(mysql_query($sql))
    {
    	$info["media"] = $media;
    	$info["search_engine"] = '百度新闻';
    	$info["id"] = mysql_insert_id();
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