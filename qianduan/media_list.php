<?php
  include_once('check_user.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>媒体管理</title>
<link href="styles/global.css" rel="stylesheet" type="text/css" />
<link href="styles/style.css" rel="stylesheet" type="text/css" />
<style>
.div li{
text-align: right;
margin-right: 55px;
margin-top:15px;
}
</style>
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
</head>
<body>
<?php
  require_once('header.php');
  
  if ($_GET["op"] == "del")
  {
  	$m_id = intval($_GET["m_id"]);
  	$sql = "delete from media_list where m_id = ". $m_id;
  	mysql_query($sql);
  }
  else if ($_GET["op"] == "add")
  {
  	$media_name = addslashes(trim($_POST["media_name"]));
  	$domain = addslashes(trim($_POST["domain"]));
  	$grade = intval(trim($_POST["grade"]));
	if ($media_name != "" && $domain != "")
	{
		$sql = "insert into media_list(media_name,domain,grade) ".
				" values('$media_name','$domain',$grade)";
		mysql_query($sql);
	}
  }
?>
<div class="Navigation">
	<ul>
    	<li><a href="ordinary_home.php">首页</a></li>
        <li><a href="#">媒体管理</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content" style="padding-bottom:0px;text-align:right;">
<form method="post" action="media_list.php?op=add" style="margin-right:100px;">
媒体名称：<input name="media_name" tpye="text">
域名标识：<input name="domain" tpye="text">
媒体级别：<select name="grade">
			<option value="4">4</option>
			<option value="3">3</option>
			<option value="2">2</option>
			<option value="1">1</option>
		</select>
<input type="submit" value=" 添加新媒体 ">
</form>
</div>
<div class="Content" style="padding-top:0px;">
				  <table border="0" cellspacing="0" cellpadding="0" width="500px">
				     <thead class="Header Center">
				         <tr>
						      <td width="30%">媒体名称</td><td width="40%">域名标识</td><td width="10%">级别</td><td width="20%">操作</td>
				         </tr>
					 </thead>
					 <tbody>
					   <?php 
					   		$sql = "select * from media_list order by m_id desc";
					   		$res = mysql_query($sql);
					   		while ($row = mysql_fetch_array($res))
					   		{
					   ?>
                        <tr>
							 <input type="hidden" class="m_id" value="<?php echo $row["m_id"];?>" />
							 <td><?php echo $row["media_name"];?></td>
							 <td>
							 	<?php echo $row["domain"];?>
							 </td>
							  <td>
							 	<?php echo $row["grade"];?>
							 </td>
							 <td><span class="del" style="cursor:pointer"><a href="media_list.php?op=del&m_id=<?php echo $row["m_id"] ?>"><img src="adminv/images/dot_del.gif" width="9" height="9" alt="删除" /></a></span></td>
					 	 </tr>	
						<?php
						}
						?>
						
					</tbody>
				 </table>
				</div>
<div class="Content_bottom"><img src="images/content_bottom.png" /></div>
<?php include_once('footer.php');?>
</body>
</html>