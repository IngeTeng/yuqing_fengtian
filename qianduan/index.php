<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>汽车行业大数据采集和分析系统</title>
<link href="styles/global.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
</head>

<body onload="MM_preloadImages('images/botton_hover.png')">
<div class="Header">
	<div class="Logo">
	    <img src="http://112.124.3.197:8020/stock/user/images/logo.jpg?v=2" height="129" />
    	<p class="LogoBt">互联网舆情监控系统（汽车行业）</p>
	</div>
</div>
<div class="Middle">
	<div class="login">
		<img src="images/car.png" class="Left" />
		<div class="login_text">
		   <form action="login_do.php" method="post" id="loginForm">
			<p><input type="text" class="Name" value="用户名" name="user_name" id="user_name" /></p>
			<p><input type="password" class="Password" value="123456" name="password" id="password" /></p>
			<p class="Logo_botton"><a href="#"><img src="images/botton_normal.png" name="Image3" width="101" height="48" border="0" id="Image3" /></a></p>
		   </form>
			<p class="Password_Text"><span class="pass" style="cursor:pointer;">忘记密码</span></p>
		</div>
	</div>
</div>
<?php include_once('footer.php');?>
</body>
</html>
<script type="text/JavaScript">
$('#user_name,#password').click(function(){
        $(this).val(''); 
})
$('.Logo_botton').click(function(){
      var name;
	  var password;
	  name=$('#user_name').val();
	  password=$('#password').val();
	  if(name==""||password==""){
	       alert('用户名或密码为空！');
	  }else{
           $('#loginForm').submit();
	  }
})
$('.pass').click(function(){
  alert('请联系管理员更改密码！');
})
</script>
