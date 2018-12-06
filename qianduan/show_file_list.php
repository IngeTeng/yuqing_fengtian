<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>文件管理</title>
<link href="styles/global.css" rel="stylesheet" type="text/css" />
<style>
td{
text-align:center;
}
</style>
</head>
<body>
<?php
if(!isset($_COOKIE['user_id'])){
   echo "<script>alert('您尚未登陆！');location.href='index.php';</script>";
}else{
  require_once('adminv/inc_dbconn.php');
  $user_id=$_COOKIE['user_id'];
  include_once('show_header.php');
?>
<div class="Navigation">
	<ul>
    	<li><a href="show_home.php">首页</a></li>
        <li><a href="#">文件管理</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
  <tr class="table_Header">
    <td width="33%">文件名称</td>
    <td width="15%">关键字</td>
    <td width="14%">起始时间</td>
    <td width="14%">截止时间</td>
    <td width="14%">导出时间</td>
    <td width="10%">操作</td>
  </tr>
                       <?php
					         $query="select user_id from user where user_type=1 order by user_id asc limit 1";
							 $res=mysql_query($query);
							 $row=mysql_fetch_array($res);
							 $special_id=$row['user_id'];    
							 $query="select * from file_list where user_id=$special_id order by export_time desc";
							 $res=mysql_query($query);
							 $num=mysql_num_rows($res);
							 while($row=mysql_fetch_array($res)){
							     $file_id=$row['file_id'];
							     $file_name=$row['file_name'];
								 $export_time=$row['export_time'];
								 $start_time=$row['start_time'];
								 $end_time=$row['end_time'];
								 $uk_id=$row['uk_id'];
								 $query2="select k_id from user_keywords where uk_id in(".$uk_id.")";
								 $res2=mysql_query($query2);
								 $keyword="";
								 $i=1;
								 $num=mysql_num_rows($res2);
								 while($row2=mysql_fetch_array($res2)){
								      $k_id=$row2['k_id'];
								      $query3="select keyword from keyword where k_id=$k_id";
								      $res3=mysql_query($query3);
								      $row3=mysql_fetch_array($res3);
									  if($i<$num){
								          $keyword.=$row3['keyword'].",";
									  }else{
									      $keyword.=$row3['keyword'];
									  } 	  
									  $i++;
								 }	
                         ?>	
                         <tr>
					         <td style="text-align:left;"><?php echo $file_name;?></td>
							 <td><?php echo $keyword;?></td>
						     <td><span class="time"><?php echo date('Y-m-d H:i',$start_time);?></span></td>
							 <td><span class="time"><?php echo date('Y-m-d H:i',$end_time);?></span></td>
							 <td><span class="time"><?php echo date('Y-m-d H:i',$export_time);?></span></td>
							 <td><a target="_blank" href="<?php echo $special_id."/".$file_id.".xls";?>" class="word-btn btn-success">下载</a></td>
					 	 </tr>	
						 <?php
						 }
						 ?>	
</table>
</div>
<div class="Content_bottom"><img src="images/content_bottom.png" /></div>
<?php include_once('footer.php');?>
<?php
}
?>
</body>
</html>
