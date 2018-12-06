<?php
include_once('check_user.php');
//print_r($_POST);

$search_num = $_POST["search_num"];
$a_type = $_POST["a_type"];
$insert_num = 0;
$already_num = 0;
for ($i = 0; $i < $search_num; $i++)
{
	$uk_id = $_POST["uk_id_".$i];
	if ($uk_id == 0)
	{
		 continue;
	}
	$article_id = $_POST["id_".$i];
	$property = $_POST["property_".$i];
	$sql = "select * from news_search where article_id = ".$article_id;	
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$article_url = $row["article_url"];
	$article_title = addslashes($row["article_title"]);
	$article_content = addslashes($row["article_content"]);
	$article_pubtime = $row["article_pubtime"];
	$article_addtime = $row["article_addtime"];
	$article_summary = addslashes($row["article_summary"]);
	$article_comment = addslashes($row["article_comment"]);
	$article_source = addslashes($row["article_source"]);
	$article_channel = addslashes($row["article_channel"]);
	$media = $row["media"];
	$user_id = $row["user_id"];
	$author = $row["author"];
	
	mysql_free_result($res);
	
	$sql = "select * from user_keywords where uk_id = ".$uk_id;
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$c_id = $row["c_id"];
	mysql_free_result($res);

	$table_name = "news_article";
	$key_name = "news_key";
	$a_name = "新闻";
	switch($a_type)
	{
		case 1:
			$table_name = "news_article";
			$key_name = "news_key";
			$a_name = "新闻";
			break;
		case 2:
			$table_name = "bbs_article";
			$key_name = "bbs_key";
			$a_name = "论坛";
			break;
		case 4:
			$table_name = "weibo_article";
			$key_name = "weibo_key";
			$a_name = "微博";
			break;
		case 6:
			$table_name = "weixin_article";
			$key_name = "weixin_key";
			$a_name = "微信";
			break;
	}
	
	$aid = 0;
    $sql = "select article_id from $table_name where article_url = '".$article_url."'";
	$res = mysql_query($sql);
	if (mysql_num_rows($res) > 0)
	{
		$row = mysql_fetch_array($res);
		$aid = $row["article_id"];
		$already_num++;
	}
	else
	{
		$sql = "";
		switch($a_type)
		{
		case 1:
			$sql = "insert into news_article(article_url,article_title,article_content,article_pubtime,".
        	   							"article_addtime,article_summary,media,article_source,article_channel)".
        	   " values('".$article_url."','".$article_title."','".$article_content."',".$article_pubtime.",".time().",'".$article_content."',".
        	   	"'".$media."','','')";
			break;
		case 2:
			$sql = "insert into bbs_article(article_url,article_title,article_content,article_pubtime,".
        	   							"article_addtime,article_summary,media,author)".
        	   " values('".$article_url."','".$article_title."','".$article_content."',".$article_pubtime.",".time().",'".$article_content."',".
        	   	"'".$media."','".$author."')";
			break;
		case 4:
			$sql = "insert into weibo_article(article_url,article_title,article_pubtime,".
        	   							"article_addtime,media,author)".
        	   " values('".$article_url."','".$article_title."',".$article_pubtime.",".time().",".
        	   	"'".$media."','".$author."')";
			break;
		case 6:
			$sql = "insert into weixin_article(article_url,article_title,article_content,article_pubtime,".
        	   							"article_addtime,article_summary,media)".
        	   " values('".$article_url."','".$article_title."','".$article_content."',".$article_pubtime.",".time().",'".$article_content."',".
        	   	"'".$media."')";
			break;
		}
		
        if (mysql_query($sql))
        {
			$aid = mysql_insert_id();
		}
		else
		{
			continue;
		}
	}
	
	$sql = "select id from $key_name where user_id = $user_id and article_id = $aid and c_id = $c_id";
	$res = mysql_query($sql);
	$num = mysql_num_rows($res);
	mysql_free_result($res);
	if ($num > 0)
	{
		continue;
	}
	$sql = 	"insert into $key_name(uk_id, article_id, article_property, article_pubtime,user_id,article_addtime,c_id,audit_status) values(".
					    $uk_id. "," . $aid . ",". $property . ",". $article_pubtime.",".$user_id.",".time().",".$c_id.",1)";
	mysql_query($sql);
	$insert_num++;
}


?>
<script>
alert("共成功导入 <?php echo $insert_num ?> 条<?php echo $a_name ?>");
window.location.href="http://42.121.110.248/yuqing/news_search.php";
</script>