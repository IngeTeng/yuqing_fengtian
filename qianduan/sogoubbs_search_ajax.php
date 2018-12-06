<?php
include_once('adminv/inc_dbconn.php');
include_once("spider_function.php");

$user_id=$_COOKIE['user_id'];
$keyword = $_POST["w"];


function get_infos($keyword)
{
	$url = "http://www.sogou.com/web?query=".urlencode($keyword)."&interation=196648&ie=utf8&sourceid=inttime_day&tsn=1&num=100";
//print_r($url."\n");
	$content = html_get($url);
//print_r($content);
	$content = str_replace("\n","", $content);
	$start_str = 'class="rb">';
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
		if (!strstr($url, "thread"))
		{
			continue;
		}
		$infos[$i]["url"]= str_replace("&amp;", "&", $url);
		preg_match('/<h3 class=\"pt\">(.*?)<\/h3>/',$div,$data);//截取标题
		$infos[$i]["title"]=strip_tags($data[1]);
		$pc = getXmlValue($div, '<div class="ft"', '</div>');
		$p = strpos($pc, ">");
		if ($p > 0)
		{
			$pc = substr($pc, $p+1);
		}
		$infos[$i]["content"]=strip_tags($pc);
		
		$bloginfo = getXmlValue($div, '<div class="bloginfo">', 'div>');
		if ($bloginfo == "")
		{
			continue;
		}
		$reply_num = getXmlValue($bloginfo, '回复：', '　');
		$reply_num = intval($reply_num, 10); 
		$time_str = getXmlValue($bloginfo, '发帖时间：', '</');
		if (!strstr($time_str, ":"))
		{
			$time_str .= date(" H:i");
		}
		$infos[$i]["bloginfo"] = $bloginfo;
		$infos[$i]["reply_num"] = $reply_num;
		$infos[$i]["time_str"] = $time_str;
		$infos[$i]["date"] = strtotime($time_str);
		
		$cateinfo = getXmlValue($div, '<cite id="cacheresult_info_', '</cite>');
		$p = strpos($cateinfo, '>');
		if ($p > 0)
		{
			$cateinfo = substr($cateinfo, $p+1);
		}
		$cis = explode(" - ", $cateinfo);
		if (count($cis) == 3)
		{
			$infos[$i]["media"] = $cis[0];
		}
		else
		{
			$infos[$i]["media"] = "";
		}
		
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
        	   	"'".$media."','','',".$user_id.",'".$keyword."',2,'搜狗论坛')";
    if(mysql_query($sql))
    {
    	$info["media"] = $media;
    	$info["search_engine"] = '搜狗论坛';
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
//echo(json_encode($infos));
?>