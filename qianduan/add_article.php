<?php
  include_once('check_user.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="Author" content="微普科技http://www.wiipu.com"/>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="styles/global.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="styles/style.css" type="text/css"/>
  <link rel="stylesheet" type="text/css" href="styles/jquery-ui.css" />
  <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui.js"></script>
  <script type="text/javascript" src="js/jquery-ui-slide.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
  <title>舆情监控系统</title>
</head>
<body>
<?php
require_once('header.php');
?>
<div class="Navigation">
	<ul>
    	<li><a href="#">首页</a></li>
        <li><a href="#">添加文章</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
 <form action="add_article_do.php" method="post">
  <input type="hidden" name="user_id" value="<?php echo $user_id;?>" />
  <label>标题：</label>
  <input type="text" name="title" size="30"/><br />
  <label>内容：</label><br />
  <textarea name="content" cols="70" rows="10"></textarea><br />
  <input type="submit" value="提交" style="width: 120px;height: 40px;margin-top: 10px;"/>
 </form> 
</div>	
<div class="Content_bottom"><img src="images/content_bottom.png" /></div>
<div class="clear"></div>
<?php include_once('footer.php');?>	
<div class="clear"></div>
</body>
</html>
