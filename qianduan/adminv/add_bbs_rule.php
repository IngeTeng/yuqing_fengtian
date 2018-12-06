<?php
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$title="添加提取规则";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> <?php echo $title;?> </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="Jiangting@WiiPu -- http://www.wiipu.com" />
<link rel="stylesheet" href="style2.css" type="text/css"/>
<style>
.long
{
	width:400px;
}
.L200
{
	width:200px;
}
p{
margin-bottom:15px;
}
p span
{
	color:#0066FF;
}
.Botton li{
display:inline;
}
</style>
</head>
<body>
  <div class="bgintor">
		<div class="tit1">
			<ul>
				<li class="l1"><a href="bbs_rule_list.php" target="mainFrame">提取规则列表</a> </li>
				<li><a href="#"  ><?php echo $title;?></a> </li>
			</ul>		
		</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：配置管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span></div>
		<div class="content">
		<form  method="post" action="bbs_rule_do.php?op=add">
          <div class="SiteEdit">
			<p><label>规则    名称：</label><input class="L200" type="text" value="" name="rule_name" /></p>
			<p>示例：<span>网页规则</span></P>
			<p><label>URL 匹配规则：</label><input class="long" type="text" name="url"/></p>
			<p>示例：<span>http://chinese.people.com.cn/n/2013/*.html</span></p>
			<p><label>标题开始标记：</label><input class="L200" type="text" name="title_b"/><label>　　　　　标题结束标记：</label><input class="L200" type="text" name="title_e"/></p>
			<p>示例：<span style="margin-right:160px;">&lt;h1 id="p_title"&gt;</span><span style="margin-left:80px;">&lt;/h1&gt;</span></p>
			<p><label>正文开始标记：</label><input class="L200" type="text" name="content_b"/><label>　　　　　正文结束标记：</label><input class="L200" type="text" name="content_e"/></p>
			<p>示例：<span style="margin-right:160px;">&lt;div id="p_content"&gt;</span><span style="margin-left:60px;">&lt;/div&gt;</span></p>
			<p><label>发布时间开始：</label><input class="L200" type="text" name="pubtime_b"/><label>　　　　　发布时间结束：</label><input class="L200" type="text" name="pubtime_e"/></p>
			<p>示例：<span style="margin-right:160px;">&lt;span id="p_publishtime"&gt;</span><span style="margin-left:30px;">&lt;/span&gt;</span></p>
			<p><label>时间&nbsp;&nbsp;&nbsp;&nbsp;格式：</label><input class="L200" type="text" name="time_format"/></p>
			<p>示例：<span style="margin-right:160px;">yyyy-mm-dd HH:MM:SS</span></p>
			<p><label>作者开始标记：</label><input class="L200" type="text" name="author_b"/><label>　　　　　作者结束标记：</label><input class="L200" type="text" name="author_e"/></p>
			<p>示例：<span style="margin-right:160px;">&lt;div id="author"&gt;</span><span style="margin-left:90px;">&lt;/div&gt;</span></p>
			<p><label>版块开始标记：</label><input class="L200" type="text" name="forum_b"/><label>　　　　　版块结束标记：</label><input class="L200" type="text" name="forum_e"/></p>
			<p>示例：<span style="margin-right:160px;">&lt;div id="forum"&gt;</span><span style="margin-left:95px;">&lt;/div&gt;</span></p>
			<p><label>媒体开始标记：</label><input class="L200" type="text" name="media_b"/><label>　　　　　媒体结束标记：</label><input class="L200" type="text" name="media_e"/></p>
			<p>示例：<span style="margin-right:160px;">&lt;div id="media"&gt;</span><span style="margin-left:95px;">&lt;/div&gt;</span></p>
			<p><label>回复数开始标记：</label><input class="L200" type="text" name="reply_b"/><label>　　　　　回复数结束标记：</label><input class="L200" type="text" name="reply_e"/></p>
			<p>示例：<span style="margin-right:160px;">&lt;div id="reply_num"&gt;</span><span style="margin-left:95px;">&lt;/div&gt;</span></p>
			<p><label>点击数开始标记：</label><input class="L200" type="text" name="click_b"/><label>　　　　　点击数结束标记：</label><input class="L200" type="text" name="click_e"/></p>
			<p>示例：<span style="margin-right:160px;">&lt;div id="click_num"&gt;</span><span style="margin-left:95px;">&lt;/div&gt;</span></p>
			
  	     </div>
  	     <ul class="Botton">
        	<li><input type="submit" value="提交" /></li>
            <li><input type="reset" value="重置" /></li>
         </ul>
        </form>
		  </div>
		</div>
	</div>
   </div>
</body>
</html>
