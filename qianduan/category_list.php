<?php
  include_once('check_user.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>分类管理</title>
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
        <li><a href="#">分类管理</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content" style="padding-bottom:0px;text-align:right;">
<a href="add_category.php">添加分类</a>
</div>
<div class="Content" style="padding-top:0px;">
				  <table border="0" cellspacing="0" cellpadding="0" width="350">
				     <thead class="Header Center">
				         <tr>
						      <td width="30%">分类</td><td width="50%">添加时间</td><td width="20%">操作</td>
				         </tr>
					 </thead>
					 <tbody>
					   <?php
						     
						     $query="select * from user_category where user_id=$user_id order by c_id asc";
							 $res=mysql_query($query);
							 while($row=mysql_fetch_array($res))
							 {
							     $c_id=$row['c_id'];
								 $category_name=$row['category_name'];
								 $add_time=$row['add_time'];				
					   ?>
                        <tr>
							 <input type="hidden" class="c_id" value="<?php echo $c_id;?>" />
							 <td><?php echo $category_name;?></td>
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
   if (!confirm("确定要删除选中的分类？ 删除分类后， 分类下的关键字仍然存在"))
   {
   		return;
   }
   var tr=$(this).parent().parent();
   var c_id=tr.find('.c_id').val();

   $.ajax({
		url:"delete_category_ajax.php",
		type:"post",
		data:{c_id:c_id},
		dataType:"json",
		error:function(){},
		success:function(){
			     tr.remove();
		}	   
	});
   
});
</script>