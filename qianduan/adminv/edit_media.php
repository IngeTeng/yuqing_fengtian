<?php
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$title="修改媒体信息";
	$m_id=$_GET['m_id'];
	$query="select media_name,domain,grade from media_list where m_id=$m_id";
	$res=mysql_query($query);
	$row=mysql_fetch_array($res);
	$grade=$row['grade'];
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
			<form action="media_do.php?op=edit" method="post">
			    <input type="hidden" value="<?php echo $m_id;?>" name="m_id" />
				<p>媒体名称：<input class="in1" type="text" name="media_name" id="media_name" value="<?php echo $row['media_name'];?>"/></p>
				<p>媒体等级：
				     <select name="grade">
					    <option value="0">请选择</option>
						<option value="1"<?php if($grade==1)echo "selected='selected'"?>>A级</option>
						<option value="2"<?php if($grade==2)echo "selected='selected'"?>>B级</option>
						<option value="3"<?php if($grade==3)echo "selected='selected'"?>>C级</option>

					 </select>
				
				</p>
				<p>媒体域名：<input class="in1" type="text" name="domain" id="domain" value="<?php echo $row['domain'];?>"/></p>
				<div class="btn">
					<input type="image" src="images/submit1.gif" width="56" height="20" alt="提交"/>
				</div>
			</form>
		</div>
	</div>
  </div>
 </body>
</html>
