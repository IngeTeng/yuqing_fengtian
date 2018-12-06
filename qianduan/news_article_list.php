<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>文章查询</title>
<link href="styles/global.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<style>
td{
text-align:center;
}
.add{
margin: 0 auto;
text-align: center;
font-size: 20px;
padding-bottom: 5px;
}
</style>
</head>
<body>
<?php
  if(!isset($_COOKIE['user_id'])){
      echo "<script>alert('您尚未登陆！');location.href='index.php';</script>";
      return;
  }
  require_once('adminv/inc_dbconn.php');
  $user_id=$_COOKIE['user_id'];
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
    	<li><a href="ordinary_home.php">首页</a></li>
        <li><a href="#">稿件追踪</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
<p class="add">
<?php
if($user_type!=2){
?>
<a href="add_article.php">添加文章</a>
<?php
}
?>
</p>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
  <tr class="table_Header">
    <td width="35%">文章标题</td>
    <td width="65%">文章内容</td>
  </tr>
  <tbody class="article">
                         <?php
							 $query="select * from news_article_index where user_id=$user_id order by add_time desc";
							 $res=mysql_query($query);
							 while($row=mysql_fetch_array($res)){
							     $id=$row['id'];
							     $article_title=$row['article_title'];
								 $article_content=$row['article_content'];
                         ?>	
						 
                         <tr>
						     <input type="hidden" class="id" value="<?php echo $id;?>" />
							 <input type="hidden" class="user_id" value="<?php echo $user_id;?>" />
					         <td style="text-align:left;"><a href="#" class="article_title"><?php echo $article_title;?></a></td>
							 <td><?php echo $article_content;?></td>						 
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
var user_type=<?php echo $user_type;?>;
$('.article_title').click(function(){
    var article_title=$(this).text();
	$.ajax({
	     url:"article_title_ajax.php",
		 type:"post",
		 data:{article_title:article_title},
		 dataType:"json",
		 error:function(){},
		 success:function(data){
		       $('.article').html('');
			   var a_ids="0";
			   for(i=0;i<data.length;i++){
			        $('.article').append('<tr><td style="text-align:left;"><a href="'+data[i]['url']+'" target="_blank">'+data[i]['title']+'</a></td><td>'+data[i]['content'].substr(0,100)+'</td></tr>');
					a_ids=a_ids+","+data[i]['article_id'];	
			   }
			   if(user_type==2){
			        $('.add').html('<div class="add"><a href="news_article_list.php">返回文章列表</a></div>');
			   }else{
			       $('.add').html('<div class="add"><form action="write_news.php" method="post" style="display:inline;"><input type="text" name="file_name" size="50"/><input type="hidden" name="a_ids" id="a_ids" /><input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id?>"/><input type="submit" value="导出" /></form>	<a href="news_article_list.php">返回文章列表</a></div>'); 
			       $('#a_ids').val(a_ids);
			   }	     
		 }
	})     
})
</script>
