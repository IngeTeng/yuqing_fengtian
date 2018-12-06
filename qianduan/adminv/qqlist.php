<?php
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$title="cookie列表";
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
				<li class="l1"><a href="add_qq.php" target="mainFrame" >添加QQ信息</a> </li>
			</ul>		
		</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：媒体管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span></div>
		<div class="content">
			<table width="70%">
				<tr class="t1">
					<td>QQ号码</td>
					<td>cookie</td>
					<td>cookie状态</td>
					<td>编辑</td>
					<td>删除</td>
				</tr>
				<?php
					$sql="select * from qq_cookies order by status desc";
					$result=mysql_query($sql);
					while($rows=mysql_fetch_array($result)){
				?>
				<tr>
					<td><?php echo $rows['qq_number']?></td>
					<td><?php echo substr($rows['qq_cookie'],0,50)."...";?></td>
					<td><?php echo $rows['status']==0?"失效":"有效" ?></td>
					<td><a href="edit_cookie.php?qc_id=<?php echo $rows['qc_id'];?>"><img width="9" height="9" alt="编辑" src="images/dot_edit.gif"></a></td>
					<td><a href="javascript:if(confirm('您确定要删除吗？')){location.href='cookie_do.php?op=del&qc_id=<?php echo $rows['qc_id'];?>'}"><img src="images/dot_del.gif" width="9" height="9" alt="删除" /></a></td>
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
