<?php
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$type=$_GET['type'];
	if($type==1){
	   $title="正面词表";
	   $add="添加正面词";
	}elseif($type==2){
	   $title="负面词表";
	   $add="添加负面词";
	}
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
				<li class="l1"><a href="add_word.php?type=<?php echo $type;?>" target="mainFrame" ><?php echo $add;?></a> </li>
			</ul>		
		</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：调性词管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span></div>
		<div class="content">
			<table width="70%">
				<tr class="t1">
					<td>调性词</td>
					<td>编辑</td>
					<td>删除</td>
				</tr>
				<?php
					$sql="select w_id,word from property where w_type=$type order by w_id asc";
					$result=mysql_query($sql);
					while($rows=mysql_fetch_array($result)){
				?>
				<tr>
					<td><?php echo $rows['word']?></td>
					<td><a href="edit_word.php?type=<?php echo $type;?>&w_id=<?php echo $rows['w_id'];?>"><img width="9" height="9" alt="编辑" src="images/dot_edit.gif"></a></td>
					<td><a href="javascript:if(confirm('您确定要删除吗？')){location.href='words_do.php?op=del&w_id=<?php echo $rows['w_id'];?>&type=<?php echo $type;?>'}"><img src="images/dot_del.gif" width="9" height="9" alt="删除" /></a></td>
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
