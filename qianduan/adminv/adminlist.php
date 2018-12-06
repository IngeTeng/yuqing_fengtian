<?php
/**
 * 管理员列表
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 */
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> 管理员列表 </title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Jiangting@WiiPu -- http://www.wiipu.com" />
  <link rel="stylesheet" href="style2.css" type="text/css"/>
 </head>
 <body>
  <div class="bgintor">
		<div class="tit1">
			<ul>
				<li><a href="adminlist.php">管理员列表</a> </li>
				<li class="l1"><a href="adminadd.php" target="mainFrame" >添加管理员</a> </li>
			</ul>		
		</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：管理员设置 －&gt; <strong>管理员列表</strong></span>
		</div>
		<div class="header2"><span>管理员列表</span></div>
		<div class="header3">
			<a href="adminAdd.php"><img class="img2" src="images/act_add.gif" width="14" height="14" alt="新建" /> <strong>添加</strong> </a>
		</div>
		<div class="content">
			<table width="100%">
				<tr class="t1">
					<td>管理员</td>
					<td>最近一次登录时间</td>
					<td>登录次数</td>
					<td>最后一次登录IP</td>
					<td>删除</td>
				</tr>
				<?php
					$sql="select * from ".WIIDBPRE."_admin order by admin_id desc";
					$result=mysql_query($sql);
					while($rows=mysql_fetch_array($result))
				{
				?>
				<tr>
					<td><?php echo $rows['admin_account']?></td>
					<td><?php echo $rows['admin_logintime']?></td>
					<td><?php echo $rows['admin_logincount']?></td>
					<td><?php echo $rows['admin_loginip']?></td>
					<?php
						if($rows['admin_account']==$_SESSION['wii_admin_account'])
						{
					?>
						<td></td>
					<?php
						}else{
					?>
					<td><a href="javascript:if(confirm('您确定要删除吗？')){location.href='admin_do.php?act=del&id=<?php echo $rows['admin_id'];?>'}"><img src="images/dot_del.gif" width="9" height="9" alt="删除" /></a></td>
					<?php
					}	
					?>
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
