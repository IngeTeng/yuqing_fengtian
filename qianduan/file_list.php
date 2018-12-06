<?php
  include_once('check_user.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>文件管理</title>
<link href="styles/global.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<style>
td{
text-align:center;
}
</style>
</head>
<body>
<?php
  include_once('header.php');
?>
<div class="Navigation">
	<ul>
    	<li><a href="ordinary_home.php">首页</a></li>
        <li><a href="#">文件管理</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
<a href="javascript:del_all()" style="float:right"><img src="adminv/images/dot_del.gif" alt="" />删除选中</a>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
  <tr class="table_Header">
  	<td width="5%">
  	<input type="checkbox" onclick="checkall(this)">
  	</td>
    <td width="28%">文件名称</td>
    <td width="15%">关键字</td>
    <td width="14%">起始时间</td>
    <td width="14%">截止时间</td>
    <td width="14%">导出时间</td>
    <td width="10%">操作</td>
  </tr>
                       <?php
                       
$pagesize=20;
$select="select count(*) as page_count from file_list";
$rest=mysql_query($select);
$rs=mysql_fetch_array($rest);
$count=$rs['page_count'];
if($count%$pagesize){
$pagecount = intval($count/$pagesize)+1;
}else{
	$pagecount = intval($count/$pagesize);
}
if(isset($_GET['page'])){
	$page=intval($_GET['page']);
}else{
	$page=1;
}
$pagestart = ($page-1)*$pagesize;
                       
							 $query="select * from file_list where user_id=$user_id order by export_time desc limit ".$pagestart.",".$pagesize;
							 $res=mysql_query($query);
							 $num=mysql_num_rows($res);
							 $out_num = 0;
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
						     <input type="hidden" class="file_id" value="<?php echo $file_id;?>" />
							 <input type="hidden" class="user_id" value="<?php echo $user_id;?>" />
					         <td>
					         <input type="checkbox" value="<?php echo $file_id;?>" name="fid_<?php echo $out_num++ ?>" id="fid_<?php echo $out_num++ ?>">
					         </td>
					         <td style="text-align:left;"><?php echo $file_name;?></td>
							 <td><?php echo $keyword;?></td>
						     <td><span class="time"><?php echo date('Y-m-d H:i',$start_time);?></span></td>
							 <td><span class="time"><?php echo date('Y-m-d H:i',$end_time);?></span></td>
							 <td><span class="time"><?php echo date('Y-m-d H:i',$export_time);?></span></td>
							 <td><a target="_blank" href="<?php echo $user_id."/".$file_id.".xls";?>" class="word-btn btn-success">下载</a>&nbsp;|&nbsp;
							     <a href="#" class="delete">删除</a></td>
					 	 </tr>	
						 <?php
						 }
						 ?>	
						 <input type="hidden" name="out_num" id="out_num" value="<?php echo $out_num ?>">
						<input type="hidden" id="g_user_id" value="<?php echo $user_id;?>" />

</table>
					<div class="page">
						<div class="pagebefore">当前页:<?php echo $page;?>/<?php echo $pagecount;?>页 每页 <?php echo $pagesize?> 条</div>
						<div class="pageafter">
						<?php echo showPage("file_list.php",$page,$pagecount);?>
						<div class="clear"></div>
						</div>
					</div>
</div>
<div class="Content_bottom"><img src="images/content_bottom.png" /></div>
<?php include_once('footer.php');?>
</body>
</html>
<script>
$('.delete').click(function(){
    var tr=$(this).parent().parent();
	var file_id=tr.find('.file_id').val();
	var user_id=tr.find('.user_id').val();
	$.ajax({
	     url:"delete_file_ajax.php",
		 type:"post",
		 data:{file_id:file_id,user_id:user_id},
		 dataType:"json",
		 error:function(){},
		 success:function(data){
		       if(data['result']=="success"){
                   tr.remove();
			   }else{
			       alert('删除失败');
			   }
		 }
	})
       
})

function checkall(obj)
{
	var num = $("#out_num").val();
	var ckd = obj.checked;
	//alert(num);
	for (var i = 0; i < num; i++)
	{
		$("#fid_"+i).attr("checked", ckd);
	}
}
function del_all()
{
	var num = $("#out_num").val();
	var del_ids = "0";
	for (var i = 0; i < num; i++)
	{
		if ($("#fid_"+i).attr("checked"))
		{
			del_ids += "," + $("#fid_"+i).val();
		}
	}
	if (del_ids == "0")
	{
		alert("请先选择要删除的文件!");
		return;
	}
	if (!confirm("确定要删除选中的文件?"))
	{
		return;
	}
	var user_id = $("#g_user_id").val();
	$.ajax({
	     url:"delete_files_ajax.php",
		 type:"post",
		 data:{fids:del_ids,user_id:user_id},
		 dataType:"json",
		 error:function(){},
		 success:function(data){
		       if(data['result']=="success")
		       {
                   alert("文件删除成功!");
                   window.location.href="file_list.php";
			   }
			   else
			   {
			       alert('删除失败');
			   }
		 }
	})
}
</script>
