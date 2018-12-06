<?php
/**
 * 分类文章列表- articlebysort.php
 *
 * @version       v0.01
 * @create time   2012-5-5
 * @update time   
 * @author        liuxiao
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 */
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$sortid = $_GET['sortid'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title> 分类文章列表</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="author" content="liuxiao@WiiPu -- http://www.wiipu.com" />
		<link rel="stylesheet" href="style2.css" type="text/css"/>
		<script language="javascript">
			function checkAll(f){
				var len = f.elements.length;
				if(document.getElementById("handler").checked==true){
					for(i=0;i<len;i++){
						var e=f.elements[i];
						if(e.type=='checkbox'){
							e.checked=true;
						}
					}
				}
				if(document.getElementById("handler").checked==false){
					for(i=0;i<len;i++){
						var e=f.elements[i];
						if(e.type=='checkbox'){
							e.checked=false;
						}
					}
				}
			}
		</script>
	</head>
	<body>
		<div class="bgintor">
				<div class="tit1">
					<ul>
						<li class="l1"><a href="articlelist.php">文章列表</a> </li>
						<li class="l1"><a href="articleadd.php" target="mainFrame" >添加文章</a> </li>
						<li><a href="articlebysort.php?sortid=<?php echo $sortid;?>">分类文章列表</a> </li>
					</ul>		
				</div>
			<div class="listintor">
				<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
					<span>位置：文章管理 －&gt; <strong>文章列表</strong></span>
				</div>
				<div class="header2"><span>文章列表</span></div>
				<div class="header3">
					<a href="javascript:if(confirm('您确定要删除吗？')){document.listForm.action = 'article_do.php?act=voldel';document.listForm.submit();}"><img src="images/act_del.gif" width="14" height="14"><strong>删除</strong></a>
					<a href="articleadd.php"><img class="img2" src="images/act_add.gif" width="14" height="14" alt="新建" /> <strong>添加</strong> </a>
				</div>
				<div class="content">
					<form action="#" method ="post" name="listForm">
						<table width="100%">
							<tr class="t1">
								<td width="5%"><input type="checkbox" id="handler" onclick="checkAll(this.form);" />全选</td>
								<td>文章标题</td>
								<td>文章分类</td>
								<td>排序权值</td>
								<td>添加时间</td>
								<td width="5%">编辑</td>
								<td width="5%">删除</td>
							</tr>
							<?php
								//分页
								$pagesize = 10;
								$sql_page = "select article_id from info_article where article_sort = ".$sortid;
								$rs_page = mysql_query($sql_page);
								$count = mysql_num_rows($rs_page);
								if($count%$pagesize){
									$pagecount = intval($count/$pagesize)+1;
								}else{
									$pagecount = intval($count/$pagesize);
								}
								if(isset($_GET['page'])){
									$page=intval($_GET['page']);
								}else{
									$page=1;
								}
								$pagestart = ($page-1)*$pagesize;

								$sql = "select info_article.*,info_sort.parent_id, info_sort.parent_name,info_sort.sort_name from info_article join info_sort on info_article.article_sort = info_sort.sort_id where article_sort = ".$sortid." or info_sort.parent_id = ".$sortid." order by sort_weight desc,article_createtime desc limit ".$pagestart.",".$pagesize;
								$rs = mysql_query($sql);
								while($rows = mysql_fetch_array($rs)){
							?>
							<tr>
								<td><input type="checkbox" class="chk" name="list_id[]" value="<?php echo $rows['article_id'];?>" /></td>
								<td class="td1"><a href="articleupdate.php?id=<?php echo $rows['article_id'];?>"><?php echo $rows['article_title'];?></a></td>
								<td><a href="articlebysort.php?sortid=<?php echo $rows['article_sort'];?>">
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
								<td><?php echo $rows['sort_weight'];?></td>
								<td><?php echo $rows['article_createtime'];?></td>
								<td><a href="articleupdate.php?id=<?php echo $rows['article_id'];?>"><img width="9" height="9" alt="编辑" src="images/dot_edit.gif"></a></td>
								<td><a href="javascript:if(confirm('您确定要删除吗？')){location.href='article_do.php?act=del&id=<?php echo $rows['article_id'];?>'}"><img width="9" height="9" alt="删除" src="images/dot_del.gif"></a></td>
							</tr>
							<?php }?>
						</table>
					</form>
					<?php	
						if($count==0){
							echo "<center><b>没有相关信息！</b></center>";
						}else{
					?>
					<div class="page">
						<div class="pagebefore">当前页:<?php echo $page;?>/<?php echo $pagecount;?>页 每页 <?php echo $pagesize?> 条</div>
						<div class="pageafter">
						<?php echo showPage('articlebysort.php?sortid='.$sortid,$page,$pagecount);?>
						<div class="clear"></div>
						</div>
					</div>
					<?php }?>
				</div>
			</div>
		</div>
	</body>
</html>
