<?php
include_once('adminv/inc_dbconn.php');
include_once("spider_function.php");

$user_id=$_COOKIE['user_id'];
$keyword = $_POST["w"];


function get_sogou_same_news_page($suburl)
{
	$url = "http://news.sogou.com/news?&clusterId=&p=42230305&query=".urlencode($keyword)."&mode=1&media=&sort=1&num=100&ie=utf8";
	$url = "http://news.sogou.com/news".$suburl;
//print_r($url."\n");
	$content = html_get($url);
//print_r($content);
	$content = str_replace("\n","", $content);
	$start_str = '<div class="rb">';
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
		
		preg_match('/<b>(.*?)<\/b>/',$div,$data);//截取标题
		$infos[$i]["title"]=strip_tags($data[1]);
		
		$infos[$i]["media"] = getXmlValue($div, '<cite title="', '">');
		$time_str = getXmlValue($div, '<cite title="'.$infos[$i]["media"].'">'.$infos[$i]["media"]."&nbsp;",'<');
		
		$pc = getXmlValue($div, '<div class="ft">', '</div>');
		$infos[$i]["content"]=strip_tags($pc);
		$p = strpos($infos[$i]["content"], '&gt;&gt;');
		if ($p > 0)
		{
			$infos[$i]["content"] = substr($infos[$i]["content"], 0, $p);
		}
		
		$infos[$i]["time_str"] = $time_str;
		$infos[$i]["date"] = strtotime($time_str);
		
		$i++;
	}
	return $infos;	
}

function get_sogou_infos($keyword)
{
	if ($keyword == "")
	{
		return array();
	}
	$url = "http://news.sogou.com/news?&clusterId=&p=42230305&query=".urlencode($keyword)."&mode=1&media=&sort=1&num=20&ie=utf8";
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
	$content_org = $content;
	$start_str = '<div class="rb">';
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
		
		preg_match('/<b>(.*?)<\/b>/',$div,$data);//截取标题
		$infos[$i]["title"]=strip_tags($data[1]);
		
		$infos[$i]["media"] = getXmlValue($div, '<cite title="', '">');
		$time_str = getXmlValue($div, '<cite title="'.$infos[$i]["media"].'">'.$infos[$i]["media"]."&nbsp;",'<');
		
		$pc = getXmlValue($div, '<div class="ft">', '</div>');
		$infos[$i]["content"]=strip_tags($pc);
		$p = strpos($infos[$i]["content"], '&gt;&gt;');
		if ($p > 0)
		{
			$infos[$i]["content"] = substr($infos[$i]["content"], 0, $p);
		}
		
		$infos[$i]["time_str"] = $time_str;
		$infos[$i]["date"] = strtotime($time_str);
		
		$i++;
	}
	
	preg_match_all('/href=\"([^\"]+)\"/iU',$content_org,$out);
//print_r($out);
	for ($k = 0; $k < count($out[1]); $k++)
	{

		if (!strstr($out[1][$k], "clusterId=http"))
		{
			continue;
		}
		$infos_2 = get_sogou_same_news_page($out[1][$k]);
//print_r($infos_2);
		for ($m = 0; $m < count($infos_2); $m++)
		{
			$infos[$i] = $infos_2[$m];
			$i++;
		}
		//sleep(rand(1,10));
		//sleep(1);
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

$infos = get_sogou_infos($keyword);
$result = array();
for ($i = 0; $i < count($infos); $i++)
{
	$info = $infos[$i];
	$title = addslashes(iconv("gbk", "utf8",$info["title"]));
        
    $content = addslashes(iconv("gbk", "utf8",$info["content"]));
	$media = get_media($info["url"], $media_list);
    if ($media == "")
    {
        $media = addslashes(iconv("gbk", "utf8",$info["media"]));
    }
    $sql = "insert into news_search(article_url,article_title,article_content,article_pubtime,".
        	   							"article_addtime,article_summary,media,article_source,article_channel,user_id,keyword,a_type,search_engine)".
        	" values('".$info["url"]."','".$title."','".$content."',".$info["date"].",".time().",'".$content."',".
        	   	"'".$media."','','',".$user_id.",'".$keyword."',1,'搜狗新闻')";
    if(mysql_query($sql))
    {
    	$r["title"] = str_replace($keyword,"<font color='#FF0000'>$keyword</font>", $title);
    	$r["time_str"] = $info["time_str"];
    	$r["url"] = $info["url"];
    	$r["media"] = $media;
    	$r["search_engine"] = '搜狗新闻';
    	$r["id"] = mysql_insert_id();
    	$result[] = $r;
    }
    else
    {
    //	print_r(mysql_error());
    }
}

echo(json_encode($result));
//echo(json_encode($infos));
?>