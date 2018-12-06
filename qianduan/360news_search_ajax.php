<?php
include_once('adminv/inc_dbconn.php');
include_once("spider_function.php");

$user_id=$_COOKIE['user_id'];
$keyword = $_POST["w"];


function get_same_news_page($suburl)
{
	$url = "http://news.so.com/".$suburl;
//print_r($url."\n");
	$content = html_get($url);
//print_r($content);
	$content = str_replace("\n","", $content);

	$p = strpos($content, '<ul class="result" id="news">');
	if ($p > 0)
	{
		$content = substr($content, $p);
	}
	$start_str = '<li class="res-list">';
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
		preg_match('/<h3>(.*?)<\/a>/',$div,$data);//截取标题
		$infos[$i]["title"]=strip_tags($data[1]);
		$pc = getXmlValue($div, '<p>', '</li>');
		$infos[$i]["content"]=strip_tags($pc);
		
		$newsinfo = getXmlValue($div, '<span ', '</span>');
		if ($newsinfo == "")
		{
			continue;
		}
		$reply_num = 0; 
		$infos[$i]["media"] = getXmlValue($newsinfo, '<em>', '</em>');
		$time_str = getXmlValue($newsinfo, 'title="', '"');
		
		$infos[$i]["newsinfo"] = $newsinfo;
		$infos[$i]["time_str"] = $time_str;
		$infos[$i]["date"] = strtotime($infos[$i]["time_str"]);
		$i++;
	}
	return $infos;	
}

function get_infos($keyword)
{
	$url = "http://news.so.com/ns?j=0&rank=pdate&src=srp&q=".urlencode($keyword)."&pn=1";
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
	$p = strpos($content, '<ul class="result" id="news">');
	if ($p > 0)
	{
		$content = substr($content, $p);
	}
	$start_str = '<li class="res-list">';
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
		preg_match('/<h3>(.*?)<\/a>/',$div,$data);//截取标题
		$infos[$i]["title"]=strip_tags($data[1]);
		$pc = getXmlValue($div, '<p>', '</li>');
		$infos[$i]["content"]=strip_tags($pc);
		
		$newsinfo = getXmlValue($div, '<span ', '</span>');
		if ($newsinfo == "")
		{
			continue;
		}
		$reply_num = 0; 
		$infos[$i]["media"] = getXmlValue($newsinfo, '<em>', '</em>');
		$time_str = getXmlValue($newsinfo, 'title="', '"');
		
		$infos[$i]["newsinfo"] = $newsinfo;
		$infos[$i]["time_str"] = $time_str;
		$infos[$i]["date"] = strtotime($infos[$i]["time_str"]);
		$i++;
	}
	
	
	preg_match_all('/href=\"([^\"]+)\"/iU',$content_org,$out);
	for ($k = 0; $k < count($out[1]); $k++)
	{
		if (!strstr($out[1][$k], "q=rptid:"))
		{
			continue;
		}
		$infos_2 = get_same_news_page($out[1][$k]);
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
        	   	"'".$media."','','',".$user_id.",'".$keyword."',1,'360新闻')";
    if(mysql_query($sql))
    {
    	$info["media"] = $media;
    	$info["search_engine"] = '360新闻';
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