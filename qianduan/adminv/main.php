<?php
/**
 * 管理后台登录后右侧首页
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   2016-10-15 CzRzChao
 * @author        jiangting 
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 */
	require_once('admincheck.php');
        require_once('inc_dbconn.php');
        
        
        $qq_sql = "SELECT count(*) FROM qq_cookies WHERE status = 1";
        $result=mysql_query($qq_sql);
        $qq_num = mysql_fetch_array($result);
        $weixin_sql = "SELECT count(*) FROM weixin_cookies WHERE status = 1";
        $result = mysql_query($weixin_sql);
        $weixin_num = mysql_fetch_array($result);
        $ifeng_num = file_get_contents("http://121.40.53.37/wii_spider/bbs_article/ifeng_result_num.php");
        
?>
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> 管理首页 </title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="author" content="Jiangting@WiiPu -- http://www.wiipu.com" />
  <link rel="stylesheet" href="style2.css" type="text/css"/>
 </head>
 <body id="flow">
	<div class="bgintor">
		<div class="tit1">
			<ul>
				<li><a href="#">管理首页</a> </li>
			</ul>
		</div>
		<div class="bgintor2">
			<div class="bgvline"></div>
			<div class="bgtitle"><span><img src="images/home.gif" width="16" height="15" alt="" /></span>
				<span><strong>位置</strong>：首页</span>
			</div>
			<div class="bgintor3">
				<div class="left">
					<div class="title2"></div>
					<div class="title1">
                                                <span class="s1">需处理的事务</span>
                                        </div>
					<div class="bgintor4">
                                            
                                                <?php echo $qq_num['count(*)']?'':'<p><a href="add_qq.php">所有腾讯微博的cookies都已失效</a></p>';
                                                      echo $weixin_num['count(*)']?'':'<p><a href="add_weixin_c.php">所有微信的cookies都已失效</a></p>'; 
                                                      echo $ifeng_num>0?'<p><a href="http://121.40.53.37/wii_spider/bbs_article/ifeng_result.php" target="_Blank">还有'. $ifeng_num. '个凤凰论坛反爬虫结果需要处理(如果停止跳转,刷新即可)</a></p>':'';
                                                ?>
					</div>
				</div>
			</div>
			<div class="bgintor3">
				<div class="left">
					<div class="title2"></div>
					<div class="title1"><span class="s1">个人信息</span></div>
					<div class="bgintor4">
						<p>登录帐号：<?php echo $_SESSION['wii_admin_account']?></p>
					</div>
				</div>
			</div>
			<div id="main"></div>
		</div>
	</div>
 </body>
</html>
