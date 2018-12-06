<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>修改密码</title>
<link href="styles/global.css" rel="stylesheet" type="text/css" />
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
  $user_id=$_COOKIE['user_id'];
  $query="select user_type from user where user_id=$user_id";
  $res=mysql_query($query);
  $row=mysql_fetch_array($res);
  $user_type=$row['user_type'];
  if($user_type==2){
     require_once('show_header.php');
  }else{
     require_once('header.php');
  }
?>

<div class="Navigation">
	<ul>
    	<li><a href="ordinary_home.php">首页</a></li>
        <li><a href="#">修改密码</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
  <form action="password_do.php" method="post">
    <div class="div">
	  <ul>
	    <li>
	      <label for="old_pass" class="label">原始密码：</label>
		  <input type="password" id="old_pass" name="old_pass" />
		</li>
		<li>
		  <label for="new_pass1" class="label">新密码：</label>
		  <input type="password" id="new_pass1" name="new_pass1" />
		</li>
		<li>
		  <label for="new_pass2" class="label">确认新密码：</label>
		  <input type="password" id="new_pass2" name="new_pass2" />
		</li>
		<li>
		  <input type="submit" value="提交" id="submit" />
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
    var old_pass=$('#oid_pass').val();
    var new_pass1=$('#new_pass1').val();
    var new_pass2=$('#new_pass2').val();
    if(old_pass==""){
	       alert('原始密码不能为空');
		   return false;  
    }
	if(new_pass1==""){
	       alert('新密码不能为空');
		   return false;  
    }
	if(new_pass2==""){
	       alert('确认密码不能为空');
		   return false;  
    }
	if(new_pass1!=new_pass2){
	       alert('两次密码输入不一致！');
		   return false;  
    }
	return true;
})
</script>