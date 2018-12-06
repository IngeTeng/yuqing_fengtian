<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>关键字管理</title>
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
if(!isset($_COOKIE['user_id'])){
   echo "<script>alert('您尚未登陆！');location.href='index.php';</script>";
}else{
  require_once('adminv/inc_dbconn.php');
  require_once('header.php');
  $user_id=$_COOKIE['user_id'];
?>
<div class="Navigation">
	<ul>
    	<li><a href="ordinary_home.php">首页</a></li>
        <li><a href="category_list.php">分类管理</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
  <form action="category_do.php" method="post" name="categoryForm">
    <div class="div">
	  <ul>
	    <li>
	      <label for="keyword" class="label">分类：</label>
		  <input type="text" id="cname" name="cname" style="width:150px;" />
		</li>
		<li style="text-align: center">
		  <input type="submit" value="提交" id="submit" style="margin:0 auto;"/>
		</li>
	  </ul>
	</div>
  </form>
</div>
<div class="Content_bottom"><img src="images/content_bottom.png" /></div>
<?php include_once('footer.php');?>
</body>
</html>
<?php
}
?>
<script>
$('#submit').click(function(){
    var cname=$('#cname').val();
    var re = /[~#^$@%&!*;,.?<>!￥，。？]/ig;
    if(re.test(cname)){
           alert('分类名称中不能含有特殊字符');
		   return false;
    } 
    var kong = /[ ]/ig;
	if(kong.test(cname)){
           alert('分类名称中不能含有空格');
		   return false;
    } 
    if(cname==""){
	       alert('分类名称不能为空');
		   return false;  
    }
	return true;
})
</script>