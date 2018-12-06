<?php

/**
 * @filename weixin_cookies.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-10-15  10:52:21
 * @updatetime 2016-10-15  10:52:21
 * @version 1.0
 * @Description
 * 微信cookies
 * 
 */

        require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$title="cookies列表";
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
                                <li class="l1"><a href="add_weixin_c.php" target="mainFrame" >添加cookie</a> </li>
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
					<td>cookies</td>
                                        <td>状态</td>
                                        <td>修改</td>
					<td>删除</td>
				</tr>
				<?php
                                        $sql = "SELECT * FROM weixin_cookies ORDER BY id desc";
					$result=mysql_query($sql);
					while($rows=mysql_fetch_array($result)){
				?>
				<tr>
					<td><?php echo substr($rows['cookie'],0,50)."..."; ?></td>
                                        <td><?php echo $rows['status']==0?"失效":"有效"; ?></td>
                                        <td><a href="edit_weixin_c.php?id=<?php echo $rows['id']?>"><img width="9" height="9" alt="编辑" src="images/dot_edit.gif"></a></td>
					<td><a href="javascript:if(confirm('您确定要删除吗？')){location.href='weixin_c_do.php?op=del&id=<?php echo $rows['id'];?>'}"><img src="images/dot_del.gif" width="9" height="9" alt="删除" /></a></td>
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
