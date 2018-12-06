<?php
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$title="用户信息";
	$user_id=$_GET['user_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> <?php echo $title;?> </title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Jiangting@WiiPu -- http://www.wiipu.com" />
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
			<span>位置：会员管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span></div>
		<div class="content">
			<table style="width:30%;">
				<tr class="t1">
					<td>关键词</td>
				</tr>
				<?php
					$sql="select k_id from user_keywords where user_id=$user_id order by uk_id asc";
					$result=mysql_query($sql);
					while($rows=mysql_fetch_array($result)){
				?>
				<tr>
					<td>
					     <?php 
						   $k_id=$rows['k_id'];
						   $query="select keyword from keyword where k_id=$k_id";
						   $res=mysql_query($query);
						   $row=mysql_fetch_array($res);
						   echo $row['keyword']; 
						 ?>	 
				   </td>
				</tr>
				<?php
				  }
				?>					
			</table>
		  </div>
		</div>
	</div>
   </div>
 </body>
</html>
