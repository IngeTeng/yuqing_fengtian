<?php
    /**
 * @filename filter_keyword.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-10-3  10:53:01
 * @updatetime 2016-10-3  10:53:01
 * @version 1.0
 * @Description
 * 关键词过滤 
 * 
 */

        require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$title="过滤词列表";
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
				<li class="l1"><a href="add_filter.php" target="mainFrame" >添加过滤词信息</a> </li>
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
					<td>对应关键词</td>
					<td>过滤词</td>
					<td>编辑</td>
					<td>删除</td>
				</tr>
				<?php
                                        $sql = "SELECT k.k_id, k.keyword, f.id, f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) ORDER BY k_id";
					$result=mysql_query($sql);
					while($rows=mysql_fetch_array($result)){
				?>
				<tr>
					<td><?php echo $rows['keyword']?:"所有关键词"?></td>
					<td><?php echo $rows['filter_word'];?></td>
                                        <td><a href="edit_filter.php?id=<?php echo $rows['id']?>"><img width="9" height="9" alt="编辑" src="images/dot_edit.gif"></a></td>
					<td><a href="javascript:if(confirm('您确定要删除吗？')){location.href='filter_do.php?op=del&id=<?php echo $rows['id'];?>'}"><img src="images/dot_del.gif" width="9" height="9" alt="删除" /></a></td>
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
