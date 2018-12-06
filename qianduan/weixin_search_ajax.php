<?php
include_once('adminv/inc_dbconn.php');
include_once("spider_function.php");

$user_id=$_COOKIE['user_id'];
$keyword = $_POST["w"];


function get_infos($keyword)
{
	$url = "http://weixin.sogou.com/weixin?query=".urlencode($keyword)."&tsn=0&p=40040100&interation=&type=2&ie=utf8&num=100";
//print_r($url."\n");
	$content = html_get($url);
//print_r($content);
	$content = str_replace("\n","", $content);
	$start_str = '<div class="wx-rb wx-rb3"';
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
		preg_match('/<h4>(.*?)<\/h4>/',$div,$data);//截取标题
		$infos[$i]["title"]=strip_tags($data[1]);
		$pc = getXmlValue($div, '<p id="sogou_vr', '</p>');
		$p = strpos($pc, ">");
		if ($p > 0)
		{
			$pc = substr($pc, $p+1);
		}
		$infos[$i]["content"]=strip_tags($pc);
		
		$infos[$i]["date"] = getXmlValue($div, "vrTimeHandle552write('", "')");
		$pc = getXmlValue($div, '<a href="', '</a>');
		$p = strpos($pc, ">");
		if ($p > 0)
		{
			$pc = substr($pc, $p+1);
		}
		$infos[$i]["author"]=strip_tags($pc);
		
		$i++;
	}
	return $infos;	
}

function get_author($url)
{
	$content = html_get($url);
	$author = getXmlValue($content, 'nickname = "', '";');
	return $author;
}

$infos = get_infos($keyword);
$result = array();
for ($i = 0; $i < count($infos); $i++)
{
	$info = $infos[$i];
	$info["author"] = get_author($info["url"]);
	$title = addslashes($info["title"]);
	$content = addslashes($info["content"]);
	$media = $info["author"];
    $sql = "insert into news_search(article_url,article_title,article_content,article_pubtime,".
        	   							"article_addtime,article_summary,media,article_source,article_channel,user_id,keyword,a_type,search_engine)".
        	" values('".$info["url"]."','".$title."','".$content."',".$info["date"].",".time().",'".$content."',".
        	   	"'".$media."','','',".$user_id.",'".$keyword."',6,'微信')";
    if(mysql_query($sql))
    {
    	$info["media"] = $media;
    	$info["search_engine"] = '微信';
    	$info["id"] = mysql_insert_id();
    	$info["title"] = str_replace($keyword,"<font color='#FF0000'>$keyword</font>", $info["title"]);
    	$info["time_str"] = date("Y-m-d H:i:s", $info["date"]);

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