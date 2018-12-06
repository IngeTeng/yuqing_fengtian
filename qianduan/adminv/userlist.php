<?php
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
    $title="用户列表";	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo $title;?></title>
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
					<li><a href="userlist.php"><?php echo $title;?></a> </li>
					<li class="l1"><a href="add_user.php">添加用户</a> </li>
				</ul>		
			</div>
			<div class="listintor">
				<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
					<span>位置：会员管理 －&gt; <strong><?php echo $title;?></strong></span>
				</div>
				<div class="header2"><span><?php echo $title;?></span></div>
				<div class="header3">
					<a href="javascript:document.listForm.action='user_do.php?act=save';document.listForm.submit();"><img src="images/act_save.gif" width="16" height="16" alt="保存" /><strong>保存关键字数目</strong></a> 
					
				</div>
				<div class="content">
					<form action="#" method="post" name="listForm">
						<table width="100%">
							<tr class="t1">
								<td>用户名</td>
								<td>关键字数目</td>
								<td >状态</td>
								<td >用户类型</td>
								<td >注册时间</td>
								<td >操作</td>
							</tr>
							<?php
								$sql="select user_id,email,status,user_type,key_num,reg_time from user order by user_id asc";
								$rs=mysql_query($sql);
								$i=0;
								while($rows = mysql_fetch_array($rs)){
									$i++;
							?>
							<tr>
								<td class="td1"><a href="user_keyList.php?user_id=<?php echo $rows['user_id'];?>"><?php echo $rows['email'];?></a></td>		
								<td><input name="user_id<?php echo $i;?>" type="hidden" value="<?php echo $rows['user_id'];?>" /><input name="key_num<?php echo $i;?>" type="text" size="4" value="<?php echo $rows['key_num'];?>" /></td>
								<td><?php if($rows['status']==0) echo "正常";else echo "异常";?></td>
								<td><?php if($rows['user_type']==0) echo "普通用户";elseif($rows['user_type']==1) echo "特殊用户";elseif($rows['user_type']==2)echo "演示用户"; ?></td>
								<td><?php echo date('Y-m-d',$rows['reg_time']);?></td>
								<?php
								   if($rows['status']==0){
								?>
								<td><a href="javascript:if(confirm('您确定要停止此用户的服务吗？')){location.href='user_do.php?act=end&id=<?php echo $rows['user_id'];?>'}">停止服务</a>|<a href="update_password.php?user_id=<?php echo $rows['user_id'];?>">修改密码</a></td>
								<?php
								 }elseif($rows['status']==1){
								?>
								<td><a href="javascript:if(confirm('您确定要继续为此用户服务吗？')){location.href='user_do.php?act=start&id=<?php echo $rows['user_id'];?>'}">开启服务</a>|<a href="update_password.php?user_id=<?php echo $rows['user_id'];?>">修改密码</a></td>
								<?php
								 }
								?>
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
