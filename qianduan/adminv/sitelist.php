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
	$title="采集站点列表";
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
				<li class="l1"><a href="addsite.php" target="mainFrame" >添加采集站点</a> </li>
			</ul>		
		</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：配置管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span></div>
		<div class="content">
			<table width="100%">
				<tr class="t1">
					<td>站点名称</td>
					<td>站点url</td>
					<td>站点类型</td>
					<td>添加时间</td>
					<td>编辑</td>
					<td>删除</td>
				</tr>
				<?php
					$sql="select site_id,site_name,site_url,site_type,site_addtime from spider_site order by site_id asc";
					$result=mysql_query($sql);
					while($rows=mysql_fetch_array($result)){
				?>
				<tr>
					<td><?php echo $rows['site_name']?></td>
					<td><a href="<?php echo $rows['site_url']?>" target="_blank"><?php echo $rows['site_url']?></a></td>
					<td><?php if($rows['site_type']==1) echo "新闻";elseif($rows['site_type']==2) echo "论坛";elseif($rows['site_type']==3) echo "博客";elseif($rows['site_type']==4) echo "微博";elseif($rows['site_type']==5) echo "视频";else echo "";?></td>
					<td><?php echo date('Y-m-d',$rows['site_addtime'])?></td>
					<td><a href="editsite.php?site_id=<?php echo $rows['site_id'];?>"><img width="9" height="9" alt="编辑" src="images/dot_edit.gif"></a></td>
					<td><a href="javascript:if(confirm('您确定要删除吗？')){location.href='site_do.php?op=del&site_id=<?php echo $rows['site_id'];?>'}"><img src="images/dot_del.gif" width="9" height="9" alt="删除" /></a></td>
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
