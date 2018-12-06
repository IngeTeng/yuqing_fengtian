<?php
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$title="修改丢弃规则";
	$id=$_GET['id'];
	$query="select url from throw_url_list where id=$id";
	$res=mysql_query($query);
	$row=mysql_fetch_array($res);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title> <?php echo $title;?> </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="author" content="liuxiao@WiiPu -- http://www.wiipu.com" />
		<link rel="stylesheet" href="style2.css" type="text/css"/>
	</head>
	<body>
		<div class="bgintor">
		<div class="tit1">
			<ul>
				<li><a href="#"><?php echo $title;?></a> </li>
			</ul>		
		</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：媒体管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span>
		</div>
		<div class="fromcontent">
			<form action="throw_do.php?op=edit" method="post">
			    <input type="hidden" value="<?php echo $id;?>" name="id" />
				<p>丢弃规则：<input class="in1" type="text" name="throw" id="throw" value="<?php echo $row['url'];?>"/></p>
				<div class="btn">
					<input type="image" src="images/submit1.gif" width="56" height="20" alt="提交"/>
				</div>
			</form>
		</div>
	</div>
  </div>
 </body>
</html>
