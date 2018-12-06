<?php
/**
 * 文章分类列表- sortlist.php
 *
 * @version       v0.01
 * @create time   2012-4-28
 * @update time   
 * @author        liuxiao
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 */
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title> 文章分类列表</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="author" content="liuxiao@WiiPu -- http://www.wiipu.com" />
		<link rel="stylesheet" href="style2.css" type="text/css"/>
		<script type="text/javascript">
		</script>
	</head>
	<body>
		<div class="bgintor">
			<div class="tit1">
				<ul>
					<li><a href="sortlist.php">分类列表</a> </li>
					<li class="l1"><a href="sortadd.php" target="mainFrame" >添加分类</a> </li>
				</ul>		
			</div>
			<div class="listintor">
				<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
					<span>位置：文章管理 －&gt; <strong>分类列表</strong></span>
				</div>
				<div class="header2"><span>分类列表</span></div>
				<div class="header3">
					<a href="javascript:document.listForm.action='sort_do.php?act=save';document.listForm.submit();"><img src="images/act_save.gif" width="16" height="16" alt="保存" /><strong>保存排序</strong></a> 
					<a href="sortadd.php"><img class="img2" src="images/act_add.gif" width="14" height="14" alt="新建" /> <strong>添加</strong> </a>
				</div>
				<div class="content">
					<form action="#" method="post" name="listForm">
						<table width="100%">
							<tr class="t1">
								<td>分类名称</td>
								<td>查看分类下文章</td>
								<td width="10%">分类排序</td>
								<td width="5%">修改</td>
								<td width="5%">删除</td>
							</tr>
							<?php
								$sql_sort="select * from info_sort order by sort_order asc";
								$rs=mysql_query($sql_sort);
								$i=0;
								while($rows = mysql_fetch_array($rs)){
									$i++;
							?>
							<tr>
								<td class="td1"><a href="articlebysort.php?sortid=<?php echo $rows['sort_id'];?>">
								<?php 
								if ($rows['parent_id'] == 0)
								{
									echo($rows["sort_name"]);
								}
								else
								{
									echo($rows["sort_name"] ." -- " . $rows["parent_name"]);
								}
							?>
								</a></td>
								<td><a href="articlebysort.php?sortid=<?php echo $rows['sort_id'];?>">查看</a></td>			
								<td><input name="sort_id<?php echo $i;?>" type="hidden" value="<?php echo $rows['sort_id'];?>" /><input name="sort_order<?php echo $i;?>" type="text" size="4" value="<?php echo $rows['sort_order'];?>" /></td>
								<td><a href="sortupdate.php?id=<?php echo $rows['sort_id'];?>"><img width="9" height="9" alt="编辑" src="images/dot_edit.gif"></a></td>
								<td><a href="javascript:if(confirm('您确定要删除吗？')){location.href='sort_do.php?act=del&id=<?php echo $rows['sort_id'];?>'}"><img width="9" height="9" alt="删除" src="images/dot_del.gif"></a></td>
							</tr>
							<?php }?>
							<input type="hidden" name="i" value="<?php echo $i;?>" />
						</table>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
