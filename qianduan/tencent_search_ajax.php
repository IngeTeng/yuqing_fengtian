<?php
include_once('adminv/inc_dbconn.php');
include_once("spider_function.php");

$user_id=$_COOKIE['user_id'];
$keyword = $_POST["w"];

function parse_web($web)
{
	$end_pos = strpos($web, "<!--  热门广播 end  -->");
	if ($end_pos > 0)
	
	$web = substr($web, $end_pos);
	
		$data = str_replace("\n","", $web);
		preg_match_all('/<li\s\sid=\"[^>]+>.*?<\/li>/',$data,$out);
		$countli=count($out[0]);
		for($i=0;$i<$countli;$i++)
		{
			if (strstr($out[0][$i], '<div class="replyBox">'))
			{
				$p = strpos($out[0][$i], '<div class="replyBox">');
				if ($p > 0)
				{
					$out[0][$i] = substr($out[0][$i], $p + strlen('<div class="replyBox">'));
				}
				$weibo[$i]["head_img"]="";
				preg_match('/gender=\"他\"[^>]+>(.*?)<\/a>/',$out[0][$i],$data);
				$weibo[$i]["author"]=$data[1];
				preg_match('/<div>(.*?)<\/div>/',$out[0][$i],$data);
				$data[1]=strip_tags($data[1]);
				$weibo[$i]["title"]=$data[1];
				preg_match('/rel=\"([^\"]+)"/',$out[0][$i],$data);
		//		$time=date('Y-m-d H:i:s',$data[1]);
				$weibo[$i]["date"]=$data[1];
				preg_match('/http:\/\/t.qq.com\/p\/t\/(\d*)/',$out[0][$i],$data);
				$weibo[$i]["mid"]=$data[1];
				if (strstr($out[0][$i], "腾讯机构认证"))
        		{
               	 	$weibo[$i]["isv"]="腾讯机构认证";
            	}
        		else if (strstr($out[0][$i], "腾讯个人认证"))
            	{
                	$weibo[$i]["isv"]="腾讯个人认证";
            	}
            	else if (strstr($out[0][$i], 'title="微博之星"'))
            	{
            		$weibo[$i]["isv"]="微博之星";
            	}
            	else
            	{
                	$weibo[$i]["isv"]="";
            	}
            	$weibo[$i]["url"] = "http://t.qq.com/p/t/".$weibo[$i]["mid"];

				continue;
			}
			preg_match('/<img\ssrc=\"([^\"]+)\"/',$out[0][$i],$data);//截取头像地址
			$weibo[$i]["head_img"]=$data[1];
			preg_match('/gender=\"他\"[^>]+>(.*?)<\/a>/',$out[0][$i],$data);
			$weibo[$i]["author"]=$data[1];
			preg_match('/<div\sclass=\"msgCnt\">(.*?)<\/div>/',$out[0][$i],$data);//截取内容
			$data[1]=strip_tags($data[1]);
		//	$data[1]=htmlspecialchars($data[1]); //去除标签
			$weibo[$i]["title"]=$data[1];
			preg_match('/rel=\"([^\"]+)"/',$out[0][$i],$data);//截取时间
	//		$time=date('Y-m-d H:i:s',$data[1]);
			$weibo[$i]["date"]=$data[1];
			preg_match('/http:\/\/t.qq.com\/p\/t\/(\d*)/',$out[0][$i],$data);
			$weibo[$i]["mid"]=$data[1];
			
			if (strstr($out[0][$i], "腾讯机构认证"))
        	{
                $weibo[$i]["isv"]="腾讯机构认证";
            }
        	else if (strstr($out[0][$i], "腾讯个人认证"))
            {
                $weibo[$i]["isv"]="腾讯个人认证";
            }
            else if (strstr($out[0][$i], 'title="微博之星"'))
            {
            	$weibo[$i]["isv"]="微博之星";
            }
            else
            {
                $weibo[$i]["isv"]="";
            }
            $weibo[$i]["url"] = "http://t.qq.com/p/t/".$weibo[$i]["mid"];

		}
		return $weibo;	
}


function get_infos($keyword, $qq_cookies)
{
	if ($keyword == "")
	{
		return array();
	}
	$url = "http://search.t.qq.com/index.php?k=".urlencode($keyword)."&pos=174&s_dup=1&s_source=&s_m_type=1";
	$qqcount=count($qq_cookies);
	for ($i = 0; $i < 5; $i++)
	{	
		$i=rand(1,$qqcount)-1;
		$content = html_get($url, $qq_cookies[$i]["cookie"]);
		if ($content != "")
		{
			break;
		}
	}
//print_r($content);
	$infos = parse_web($content);
	return $infos;	
}

	$sql = "select qc_id, qq_cookie from qq_cookies where status = 1";
	$qq_cookies = array();
	$res = mysql_query($sql);
	while ($row = mysql_fetch_array($res))
	{
		$qq_cookies[] = array("id"=>$row["qc_id"] ,"cookie"=>$row["qq_cookie"]);
	}
	mysql_free_result($res);

$infos = get_infos($keyword, $qq_cookies);

$result = array();
for ($i = 0; $i < count($infos); $i++)
{
	$info = $infos[$i];
	$title = addslashes($info["title"]);
	$content = "";
	$media = "腾讯";
    $sql = "insert into news_search(article_url,article_title,article_content,article_pubtime,".
        	   							"article_addtime,article_summary,media,article_source,article_channel,user_id,keyword,a_type,search_engine,author)".
        	" values('".$info["url"]."','".$title."','".$content."',".$info["date"].",".time().",'".$content."',".
        	   	"'".$media."','','',".$user_id.",'".$keyword."',4,'腾讯','".$info["author"]."')";
    if(mysql_query($sql))
    {
    	$info["media"] = $info["author"];
    	$info["search_engine"] = '腾讯微博';
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