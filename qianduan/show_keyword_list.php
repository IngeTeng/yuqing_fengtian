<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>关键字管理</title>
<link href="styles/global.css" rel="stylesheet" type="text/css" />
<link href="styles/style.css" rel="stylesheet" type="text/css" />
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
  require_once('show_header.php');
  $user_id=$_COOKIE['user_id'];
?>
<div class="Navigation">
	<ul>
    	<li><a href="show_home.php">首页</a></li>
        <li><a href="#">关键字管理</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content" style="padding-bottom:0px;">

</div>
<div class="Content" style="padding-top:0px;">
				  <table border="0" cellspacing="0" cellpadding="0" width="350">
				     <thead class="Header Center">
				         <tr>
						      <td width="40%">关键字</td><td width="60%">添加时间</td>
				         </tr>
					 </thead>
					 <tbody>
					   <?php
						     $query="select user_id from user where user_type=1 order by user_id asc limit 1";
							 $res=mysql_query($query);
							 $row=mysql_fetch_array($res);
							 $special_id=$row['user_id']; 
						     $query="select k_id,uk_id,add_time from user_keywords where user_id=$special_id";
							 $res=mysql_query($query);
							 while($row=mysql_fetch_array($res)){
							     $k_id=$row['k_id'];
								 $uk_id=$row['uk_id'];
								 $add_time=$row['add_time'];
								 $query1="select keyword from keyword where k_id=$k_id";
								 $res1=mysql_query($query1);
								 $row1=mysql_fetch_array($res1);
								 $keyword=$row1['keyword'];
					   ?>
                        <tr>
							 <td><?php echo $keyword;?></td>
						     <td><span class="time"><?php echo date('Y-m-d H:i',$add_time);?></span></td>
					 	 </tr>	
						<?php
						}
						?>
						
					</tbody>
				 </table>
				</div>
<div class="Content_bottom"><img src="images/content_bottom.png" /></div>
<?php include_once('footer.php');?>
</body>
</html>
<?php
}
?>
<script>
$('.del').click(function(){
   var tr=$(this).parent().parent();
   var add_time=tr.find('.addTime').val();
   var uk_id=tr.find('.uk_id').val();
   var k_id=tr.find('.k_id').val();
   var now=new Date();
   var time=now.getTime()/1000;
   if(time-add_time>2160000){
       $.ajax({
	        url:"delete_keyword_ajax.php",
			type:"post",
			data:{uk_id:uk_id,k_id:k_id},
			dataType:"json",
			error:function(){},
			success:function(){
			     tr.remove();
			}	   
	   })
   }else{
         alert('该关键字添加时间未满25天，不能删除！');
   } 
})
</script>