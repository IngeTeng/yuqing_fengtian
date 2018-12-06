<?php
  include_once('check_user.php');
?>
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
  require_once('header.php');
?>
<div class="Navigation">
	<ul>
    	<li><a href="ordinary_home.php">首页</a></li>
        <li><a href="#">关键字管理</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content" style="padding-bottom:0px;text-align:right;">
<a href="add_keyword.php">添加关键字</a>
</div>
<div class="Content" style="padding-top:0px;">
				  <table border="0" cellspacing="0" cellpadding="0" width="500px">
				     <thead class="Header Center">
				         <tr>
						      <td width="30%">关键字</td><td width="20%">所属分类</td><td width="30%">添加时间</td><td width="10%">操作</td>
				         </tr>
					 </thead>
					 <tbody>
					   <?php 
					   		$sql = "select * from user_category where user_id = $user_id order by c_id asc";
					   		$clist = array();
					   		$clist[] = array("id"=>0, "name"=>"未分类");
					   		$res = mysql_query($sql);
					   		while ($row = mysql_fetch_array($res))
					   		{
					   			$clist[] = array("id"=>$row["c_id"], "name"=>$row["category_name"]);
					   		}
					   		mysql_free_result($res);
					   		
						     $query="select k_id,uk_id,add_time,c_id from user_keywords where user_id=$user_id order by uk_id desc";
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
						     <input type="hidden" class="addTime"  value="<?php echo $add_time?>"/>
							 <input type="hidden" class="uk_id" value="<?php echo $uk_id;?>" />
							 <input type="hidden" class="k_id" value="<?php echo $k_id;?>" />
							 <td><?php echo $keyword;?></td>
							 <td>
							 	<select name="c_id" class="c_id">
							 	<?php
							 	for ($i = 0; $i < count($clist); $i++)
							 	{
							 		$selected = "";
							 		if ($clist[$i]["id"] == $row["c_id"])
							 		{
							 			$selected = "selected='selected'";
							 		}
							 	?>
							 		<option value="<?php echo $clist[$i]["id"] ?>" <?php echo $selected ?>><?php echo $clist[$i]["name"] ?></option>
							 	<?php
							 	}
							 	?>
							 	</select>
							 </td>
						     <td><span class="time"><?php echo date('Y-m-d H:i',$add_time);?></span></td>
							 <td><span class="del" style="cursor:pointer"><img src="adminv/images/dot_del.gif" width="9" height="9" alt="删除" /></span></td>
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
});

$('.c_id').change(function(){
      var c_id=$(this).val();
	  var tr=$(this).parent().parent();
	  var uk_id=tr.find('.uk_id').val();
	  $.ajax({
	      url:"update_keyword_category_ajax.php",
		  type:"POST",
		  data:{c_id:c_id,uk_id:uk_id},
		  dataType:"json",
		  error:function(){},
		  success:function(){}
	 })
	  
})
</script>