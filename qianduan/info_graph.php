<?php
  if(!isset($_COOKIE['user_id'])){
      echo "<script>alert('您尚未登陆！');location.href='index.php';</script>";
      return;
  }
  include_once('adminv/inc_dbconn.php');
  $user_id=$_COOKIE['user_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>统计图</title>
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
  $query="select user_type from user where user_id=$user_id";
  $res=mysql_query($query);
  $row=mysql_fetch_array($res);
  $user_type=$row['user_type'];
  if($user_type==2){
       $query="select user_id from user where user_type=1 order by user_id asc limit 1";
       $res=mysql_query($query);
       $row=mysql_fetch_array($res);
       $user_id=$row['user_id'];
	   include_once('show_header.php');
  }else{
       include_once('header.php');
  }
?>
<div class="Navigation">
	<ul>
    	<li><a href="#">首页</a></li>
        <li><a href="#">统计图</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
      <div class="output stats_navi">
	       <ul>
		       <li><a href="info_stats.php">总体统计</a></li>
			   <li><a href="media_stats.php">媒体统计</a></li>
			   <li><a href="weibo_stats.php">微博统计</a></li>
			   <li class="active"><a href="info_graph.php">统计图</a></li>
		   </ul>
	  </div>
      <form action="graph.php" method="post" name="queryForm">
              <div class="output">
				    <label>关&nbsp;键&nbsp;字：</label>
					<input type="checkbox" value="all" class="allKeywords"/>全部
					<?php
						     $query="select k_id,uk_id from user_keywords where user_id=$user_id order by uk_id asc";
							 $res=mysql_query($query);
							 $uk_ids="0";
							 while($row=mysql_fetch_array($res)){
							     $k_id=$row['k_id'];
								 $uk_id=$row['uk_id'];
								 $uk_ids.=",".$uk_id;
								 $query1="select keyword from keyword where k_id=$k_id";
								 $res1=mysql_query($query1);
								 $row1=mysql_fetch_array($res1);
								 $keyword=$row1['keyword'];
					?>
					<input type="checkbox" value="<?php echo $uk_id;?>" class="keyword1" name="kids[]" /><?php echo $keyword;?>
					<?php
						   }
					?>
				</div>
				<div class="output">
					<label for="start">起始时间：</label>
					<input type="text" id="start" name="start" value="" />
					<label for="end">截止时间：</label>
					<input type="text" id="end" name="end" value="" />
					<input type="button" id="query" value="查看统计图" />
				</div>
		</form>
		
		
</div>
<div class="Content" style="padding-top:0px;">
		
</div>
<div class="Content_bottom"><img src="images/content_bottom.png" /></div>
<?php include_once('footer.php');?>
</body>
</html>
<script>
$('#start,#end').datepicker({dateFormat: 'yy-mm-dd'});
$('.keyword1').click(function(){
     $('.allKeywords').attr("checked",false);
})
$('.allKeywords').click(function(){
     if($(this).attr("checked")){
	      $('.keyword1').attr("checked",true);
	 }else{
	      $('.keyword1').attr("checked",false); 
	 }
	   
})
$('#query').click(function(){
   var kids="0";
   $("input[name='kids[]']").each(function(){        
		 if($(this).attr("checked")){
		    kids=kids+","+$(this).val();
		 }
		 	 
   })
   var start_date=$('#start').val();
   var end_date=$('#end').val();
   var audit=$('#audit').val();
   var author_type=$('#author_type').val();
   var a_type=$('#a_type').val();
   var property=$('#property').val();
   if(kids=="0"){
         alert('请选择关键字！');
		 return false;
   }
   if(start_date==""&&end_date==""){
        alert('请选择起止时间'); 
		return false;  
   }
   if(start_date!=""&&end_date==""){
        alert('请选择截止时间'); 
		return false;  
   }
   if(start_date==""&&end_date!=""){
        alert('请选择起始时间');   
		return false;
   }
   if(start_date>end_date){
        alert('开始日期应早于截止日期，请重新选择');
		return false;
   }
   /*
   var start_time = new Date(start_date.replace(/-/g,'/'));
   var end_time = new Date(end_date.replace(/-/g,'/'));
   if(end_time-start_time>2592000000){
        alert('查询跨距不能超过30天');
		return false;
   }
   */
   queryForm.submit();
})
</script>