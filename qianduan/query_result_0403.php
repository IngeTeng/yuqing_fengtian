<?php
  include_once('check_user.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="Author" content="微普科技http://www.wiipu.com"/>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="styles/global.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="styles/style.css" type="text/css"/>
  <link rel="stylesheet" type="text/css" href="styles/jquery-ui.css" />
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui.js"></script>
  <script type="text/javascript" src="js/jquery-ui-slide.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
  <title> 微普舆情监控系统</title>
</head>
<body>
<?php
require_once('header.php');
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
}else{
      echo "<script>alert('查询条件不足!');location.href='ordinary_home.php';</script>";
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
	<form action="query_result.php" method="post" name="queryForm">
				<div class="output">
				    <label>关&nbsp;键&nbsp;字：</label>
					<input type="checkbox" value="all" class="allKeywords"/>全部
					<?php
						     require_once('adminv/inc_dbconn.php');
						     $query="select k_id,uk_id from user_keywords where user_id=$user_id order by uk_id asc";
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
					<input type="button" id="query" value="查询" />
				</div>
		</form>
		
		<form action="write_sheets.php" method="post">
				<div class="output">
					  <input type="hidden" name="start_date" value="<?php echo $start_date;?>" />
					  <input type="hidden" name="end_date" value="<?php echo $end_date;?>" />
					  <input type="hidden" name="uk_ids" value="<?php echo $uk_ids;?>" />
					  <input type="hidden" name="user_id" value="<?php echo $user_id;?>" />
					  <input type="hidden" name="audit" value="<?php echo $audit;?>" />
					  <input type="hidden" name="author_type" value="<?php echo $author_type;?>" />
					  <input type="hidden" name="property" value="<?php echo $property;?>" />
					  <label for="file_name">文件名：</label>
				      <input type="text" name="file_name" id="file_name" size="60" value="<?php echo $date."_舆情报告"; ?>" />
					  <input type="submit" id="output" value="导出" />
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
		            </ul>
					<div id="total"></div>
				</div>
				<form action="query_result.php" method="post" name="resultByType">
				      <input type="hidden" name="start" value="<?php echo $start_date;?>" />
					  <input type="hidden" name="end" value="<?php echo $end_date;?>" />
					  <input type="hidden" name="kids" value="<?php print_r($_POST['kids']);?>" />
					  <input type="hidden" name="uk_ids" value="<?php echo $uk_ids;?>" />
					  <input type="hidden" name="audit" value="<?php echo $audit;?>" />
					  <input type="hidden" name="author_type" value="<?php echo $author_type;?>" />
					  <input type="hidden" name="property" value="<?php echo $property;?>" />
					  <input type="hidden" name="a_type" id="type" />  
				</form>
				<div class="Content" style="padding-top:0px;">
				  <table border="0" cellspacing="0" cellpadding="0" width="900">
				     <thead class="Header Center">
				         <tr>
						      <td width="4%">选择</td><td width="40%">文章标题</td><td width="10%">媒体名称</td><td width="10%">文章调性</td><td width="10%">关键字</td><td width="10%">文章类型</td><td width="12%">发表时间</td><td width="4%">操作</td>
				         </tr>
					 </thead>
					 <tbody>
					    <?php					    
							 $start_time=strtotime($start_date);
						     $end_time=strtotime($end_date);
							 if($audit=="all"&&$property=="all"){
							       $query2="select article_id,article_property,id,uk_id,audit_status,a_type from $key_table where uk_id in (".$uk_ids.") and article_pubtime>$start_time and article_pubtime<$end_time order by article_pubtime desc";
							 }elseif($audit=="all"&&$property!="all"){
							       $query2="select article_id,article_property,id,uk_id,audit_status,a_type from $key_table where uk_id in (".$uk_ids.") and article_pubtime>$start_time and article_pubtime<$end_time and article_property=$property order by article_pubtime desc";
							 }elseif($audit!="all"&&$property=="all"){
							       $query2="select article_id,article_property,id,uk_id,audit_status,a_type from $key_table where uk_id in (".$uk_ids.") and article_pubtime>$start_time and article_pubtime<$end_time and audit_status=$audit order by article_pubtime desc";
							 }elseif($audit!="all"&&$property!="all"){
							       $query2="select article_id,article_property,id,uk_id,audit_status,a_type from $key_table where uk_id in (".$uk_ids.") and article_pubtime>$start_time and article_pubtime<$end_time and audit_status=$audit and article_property=$property order by article_pubtime desc";
							 }
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
								      //$query3="select article_title,article_url,article_pubtime,media from $article_table where article_id=$article_id";
									  $query3="select article_title,article_url,article_pubtime,media from $article_table where article_id=$article_id";
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
				        ?>	
					     
						 <tr>
						     <input type="hidden" name="id" class="id" value="<?php echo $id;?>" />
							 <input type="hidden" name="article_type" class="article_type" value="<?php echo $type;?>" />
							 <td><input type="checkbox" class="checkbox" name="checkbox" /></td>
					         <td style="text-align:left;">
							         <a href="<?php echo $article_url; ?>" target="_blank"  title="点击查看原文">
							                <?php echo str_replace($keyword,"<font color='#FF0000'>$keyword</font>",$article_title);?>
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
							     <select name="key" class="key">
								       <?php
						     
						                  $query4="select k_id,uk_id from user_keywords where user_id=$user_id";
							              $res4=mysql_query($query4);
							              while($row4=mysql_fetch_array($res4)){
							                   $k_id=$row4['k_id'];
								               $query5="select keyword from keyword where k_id=$k_id";
								               $res5=mysql_query($query5);
								               $row5=mysql_fetch_array($res5);
								               $keyword=$row5['keyword'];
						                 ?>
						                 <option value="<?php echo $k_id;?>" <?php if($row4['uk_id']==$uk_id) echo "selected='selected'"; ?>><?php echo $keyword;?></option>
						                 <?php
						                   }
						                 ?>
								 </select>
							 </td>
							 <td>
							     <select name="a_type" class="a_type">
								       <option value="0" <?php if($a_type==0)echo "selected='selected'";?>>非经销商发稿</option>
									   <option value="1" <?php if($a_type==1)echo "selected='selected'";?>>经销商发稿</option>
									   <option value="2" <?php if($a_type==2)echo "selected='selected'";?>>竞品攻击</option>
									   <option value="3" <?php if($a_type==3)echo "selected='selected'";?>>非车主投诉</option>
									   <option value="4" <?php if($a_type==4)echo "selected='selected'";?>>车主投诉</option>
								 </select>
							 </td>
						     <td><span class="time"><?php echo date('Y-m-d H:i',$article_pubtime);?></span></td>
							 <td><a href="#" class="del"><img src="adminv/images/dot_del.gif" width="9" height="9" alt="删除" /></a></td>
					 	 </tr>	
						 <?php
						  $num++;
						 }
						 ?>	
						 </tbody>
						 </table>
				</div>
                <div class="Content_bottom"><img src="images/content_bottom.png" /></div>
				<div id="bottom_block">
				        <input type="checkbox" id="select_all" />全选&nbsp;
						<input type="checkbox" id="remove" />取消&nbsp;
						<input type="checkbox" id="antiAll" />反选&nbsp;&nbsp;	
						<input type="button" value="审核所选" id="audit_select" />&nbsp;&nbsp;
						<input type="button" value="正面" id="positive" />&nbsp;&nbsp;
						<input type="button" value="中性" id="objective" />&nbsp;&nbsp;
						<input type="button" value="负面" id="negative" />&nbsp;&nbsp;
						<input type="button" value="删除所选" id="delete" />&nbsp;&nbsp;
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<?php include_once('footer.php');?>	
		<div class="clear"></div>
	</div>
 </div>
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
$('.property li').click(function(){
     var active=$(this);
	 var property;
	 if(active.text()=="正"){
	     property=1;
	 }else if(active.text()=="中"){
	     property=0;
	 }else if(active.text()=="负"){
	     property=2;
	 }	 
	 var tr=$(this).parent().parent().parent();
	 var id=tr.find('.id').val();
	 var article_type=tr.find('.article_type').val();
	 $.ajax({
	      url:"update_article_property_ajax.php",
		  type:"POST",
		  data:{a_id:id,a_type:article_type,pro:property},
		  dataType:"json",
		  error:function(){},
		  success:function(){
		         active.parent().find('li').removeAttr('class');
	             active.attr('class','active');
		  }
	 })
	 
})
$('.audit li').click(function(){
     var active=$(this); 
	 var tr=$(this).parent().parent().parent();
	 var id=tr.find('.id').val();
	 var article_type=tr.find('.article_type').val();
	 $.ajax({
	      url:"update_audit_status_ajax.php",
		  type:"POST",
		  data:{a_id:id,a_type:article_type},
		  dataType:"json",
		  error:function(){},
		  success:function(){
	             active.attr('class','active');
		  }
	 })
	 
})
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
   var start_time = new Date(start_date.replace(/-/g,'/'));
   var end_time = new Date(end_date.replace(/-/g,'/'));
   if(end_time-start_time>2592000000){
        alert('查询跨距不能超过30天');
		return false;
   }
   //location.href="query_result2.php?kids="+kids+"&start="+start_date+"&end="+end_date+"&audit="+audit+"&a_type="+a_type+"&author_type="+author_type+"&property="+property;
    queryForm.submit();
})
$('.del').click(function(){
      var tr=$(this).parent().parent(); 
	  var article_type=tr.find('.article_type').val();
	  var a_id=tr.find('.id').val();
      $.ajax({
                   url: "del_article_ajax.php",  
                   type: "POST",
                   data:{id:a_id,type:article_type},
                   dataType: "json",
                   error: function(){},  
                   success: function(data){
					        tr.remove(); 
			         } 					   
      });
})
$('.a_type').change(function(){
      var type=$(this).val();
	  var tr=$(this).parent().parent();
	  var article_type=tr.find('.article_type').val();
	  var id=tr.find('.id').val();
	  $.ajax({
	      url:"update_article_type_ajax.php",
		  type:"POST",
		  data:{a_id:id,a_type:article_type,at:type},
		  dataType:"json",
		  error:function(){},
		  success:function(){}
	 })
	  
})
$('.key').change(function(){
      var k_id=$(this).val();
	  var u_id=<?php echo $user_id;?>;
	  var tr=$(this).parent().parent();
	  var article_type=tr.find('.article_type').val();
	  var id=tr.find('.id').val();
	  $.ajax({
	      url:"update_keyword_ajax.php",
		  type:"POST",
		  data:{a_id:id,a_type:article_type,key_id:k_id,user_id:u_id},
		  dataType:"json",
		  error:function(){},
		  success:function(){}
	 })
	  
})
$('#select_all').click(function(){
    $("#remove,#antiAll").attr("checked",false);
    if($(this).attr("checked")){
          $("input[name='checkbox']").attr("checked",true);
	}else{
	      $("input[name='checkbox']").attr("checked",false);
	}
})
$('#remove').click(function(){
     $(this).attr("checked",true);
     $("#select_all,#antiAll").attr("checked",false);
     $("input[name='checkbox']").attr("checked",false);
})
$("#antiAll").click(function(){
     $("#select_all,#remove").attr("checked",false);
     $("input[name='checkbox']").each(function(){
           $(this).attr("checked",!this.checked);  
	 })            
});
$('#delete').click(function(){
     $("input[name='checkbox']").each(function(){
          if($(this).attr("checked")){
			  var tr=$(this).parent().parent();
	          var a_id=tr.find('.id').val();
	          var article_type=tr.find('.article_type').val();
			  $.ajax({
                   url: "del_article_ajax.php",  
                   type: "POST",
                   data:{id:a_id,type:article_type},
                   dataType: "json",
                   error: function(){},  
                   success: function(data){
					      tr.remove();
			        } 					   
              });	    
		  }  
	 })  
});
$('#auditSelect').click(function(){
     var flag = 0;
     $("input[name='checkbox']").each(function(){
          if($(this).attr("checked")){
          	  flag = 1;
			  var tr=$(this).parent().parent();
			  var active=tr.find('.audit li');
	          var id=tr.find('.id').val();
	          //alert(id);
	          var article_type=tr.find('.article_type').val();
			  $.ajax({
	            url:"update_audit_status_ajax.php",
		        type:"POST",
		        data:{a_id:id,a_type:article_type},
		        dataType:"json",
		        error:function(){},
		        success:function()
		        {
	                  tr.remove();
		        }
	         })
		  }  
	 });
	 if (flag == 0)
	 {
	 	alert("请先选择需要审核的数据");
	 }
	 else
	 {
	 	alert('审核完成,选中数据已转入已审核状态');
	 }
});
$('#positive,#objective,#negative').click(function(){
     var property;
	 var text;
     if($(this).attr("id")=="positive"){property=1;text="正";}
	 else if($(this).attr("id")=="objective"){property=0;text="中";}
	 else if($(this).attr("id")=="negative"){property=2;text="负";}
     $("input[name='checkbox']").each(function(){
          if($(this).attr("checked")){
			  var tr=$(this).parent().parent();
			  //var active=tr.find('.audit li');
	          var id=tr.find('.id').val();
	          var article_type=tr.find('.article_type').val();
			  $.ajax({
	              url:"update_article_property_ajax.php",
		          type:"POST",
		          data:{a_id:id,a_type:article_type,pro:property},
		          dataType:"json",
		          error:function(){},
		          success:function(){
				        tr.find('.property li').removeAttr('class');
				        tr.find('.property li').each(function(){
						    if($(this).text()==text){
							    $(this).attr('class','active');
							} 
						})
		          }
	          })
		  }  
	 })  
});
$('#news,#bbs,#blog,#weibo,#video').click(function(){
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
	  }
	  $('#type').val(type);
	  resultByType.submit(); 
})
</script>