<?php
/**
 * 管理后台左侧菜单
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 */
	require_once('admincheck.php');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> 菜单 </title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Jiangting@WiiPu -- http://www.wiipu.com" />
  <link rel="stylesheet" href="style2.css" type="text/css"/>
 </head>	
 <body id="flow">
	<div class="menu" id="me">
		<div class="menu_content">
			<div class="menu_h">采集站点配置</div>
			<div class="menu_intor">
				<p><a href="sitelist.php" target="mainFrame">采集站点列表</a></p>
				<p><a href="throw_list.php" target="mainFrame">丢弃列表</a></p>
			</div>
		</div>
		<div class="menu_content">
			<div class="menu_h">提取规则配置</div>
			<div class="menu_intor">
				<p><a href="news_rule_list.php" target="mainFrame">新闻类</a></p>
			</div>
			<div class="menu_intor">
				<p><a href="bbs_rule_list.php" target="mainFrame">论坛类</a></p>
			</div>
			<div class="menu_intor">
				<p><a href="blog_rule_list.php" target="mainFrame">博客类</a></p>
			</div>
			<div class="menu_intor">
				<p><a href="video_rule_list.php" target="mainFrame">视频类</a></p>
			</div>
		</div>
		<div class="menu_content">
			<div class="menu_h">调性词管理</div>
			<div class="menu_intor">
				<p><a href="words_list.php?type=1" target="mainFrame">正面词表</a></p>
			</div>
			<div class="menu_intor">
				<p><a href="words_list.php?type=2" target="mainFrame">负面词表</a></p>
			</div>
		</div>
		<div class="menu_content">
			<div class="menu_h">媒体管理</div>
			<div class="menu_intor">
				<p><a href="media_list.php" target="mainFrame">媒体列表</a></p>
			</div>
			<div class="menu_intor">
				<p><a href="channel_list.php" target="mainFrame">频道列表</a></p>
			</div>
		</div>
		<div class="menu_content">
			<div class="menu_h menu_h3">系统管理</div>
			<div class="menu_intor">
				<p><a href="adminlist.php" target="mainFrame">管理员设置</a></p>
			</div>
			<div class="menu_intor" >
				<p><a href="userlist.php" target="mainFrame">会员管理</a></p>
			</div>
			<div class="menu_intor" >
				<p><a href="qqlist.php" target="mainFrame">QQ_cookie管理</a></p>
			</div>
                        <div class="menu_intor" >
                                <p><a href="weixin_cookies.php" target="mainFrame">微信_cookie管理</a></p>
			</div>
			<div class="menu_intor" >
				<p><a href="keyword_list.php" target="mainFrame">关键词列表</a></p>
			</div>
                        <div class="menu_intor">
                            <p><a href="filter_keyword.php" target="mainFrame">过滤词列表</a>
                        </div>
		</div>
	</div>
 </body>
</html>