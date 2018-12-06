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
        <li><a href="#">舆情概况</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
      <form action="query_result.php" method="post" name="queryForm">
              <div class="output">
				    <table width="100%" style="border:none;">
				    <tr style="border:none;">
					<td valign="top" width="8%" style="border:none;text-align: left;">
				    <label>关&nbsp;键&nbsp;字：</label>
					</th>
					<td style="border:none;text-align: left;">
					<input type="checkbox" value="all" class="allKeywords"/><strong>全部</strong>
					
					<?php
						     require_once('adminv/inc_dbconn.php');
							 $sql = "select * from user_category where user_id = $user_id order by c_id asc";
							 $res = mysql_query($sql);
							 while ($row = mysql_fetch_array($res))
							 {
					?>
								<p>
									<input type="checkbox" value="all" class="catKeywords"/><span style="color:#0000ca;font-weight:bold;"><?php echo $row["category_name"] ?>：</span>
					<?php
							 	$c_id = $row["c_id"];
							 	if($c_id == 40){
							 		$query="select k_id,uk_id from user_keywords where user_id=$user_id and c_id = $c_id order by weight desc";
							 	}else{
							 		$query="select k_id,uk_id from user_keywords where user_id=$user_id and c_id = $c_id order by uk_id asc ";
							 	}
							 	
							 	$res2=mysql_query($query);

							 	while($row2=mysql_fetch_array($res2)){
							     $k_id=$row2['k_id'];
							     // if($k_id == 99)
							     // 	continue;
								 $uk_id=$row2['uk_id'];
								 $query1="select keyword from keyword where k_id=$k_id";
								 $res1=mysql_query($query1);
								 $row1=mysql_fetch_array($res1);
								 $keyword=$row1['keyword'];
					?>
					<input type="checkbox" value="<?php echo $uk_id;?>" class="keyword1" name="kids[]" /><?php echo $keyword;?>
					<?php
						   }
					
					?>
								</p>
								
					<?php
							 }
					?>
						<p>
									<input type="checkbox" value="all" class="catKeywords"/><span style="color:#0000ca;font-weight:bold;">未分类：</span>
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
					<input type="checkbox" value="<?php echo $uk_id;?>" class="keyword1" name="kids[]" /><?php echo $keyword;?>
					<?php
						   }
					?>
						</p>
					</td>
					</tr>
				</table>
				</div>
				<div class="output">
					<label for="start">起始时间：</label>
					<input type="text" id="start" name="start" value="" />
					<label for="end">截止时间：</label>
					<input type="text" id="end" name="end" value="" />
				</div>
				<div class="output">
				    <label for="a_type">文章分类：</label>
					<select name="a_type" id="a_type">
						<option value="0" selected="selected">全部</option>
					    <option value="1">新闻</option>
						<option value="2">论坛</option>
						<option value="3">博客</option>
						<option value="4">微博</option>
						<option value="5">视频</option>
						<option value="6">微信</option>
						<option value="7">知道</option>
						<option value="8">APP</option>
					</select>&nbsp;&nbsp;
					<div style="display:none" class="author_type">
					   <label for="isV">作者状态：</label>
					   <select name="author_type" id="author_type">
					      <option value="all" selected="selected">全部</option>
						  <option value="0">非认证用户</option>
						  <option value="1">认证用户</option>
					   </select>&nbsp;&nbsp;
					</div>
				    <label for="audit">文章状态：</label>
					<select name="audit" id="audit">
					    <option value="all" selected="selected">全部</option>
					    <option value="0">未审核</option>
						<option value="1">已审核</option>
					</select>&nbsp;&nbsp;
					<label for="quchong"> 是否去重：</label>
					<select name="quchong" id="quchong">
					 
					    <option value="0" selected="selected">不去重</option>
						<option value="1">去重</option>
					</select>&nbsp;&nbsp;
					<label for="property">文章调性：</label>
					<select name="property" id="property">
					    <option value="all" selected="selected">全部</option>
					    <option value="1">正</option>
					    <option value="0">中</option>
						<option value="2">负</option>
					</select>&nbsp;&nbsp;

					<label for="order">排序方式：</label>
					<select name="order" id="order">
					    <option value="1" selected="selected">按文章发布时间降序排列</option>
					    <option value="2" >按文章采集时间降序排列</option>
					</select>
					<br/>
					<label for="a_type2">文章类型：</label>
					<select name="a_type2" id="a_type2">
						<option value="-1" selected="selected">全部</option>
					    <option value="0" >非经销商发稿</option>
						<option value="1">经销商发稿</option>
					</select>
                    <label for="media_type">媒体等级：</label>
                    <select name="media_type" id="media_type">
                        <option value="0" selected>全部</option>
                        <option value="1" >A级</option>
                        <option value="2" >B级</option>
                        <option value="3" >C级</option>
                    </select>
				</div>
				<div class="output">
				    <label for="filter_place">内容筛选：</label>
					<select name="filter_place">
					    <option value="1" selected="selected">仅在标题中</option>
						<option value="2">标题或摘要中</option>
						<option value="3">标题或正文中</option>
					</select> 
					<select name="filter_type" id="filter_type">
					    <option value="1" selected="selected">+</option>
						<option value="2">-</option>
					</select>
					<input type="text" id="" name="filter_words" value="" />
					<input type="button" id="query" value="查询" />
				</div>
		</form>
</div>
<div class="Content_bottom"><img src="images/content_bottom.png" /></div>
<?php include_once('footer.php');?>
</body>
</html>
<script>
$('#start,#end').datetimepicker({
showSecond: true, //显示秒
dateFormat: 'yy-mm-dd',
timeFormat: 'hh:mm:ss',//格式化时间
stepHour: 1,//设置步长
stepMinute: 1,
stepSecond: 1
});
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
$('.catKeywords').click(function(){
    // alert("123");
     var p = $(this).parent();
     //alert(p);
     var ks = p.find('.keyword1');
    // alert(ks.length);
     if($(this).attr("checked"))
     {
     	for (var i = 0; i < ks.length; i++)
     	{
     		ks[i].checked = true;
	    }
	 }
	 else
	 {
	    for (var i = 0; i < ks.length; i++)
     	{
	     	ks[i].checked = false;
	    }
	 }
	   
})
$('#a_type').change(function(){
   if($(this).val()==4){
        $('.author_type').css('display','inline');
   }else{
        $('.author_type').css('display','none');
   }
})
$('#query').click(function(){
   var kids="0";
   $("input[name='kids[]']").each(function(){        
		 if($(this).attr("checked")){
		    kids=kids+","+$(this).val();
		 }
		 	 
   })
  // $('#uk_ids').val(kids);
   var start_date=$('#start').val();
   var end_date=$('#end').val();
   var audit=$('#audit').val();
   var author_type=$('#author_type').val();
   var a_type=$('#a_type').val();
   var a_type2=$('#a_type2').val();
   var property=$('#property').val();
   var media_type=$('#media_type').val();
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
   var start_time = new Date(start_date.replace(/-/g,'/'));
   var end_time = new Date(end_date.replace(/-/g,'/'));
   if(end_time-start_time>2592000000){
        alert('查询跨距不能超过30天');
		return false;
   }
   queryForm.submit();
})
</script>