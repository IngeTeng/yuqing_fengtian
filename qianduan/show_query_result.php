<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="Author" content="微普科技http://www.wiipu.com"/>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="styles/global.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="styles/style.css" type="text/css"/>
  <link rel="stylesheet" type="text/css" href="styles/jquery-ui.css" />
  <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui.js"></script>
  <script type="text/javascript" src="js/jquery-ui-slide.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
  <title> 微普舆情监控系统</title>
</head>
<body>
<?php
if(!isset($_COOKIE['user_id'])){
   echo "<script>alert('您尚未登陆！');location.href='index.php';</script>";
}else{
  require_once('adminv/inc_dbconn.php');
  require_once('show_header.php');
  $user_id=$_COOKIE['user_id'];
  if(isset($_POST['kids'])&&isset($_POST['audit'])&&isset($_POST['start'])&&isset($_POST['end'])&&isset($_POST['property'])&&isset($_POST['author_type'])){
      $kids_array=$_POST['kids'];
      if(is_array($kids_array)){
	      $uk_ids="0";
	      foreach($kids_array as $value){
	           $uk_ids.=",".$value;   
	      }
	  }else{
	      $uk_ids=$_POST['uk_ids'];
		  $kids_array=explode(",",$uk_ids);  
	  }
      $audit=$_POST['audit'];
      $start_date=$_POST['start'];
      $end_date=$_POST['end'];
      $property=$_POST['property'];
      $author_type=$_POST['author_type'];
      $type=$_POST['a_type'];
	  $order=$_POST['order'];
      $filter_place=$_POST['filter_place'];
      $filter_type=$_POST['filter_type'];
      $filter_words=$_POST['filter_words'];
}else{
  echo "<script>alert('查询条件不足!');location.href='show_home.php';</script>";
}
if($start_date==$end_date){
    $date=$start_date;
}else{
    $date=$start_date."至".$end_date;
}
if($type==1){
   $key_table="news_key";
   $article_table="news_article";
}elseif($type==2){
   $key_table="bbs_key";
   $article_table="bbs_article";
}elseif($type==3){
   $key_table="blog_key";
   $article_table="blog_article";
}elseif($type==4){
   $key_table="weibo_key";
   $article_table="weibo_article";
}elseif($type==5){
   $key_table="video_key";
   $article_table="video_article";
}elseif($type==6){
   $key_table="weixin_key";
   $article_table="weixin_article";
}elseif($type==7){
   $key_table="zhidao_key";
   $article_table="zhidao_article";
}elseif($type==8){
   $key_table="app_key";
   $article_table="app_article";
}
?>
<div class="Navigation">
	<ul>
    	<li><a href="#">首页</a></li>
        <li><a href="#">舆情概况</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
	<form action="show_query_result.php" method="post" name="queryForm">
				<div class="output">
				    <table width="100%" style="border:none;">
				    <tr style="border:none;">
					<td valign="top" width="8%" style="border:none;text-align: left;">
				    <label>关&nbsp;键&nbsp;字：</label>
					</th>
					<td style="border:none;text-align: left;">
					<input type="checkbox" value="all" class="allKeywords"/>全部
					<?php
						     $query="select user_id from user where user_type=1 order by user_id asc limit 1";
							 $res=mysql_query($query);
							 $row=mysql_fetch_array($res);
							 $special_id=$row['user_id'];    
						     $query="select k_id,uk_id from user_keywords where user_id=$special_id order by uk_id asc limit 5";
							 $res=mysql_query($query);
							 while($row=mysql_fetch_array($res)){
							     $k_id=$row['k_id'];
								 $uk_id=$row['uk_id'];
								 $query1="select keyword from keyword where k_id=$k_id";
								 $res1=mysql_query($query1);
								 $row1=mysql_fetch_array($res1);
								 $keyword=$row1['keyword'];
					?>
					<input type="checkbox" value="<?php echo $uk_id;?>" class="keyword1" name="kids[]" <?php if(in_array($uk_id,$kids_array)) echo "checked='checked'";?>  /><?php echo $keyword;?>
					<?php
						   }
					?>
					</td>
					</tr>
				</table>
				</div>
				<div class="output">
					<label for="start">起始时间：</label>
					<input type="text" id="start" name="start" value="<?php echo $start_date?>" />
					<label for="end">截止时间：</label>
					<input type="text" id="end" name="end" value="<?php echo $end_date?>" />
				</div>
				<div class="output">
				    <label for="a_type">文章分类：</label>
					<select name="a_type" id="a_type">
					    <option value="1"<?php if($type==1) echo "selected='selected'";?>>新闻</option>
						<option value="2"<?php if($type==2) echo "selected='selected'";?>>论坛</option>
						<option value="3"<?php if($type==3) echo "selected='selected'";?>>博客</option>
						<option value="4"<?php if($type==4) echo "selected='selected'";?>>微博</option>
						<option value="5"<?php if($type==5) echo "selected='selected'";?>>视频</option>
						<option value="6"<?php if($type==6) echo "selected='selected'";?>>微信</option>
						<option value="7"<?php if($type==7) echo "selected='selected'";?>>知道</option>
						<option value="8"<?php if($type==8) echo "selected='selected'";?>>APP</option>

					</select>
					<div style="display:none" class="author_type">
					   <label for="isV">作者状态：</label>
					   <select name="author_type" id="author_type">    
						  <option value="0" <?php if($author_type==0) echo "selected='selected'";?>>非认证用户</option>
						  <option value="1" <?php if($author_type==1) echo "selected='selected'";?>>认证用户</option>
						  <option value="all" <?php if($author_type=='all') echo "selected='selected'";?>>全部</option>
					   </select>
					</div>
				    <label for="audit">文章状态：</label>
					<select name="audit" id="audit">					    
					    <option value="0" <?php if($audit==0) echo "selected='selected'";?>>未审核</option>
						<option value="1" <?php if($audit==1) echo "selected='selected'";?>>已审核</option>
						<option value="all" <?php if($audit=="all") echo "selected='selected'";?>>全部</option>
					</select>
					<label for="property">文章调性：</label>
					<select name="property" id="property">					    
					    <option value="1" <?php if($property==1) echo "selected='selected'";?>>正</option>
					    <option value="0" <?php if($property==0) echo "selected='selected'";?>>中</option>
						<option value="2" <?php if($property==2) echo "selected='selected'";?>>负</option>
						<option value="all"  <?php if($property=="all") echo "selected='selected'";?>>全部</option>
					</select>
					<label for="audit">排序方式：</label>
					<select name="order" id="order">					    
					    <option value="1" <?php if($order==1) echo "selected='selected'";?>>按文章发布时间降序排列</option>
						<option value="2" <?php if($order==2) echo "selected='selected'";?>>按文章采集时间降序排列</option>
					</select>
				</div>
				<div class="output">
				    <label for="filter_place">内容筛选：</label>
					<select name="filter_place">
					    <option value="1" <?php if($filter_place==1) echo "selected='selected'";?>>仅在标题中</option>
						<option value="2" <?php if($filter_place==2) echo "selected='selected'";?>>标题或摘要中</option>
					</select> 
					<select name="filter_type" id="filter_type">
					    <option value="1" <?php if($filter_type==1) echo "selected='selected'";?>>+</option>
						<option value="2" <?php if($filter_type==2) echo "selected='selected'";?>>-</option>
					</select>
					<input type="text" id="" name="filter_words" value="<?php echo $filter_words?>" />
					<input type="button" id="query" value="查询" />
				</div>
		</form>				  
	</div>
				<div class="Content navi" style="padding-bottom:0px;">
				    <ul style="display:inline;">
		            	<li <?php if($type==1)echo "class='active'";?>><a href="#" id="news">新闻</a></li>
			            <li <?php if($type==2)echo "class='active'";?>><a href="#" id="bbs">论坛</a></li>
			            <li <?php if($type==3)echo "class='active'";?>><a href="#" id="blog">博客</a></li>
			            <li <?php if($type==4)echo "class='active'";?>><a href="#" id="weibo">微博</a></li>			
			            <li <?php if($type==5)echo "class='active'";?>><a href="#" id="video">视频</a></li>		
		            	<li <?php if($type==6)echo "class='active'";?>><a href="#" id="weixin">微信</a></li>		
			            <li <?php if($type==7)echo "class='active'";?>><a href="#" id="zhidao">知道</a></li>		
			            <li <?php if($type==8)echo "class='active'";?>><a href="#" id="app">APP</a></li>		

		            </ul>
					<div id="total"></div>
				</div>
				<form action="show_query_result.php" method="post" name="resultByType">
				      <input type="hidden" name="start" value="<?php echo $start_date;?>" />
					  <input type="hidden" name="end" value="<?php echo $end_date;?>" />
					  <input type="hidden" name="kids" value="<?php print_r($_POST['kids']);?>" />
					  <input type="hidden" name="uk_ids" value="<?php echo $uk_ids;?>" />
					  <input type="hidden" name="audit" value="<?php echo $audit;?>" />
					  <input type="hidden" name="author_type" value="<?php echo $author_type;?>" />
					  <input type="hidden" name="property" value="<?php echo $property;?>" />
					  <input type="hidden" name="a_type" id="type" /> 
					  <input type="hidden" name="filter_place" value="<?php echo $filter_place;?>" />
					  <input type="hidden" name="filter_type" value="<?php echo $filter_type;?>" />
					  <input type="hidden" name="filter_words" value="<?php echo $filter_words;?>" /> 
				</form>
				<div class="Content" style="padding-top:0px;">
				  <table border="0" cellspacing="0" cellpadding="0" width="900">
				     <thead class="Header Center">
				         <tr>
						      <td width="40%">文章标题</td><td width="11%">媒体名称</td><td width="11%">文章调性</td><td width="11%">关键字</td><td width="12%">文章类型</td><td width="15%">发表时间</td>
				         </tr>
					 </thead>
					 <tbody>
					    <?php					    
							 $start_time=strtotime($start_date);
						     $end_time=strtotime($end_date);
							 $query2="select article_id,article_property,id,uk_id,audit_status,a_type from $key_table where uk_id in (".$uk_ids.") and article_pubtime>$start_time and article_pubtime<$end_time ";
							 if($audit!="all"){
							      $query2.="and audit_status=$audit ";
							 }
							 if($property!="all"){
							      $query2.="and article_property=$property ";
							 }
							 if($order==1){
							     $query2.="order by article_pubtime desc";
							 }
							 if($order==2){
							     $query2.="order by article_addtime desc";
							 }
							 $query2.= " limit 2000";
							 $res2=mysql_query($query2);
							 $num=0;
							 while($row2=mysql_fetch_array($res2)){
							     $article_id=$row2['article_id'];
								 $audit_status=$row2['audit_status'];
								 $a_type=$row2['a_type'];
								 $id=$row2['id'];
								 $uk_id=$row2['uk_id'];
								 $query4="select k_id from user_keywords where uk_id=$uk_id";
							     $res4=mysql_query($query4);
								 $row4=mysql_fetch_array($res4);
								 $k_id=$row4['k_id'];
								 $query5="select keyword from keyword where k_id=$k_id";
								 $res5=mysql_query($query5);
								 $row5=mysql_fetch_array($res5);
								 $keyword=$row5['keyword'];
								 $article_property=$row2['article_property'];
								 if($author_type=="all"){
									  if($type==4){
									       $query3="select article_title,article_url,article_pubtime,media from $article_table where article_id=$article_id";
									  }else{
									       $query3="select article_title,article_url,article_pubtime,media,article_summary from $article_table where article_id=$article_id";
									  }
								 }else{							     
									  $query3="select article_title,article_url,article_pubtime,media,rz_info from $article_table where article_id=$article_id and isV=$author_type";
								 }
								 $res3=mysql_query($query3);
								
								 $x=mysql_num_rows($res3);
								 if($x==0){
								     continue;
								 }
								 $row3=mysql_fetch_array($res3);
								 $article_title=$row3['article_title'];
								 $article_url=$row3['article_url'];
								 $article_pubtime=$row3['article_pubtime'];
								 $media=$row3['media'];
								 if($type==4){
								     $media=$media."</br>".$row3['rz_info'];
								 }
								 if($filter_words==""){
								    $flag=true;
								 }else{
								    $flag=false;
								    if($filter_place==1){
								        if($filter_type==1){
								           if(strstr($article_title,$filter_words)){
									            $flag=true;
									       }
								        }
									    if($filter_type==2){
								           if(!strstr($article_title,$filter_words)){
									            $flag=true;
									       }
								        }
								    }
								    if($filter_place==2){
								        if($filter_type==1){
								           if(strstr($article_title,$filter_words)||strstr($article_summary,$filter_words)){
									            $flag=true;
										   }
									    }
									    if($filter_type==2){
								           if(!strstr($article_title,$filter_words)&&!strstr($article_summary,$filter_words)){
									            $flag=true;
										   }
									    }
									 
								    }
								 }
								 
								 if($flag){	
				        ?>	
					     
						 <tr>
					         <td style="text-align:left;">
							      <a href="<?php echo $article_url; ?>" target="_blank"  title="点击查看原文">
							             <?php echo str_replace($filter_words,"<font color='#FF0000'>$filter_words</font>",str_replace($keyword,"<font color='#FF0000'>$keyword</font>",$article_title));?>
							      </a>
							 </td>
							 <td><?php echo $media;?></td>
							 <td>
							 <ul class="audit">
							    <li <?php if($audit_status==1)echo "class='active'";?>>审</li>
							 </ul>
							 <ul class="property">
							    <li <?php if($article_property==1)echo "class='active'";?>>正</li>
								<li <?php if($article_property==0)echo "class='active'";?>>中</li>
								<li <?php if($article_property==2)echo "class='active'";?>>负</li>
							 </ul>
							 </td>
							 <td>
							     <?php
						                  $query4="select k_id from user_keywords where uk_id=$uk_id";
							              $res4=mysql_query($query4);
							              $row4=mysql_fetch_array($res4);
							              $k_id=$row4['k_id'];
								          $query5="select keyword from keyword where k_id=$k_id";
								          $res5=mysql_query($query5);
								          $row5=mysql_fetch_array($res5);
								          $keyword=$row5['keyword'];
						                  echo $keyword;
						        ?>
							 </td>
							 <td>
							      <?php 
									         if($a_type==0)echo "非经销商发稿";
									         elseif($a_type==1)echo "经销商发稿";
									         elseif($a_type==2)echo "竞品攻击";elseif($a_type==3)echo "非车主投诉";
											 elseif($a_type==4)echo "车主投诉";
								  ?>
							 </td>
						     <td><span class="time"><?php echo date('Y-m-d H:i',$article_pubtime);?></span></td>
					 	 </tr>	
						 <?php
						   $num++;
						   }
						 }
						 ?>	
						 </tbody>
						 </table>
				</div>
                <div class="Content_bottom"><img src="images/content_bottom.png" /></div>
			</div>
			<div class="clear"></div>
		</div>
		<?php include_once('footer.php');?>
		<div class="clear"></div>
	</div>
 </div>
 <?php
 }
 ?>
 </body>
</html>
<script>
var type=<?php echo $type;?>;
if(type==4){
        $('.author_type').css('display','inline');
}else{
        $('.author_type').css('display','none');
}
var num=<?php echo $num;?>;
$('#total').text("共 "+num+" 条");
/*$("#start,#end").datepicker({dateFormat:"yy-mm-dd"});*/
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
   var start_date=$('#start').val();
   var end_date=$('#end').val();
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
$('#news,#bbs,#blog,#weibo,#video,#weixin,#zhidao,#app').click(function(){
      var type;
	  if($(this).attr('id')=="news"){
	      type=1;
	  }else if($(this).attr('id')=="bbs"){
	      type=2;
	  }else if($(this).attr('id')=="blog"){
	      type=3;
	  }else if($(this).attr('id')=="weibo"){
	      type=4;
	  }else if($(this).attr('id')=="video"){
	      type=5;
	  }else if($(this).attr('id')=="weixin"){
	      type=6;
	  }else if($(this).attr('id')=="zhidao"){
	      type=7;
	  }else if($(this).attr('id')=="app"){
	      type=8;
	  }
	  $('#type').val(type);
	  resultByType.submit(); 
})
</script>