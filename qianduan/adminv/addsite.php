<?php
/**
 * 管理员列表
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 */
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$title="添加采集站点";
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
	width:500px;
}
p{
margin-bottom:15px;
}
p span
{
	color:#ff0000;
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
				<li class="l1"><a href="sitelist.php" target="mainFrame">采集站点列表</a> </li>
				<li><a href="#"  ><?php echo $title;?></a> </li>
			</ul>		
		</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：配置管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span></div>
		<div class="content">
		<form name="book_form" id="book_form" method="post" action="site_do.php?op=add">
		<p><label>站点名称：</label><input type="text" name="name"/><span>*</span></p>
		<p>
		     <label>站点类型：</label>
			 <select name="site_type">
			      <option value="0">请选择</option>
			      <option value="1" selected="selected">新闻</option>
				  <option value="2">论坛</option>
				  <option value="3">博客</option>
				  <option value="4">微博</option>
				  <option value="5">视频</option>
			 </select>
		   
		     <span>*</span></p>
		<p><label>起始地址：</label><input class="long" type="text" name="url" value="http://"/><span> *需带http://</span></p>
		<p><label>采集深度：</label><input type="text" name="crawl" value="2"/><span> *</span></p>
		<p><label>更新深度：</label><input type="text" name="update" value="2"/><span> *</span></p>
		<p><label>压力：</label><input type="text" name="stress" value="2"/><span> *</span></p>
		<p><label>保留URL规则1：</label><input class="long" type="text" name="hr1"/></p>
		<p><label>保留URL规则2：</label><input class="long" type="text" name="hr2"/></p>
		<p><label>保留URL规则3：</label><input class="long" type="text" name="hr3"/></p>
		<p><label>保留URL规则4：</label><input class="long" type="text" name="hr4"/></p>
		<p><label>丢弃URL规则1：</label><input class="long" type="text" name="tr1"/></p>
		<p><label>丢弃URL规则2：</label><input class="long" type="text" name="tr2"/></p>
		<p><label>丢弃URL规则3：</label><input class="long" type="text" name="tr3"/></p>
		<p><label>丢弃URL规则4：</label><input class="long" type="text" name="tr4"/></p>
		<p>&nbsp</p>
  	    </div>
  	    <ul class="Botton">
        	<li><input type="submit"  value="提交"/></li>
            <li><input type="reset" value="重置" /></li>
        </ul>
        </form>
		  </div>
		</div>
	</div>
   </div>
 </body>
</html>
