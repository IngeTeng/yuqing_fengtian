<?php
//显示15天内的文件
for ($i=0; $i < 15; $i++) { 
	$file_path[$i]  = date('Y-m-d', strtotime('-'. $i. ' day'));
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>日报列表</title>
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
        <li><a href="#">日报列表</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
<p class="add">
<?php
if($user_type!=2){
?>
<?php
}
?>
</p>

          <?php
							 foreach ($file_path as $key => $value) {
								  if(is_dir('auto_files/'.$value)){
  									$files = glob('auto_files/'.$value.'/*');
                    //print_r($files);
                    $html = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
                        <tr class="table_Header">
                          <td width="80%">文件名</td>
                          <td width="20%"></td>
                        </tr>
                        <tbody class="article"><caption style="padding:20px 0px;font-size:28px;" >'.$value.'</caption>';
  									foreach ($files as $file) {
  										$file_tmp = iconv('gb2312', 'utf-8', $file);
                      $file = iconv('gb2312', 'utf-8', $file);
                      //print_r($file);
                      $file_tmp = trim(strrchr($file_tmp, '/'), '/');//获取无路径的文件名

                      $html .= '<tr>
                                  <td style="text-align:left;"><a href="download_file.php?filename='.$file.'" class="article_title">'.$file_tmp.'</a></td>
                                  <td><a href="download_file.php?filename='.$file.'" class="article_title">下载</a></td>             
                              </tr>  ';

                    }
                    $html .= ' </tbody>
                       </table>';
                    echo $html;
                  }
                }
							
           ?>	
						 
                      
						 


</div>
<div class="Content_bottom"><img src="images/content_bottom.png" /></div>
<?php include_once('footer.php');?>
</body>
</html>
<script>
</script>
