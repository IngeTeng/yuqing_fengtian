<?php
include_once('adminv/inc_dbconn.php');
include_once("spider_function.php");

$user_id=$_COOKIE['user_id'];
$keyword = $_POST["w"];

//$keyword = $_GET["w"];
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
	$info["author"] = $a[1][0];
	
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
	preg_match_all('/<em>(.*)<\/em>/iU', $content, $a);
	if ($a == null)
	{
		return false;
	}
	$info["title"] = preg_replace("/<(.*?)>/si","",$a[1][0]);
	
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
    preg_match_all('/transparent.gif\" title= \"([^\"]+)\"/', $content, $a);
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
function get_infos($keyword)
{
	if ($keyword == "")
	{
		return array();
	}
	$url = "http://s.weibo.com/weibo/". urlencode($keyword) ."&scope=ori&nodup=1&xsort=time";
	for ($i = 0; $i < 5; $i++)
	{
		$content = html_get($url);
		if ($content != "")
		{
			break;
		}
	}
//print_r($content);
	$content = str_replace("\\\n","",$content);
	$content = str_replace("<dl class=\\\"comment\\\" style=\\\"display: none;\\\" node-type=\\\"feed_list_media_disp\\\"><\\/dl>", "", $content);
	preg_match_all('/<dl class=\\\"feed_list W_linecolor(.*)\\/dl>/iU', $content, $dls);
	$infos = array();
	for ($i = 0; $i < count($dls[0]); $i++)
	{
		get_weibo_info($dls[0][$i], $info);
		$infos[] = $info;
	}
	return $infos;	
}

$infos = get_infos($keyword);

$result = array();
for ($i = 0; $i < count($infos); $i++)
{
	$info = $infos[$i];
	$title = addslashes($info["title"]);
	$content = "";
	$media = "新浪";
    $sql = "insert into news_search(article_url,article_title,article_content,article_pubtime,".
        	   							"article_addtime,article_summary,media,article_source,article_channel,user_id,keyword,a_type,search_engine,author)".
        	" values('".$info["url"]."','".$title."','".$content."',".$info["date"].",".time().",'".$content."',".
        	   	"'".$media."','','',".$user_id.",'".$keyword."',4,'新浪微博','".$info["author"]."')";
    if(mysql_query($sql))
    {
    	$info["media"] = $info["author"];
    	$info["search_engine"] = '新浪微博';
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

?>