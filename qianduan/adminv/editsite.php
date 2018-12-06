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
	$title="编辑采集站点";
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
				<li class="l1"><a href="sitelist.php">采集站点列表</a> </li>
				<li ><a href="#" target="mainFrame" ><?php echo $title;?></a> </li>
			</ul>		
		</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：配置管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span></div>
		<div class="content">
<?php
$site_id=$_GET['site_id'];
$sql="select * from spider_site where site_id=$site_id";
$res=mysql_query($sql);
$row=mysql_fetch_array($res);
$site_name=$row['site_name'];
$site_url=$row['site_url'];
$crawl=$row['site_crawldepth'];
$update=$row['site_updatedepth'];
$stress=$row['stress'];
$intv=$row['site_interval'];
$hr1=$row['site_urlhold1'];
$hr2=$row['site_urlhold2'];
$hr3=$row['site_urlhold3'];
$hr4=$row['site_urlhold4'];
$tr1=$row['site_urlthrow1'];
$tr2=$row['site_urlthrow2'];
$tr3=$row['site_urlthrow3'];
$tr4=$row['site_urlthrow4'];
$site_type=$row['site_type'];
?>
		<form name="book_form" id="book_form" method="post" action="site_do.php?op=edit&site_id=<?php echo $site_id;?>">
		<p><label>站点名称：</label><input type="text" name="name" value="<?php echo $site_name; ?>"/><span>*</span></p>
		<p>
		     <label>站点类型：</label>
			 <select name="site_type">
			      <option value="0">请选择</option>
			      <option value="1"<?php if($site_type==1) echo "selected='selected'";?>>新闻</option>
				  <option value="2"<?php if($site_type==2) echo "selected='selected'";?>>论坛</option>
				  <option value="3"<?php if($site_type==3) echo "selected='selected'";?>>博客</option>
				  <option value="4"<?php if($site_type==4) echo "selected='selected'";?>>微博</option>
				  <option value="5"<?php if($site_type==5) echo "selected='selected'";?>>视频</option>
			 </select>
		   
		     <span>*</span></p>
		<p><label>起始地址：</label><input class="long" type="text" name="url" value="<?php echo $site_url; ?>"/><span> *需带http://</span></p>
		<p><label>采集深度：</label><input type="text" name="crawl" value="<?php echo $crawl; ?>"/><span> *</span></p>
		<p><label>更新深度：</label><input type="text" name="update" value="<?php echo $update; ?>"/><span> *</span></p>
		<p><label>压力：</label><input type="text" name="stress" value="<?php echo $stress; ?>"/><span> *</span></p>
		<p><label>扫描间隔：</label><input type="text" name="intv" value="<?php echo $intv; ?>"/><span> *单位：秒</span></p>
		<p><label>保留URL规则1：</label><input class="long" type="text" name="hr1" value="<?php echo $hr1; ?>"/></p>
		<p><label>保留URL规则2：</label><input class="long" type="text" name="hr2" value="<?php echo $hr2; ?>"/></p>
		<p><label>保留URL规则3：</label><input class="long" type="text" name="hr3" value="<?php echo $hr3; ?>"/></p>
		<p><label>保留URL规则4：</label><input class="long" type="text" name="hr4" value="<?php echo $hr4; ?>"/></p>
		<p><label>丢弃URL规则1：</label><input class="long" type="text" name="tr1" value="<?php echo $tr1; ?>"/></p>
		<p><label>丢弃URL规则2：</label><input class="long" type="text" name="tr2" value="<?php echo $tr2; ?>"/></p>
		<p><label>丢弃URL规则3：</label><input class="long" type="text" name="tr3" value="<?php echo $tr3; ?>"/></p>
		<p><label>丢弃URL规则4：</label><input class="long" type="text" name="tr4" value="<?php echo $tr4; ?>"/></p>
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
