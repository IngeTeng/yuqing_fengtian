<?php

/**
 * @filename edit_filter.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-10-3  13:20:50
 * @updatetime 2016-10-3  13:20:50
 * @version 1.0
 * @Description
 * 修改过滤词信息
 * 
 */
require_once('admincheck.php');
require_once('inc_dbconn.php');
$title="修改";
$f_id = $_GET['id'];
$query="SELECT * FROM keyword_filter WHERE id=$f_id";
$res=mysql_query($query);
$row=mysql_fetch_array($res);

$keyword_sql = "SELECT * FROM keyword ORDER BY k_id";
$result = mysql_query($keyword_sql);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title> <?php echo $title;?> </title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="author" content="liuxiao@WiiPu -- http://www.wiipu.com" />
		<link rel="stylesheet" href="style2.css" type="text/css"/>
	</head>
	<body>
		<div class="bgintor">
		<div class="tit1">
			<ul>
                                <li class="l1"><a href="filter_keyword.php"><?php echo '过滤词列表';?></a> </li>
				<li><a href="#"><?php echo $title;?></a> </li>
			</ul>		
		</div>
	<div class="listintor">
		<div class="header1"><img src="images/square.gif" width="6" height="6" alt="" />
			<span>位置：媒体管理 －&gt; <strong><?php echo $title;?></strong></span>
		</div>
		<div class="header2"><span><?php echo $title;?></span>
		</div>
		<div class="fromcontent">
                        <form action="filter_do.php?op=edit" method="post">
			    <input type="hidden" value="<?php echo $f_id;?>" name="f_id" />
				<p>对应关键词：                                    
                                    <select name="keyword">
                                        <option value="0">所有关键词</option>
                                        <?php while($rows=mysql_fetch_array($result)) {
                                            if($$row['k_id'] == $rows['k_id']) { 
                                                echo '<option value="'. $rows['k_id'].'" selected="selected">'. $rows['keyword']. '</option>';
                                            }
                                            else {
                                                echo '<option value="'. $rows['k_id']. '">'. $rows['keyword']. '</option>';
                                            } 
                                        } ?>
                                    </select>
                                </p>
				<p>过滤词:</p>
                                
				<textarea name="filter_word"><?php echo $row['filter_word'];?></textarea>
				<div class="btn">
					<input type="image" src="images/submit1.gif" width="56" height="20" alt="提交"/>
				</div>
			</form>
		</div>
	</div>
  </div>
 </body>
</html>
