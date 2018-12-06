<?php
  include_once('check_user.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>舆情监控系统</title>
<link href="styles/style.css" rel="stylesheet" type="text/css" />
<link href="styles/global.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="styles/jquery-ui.css?v=2" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery-ui-slide.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
</head>
<body>
<?php
     include_once('header.php');
?>
<div class="Navigation">
	<ul>
    	<li><a href="#">首页</a></li>
        <li><a href="#">微信数据补充</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
      <form action="http://47.92.209.195/yuqing/wii_spider/weixin_list/filldata.php" method="post" name="submitData">
              <div class="output">
				    <table width="100%" style="border:none;">
				    <tr style="border:none;">
					<td valign="top" width="8%" style="border:none;text-align: left;">
				    <label>关&nbsp;键&nbsp;字：</label>
					</th>
					<td style="border:none;text-align: left;">
					
					<?php
							//获取关键词分类
					     require_once('adminv/inc_dbconn.php');
						 $sql = "select * from user_category where user_id = $user_id order by c_id asc";
						 $res = mysql_query($sql);
						 while ($row = mysql_fetch_array($res))
						 {
					?>
					<p>
						<span style="color:#0000ca;font-weight:bold;"><?php echo $row["category_name"] ?>：</span>
					<?php
					 	$c_id = $row["c_id"];
					 	$query="select k_id,uk_id from user_keywords where user_id=$user_id and c_id = $c_id order by uk_id asc";
					 	$res2=mysql_query($query);
					 	while($row2=mysql_fetch_array($res2)){
						     $k_id=$row2['k_id'];
							 $uk_id=$row2['uk_id'];
							 $query1="select keyword from keyword where k_id=$k_id";
							 $res1=mysql_query($query1);
							 $row1=mysql_fetch_array($res1);
							 $keyword=$row1['keyword'];
					?>
						<input type="radio" value="<?php echo $keyword;?>" class="keyword1" name="kids[]" /><?php echo $keyword;?>
					<?php
						   }
					
					?>
					</p>
								
					<?php
							 }
					?>
						<p>
									<span style="color:#0000ca;font-weight:bold;">未分类：</span>
					<?php
						     $query="select k_id,uk_id from user_keywords where user_id=$user_id and c_id = 0 order by uk_id asc";
							 $res=mysql_query($query);
							 while($row=mysql_fetch_array($res)){
							     $k_id=$row['k_id'];
								 $uk_id=$row['uk_id'];
								 $query1="select keyword from keyword where k_id=$k_id";
								 $res1=mysql_query($query1);
								 $row1=mysql_fetch_array($res1);
								 $keyword=$row1['keyword'];
					?>
					<input type="radio" value="<?php echo $keyword;?>" class="keyword1" name="kids[]" /><?php echo $keyword;?>
					<?php
						   }
					?>
						</p>
					</td>
					</tr>
				</table>
				</div>
				<div align="center">
					<p>请将搜狗微信的搜索结果的网页源代码复制到以下文本框中：</p>
					<textarea name="code" id="code" rows="30" cols="100"></textarea>
					<br/><br/>
					<input type="button" id="sub" value="提交" />
				</div>
				</div>
				
		</form>
</div>
<div class="Content_bottom"><img src="images/content_bottom.png" /></div>
<?php include_once('footer.php');?>
</body>
</html>
<script>

$('#sub').click(function(){
   var kids="0";
   $("input[name='kids[]']").each(function(){        
		 if($(this).attr("checked")){
		    kids=$(this).val();
		 }
		 	 
   })
   //var keyw = $('#keyword').val();
   var code=$('#code').val();

   if(kids=="0"){
         alert('请选择关键字！');
		 return false;
   }

   submitData.submit();
})
</script>