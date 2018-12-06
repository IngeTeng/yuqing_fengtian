<?php
  include_once('check_user.php');

set_time_limit(300);//设置运行时间，防止数据多时运行时间过长而终止
ini_set('memory_limit', '256M');//修改php的运行内存限制,因为app层面导出数据出现内存不足的问题
  // 获取的html,带模拟登陆
function get_html($url, $cookie='', $proxy='', $proxy_port='', $referer='', $gzip=false) {
    $ch = curl_init();
    // 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//允许页面跳转，获取重定向
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);      // 60秒超时
    if($gzip) curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // 编码格式

    if($cookie != '') {
    	$coo = "Cookie:$cookie";
    	$headers[] = $coo;
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($referer != '') {
        curl_setopt($ch, CURLOPT_REFERER, $referer);
    }
    if($proxy != '' and $proxy_port != '') {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }

    // 获取内容
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

?>
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
  <script type="text/javascript" src="js/layer/layer.js"></script>
  <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
  <title> 微普舆情监控系统</title>
</head>
<body>
<?php
require_once('header.php');
if( isset($_POST['kids'])&&isset($_POST['audit'])&&isset($_POST['start'])&&isset($_POST['end'])&&isset($_POST['property'])&&isset($_POST['author_type'])){
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
      $quchong=isset($_POST['quchong']) ? $_POST['quchong'] : 0;
      $start_date=$_POST['start'];
      $end_date=$_POST['end'];
      $property=$_POST['property'];
      $author_type=$_POST['author_type'];
      $type=$_POST['a_type'];//文章分类
      $a_type2=isset($_POST['a_type2'])?$_POST['a_type2']:-1;//是否经销商发稿
      $media_type=isset($_POST['media_type'])?$_POST['media_type']:0;//是否经销商发稿

	  $order=$_POST['order'];
	  $filter_place=$_POST['filter_place'];
      $filter_type=$_POST['filter_type'];
      $filter_words=$_POST['filter_words'];
}else{
      echo "<script>alert('查询条件不足!');location.href='ordinary_home.php';</script>";
}
if($start_date==$end_date){
    $date=$start_date;
}else{
    $date=$start_date."至".$end_date;
}
if($type==1){
	$type_arr =  array('news');
   // $key_table="news_key";
   // $article_table="news_article";
}elseif($type==2){
	$type_arr =  array('bbs');
   // $key_table="bbs_key";
   // $article_table="bbs_article";
}elseif($type==3){
	$type_arr =  array('blog');
   // $key_table="blog_key";
   // $article_table="blog_article";
}elseif($type==4){
	$type_arr =  array('weibo');
   // $key_table="weibo_key";
   // $article_table="weibo_article";
}elseif($type==5){
	$type_arr =  array('video');
   // $key_table="video_key";
   // $article_table="video_article";
}elseif($type==6){
	$type_arr =  array('weixin');
   // $key_table="weixin_key";
   // $article_table="weixin_article";
}elseif($type==7){
	$type_arr =  array('zhidao');
   // $key_table="zhidao_key";
   // $article_table="zhidao_article";
}elseif($type==8){
	$type_arr =  array('app');
   // $key_table="app_key";
   // $article_table="app_article";
}elseif($type == 0){
	$type_arr = array('news','bbs','blog','weibo','video','weixin','zhidao','app');
	//$article_table = array('news_article','bbs_article','blog_article','weibo_article','video_article','weixin_article','zhidao_article','app_article' );
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
//                                echo $sql;
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
							     if($k_id == 99)
							     	continue;
								 $uk_id=$row2['uk_id'];
								 $query1="select keyword from keyword where k_id=$k_id";
								 $res1=mysql_query($query1);
								 $row1=mysql_fetch_array($res1);
								 $keyword=$row1['keyword'];
					?>
					<input type="checkbox" value="<?php echo $uk_id;?>" class="keyword1" name="kids[]"  <?php if(in_array($uk_id,$kids_array)) echo "checked='checked'";?>><?php echo $keyword;?></input>
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
					<input type="checkbox" value="<?php echo $uk_id;?>" class="keyword1" name="kids[]"  <?php if(in_array($uk_id,$kids_array)) echo "checked='checked'";?>><?php echo $keyword;?></input>
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
					<input type="text" id="start" name="start" value="<?php echo $start_date?>" />
					<label for="end">截止时间：</label>
					<input type="text" id="end" name="end" value="<?php echo $end_date?>" />
				</div>
				<div class="output">
				    <label for="a_type">文章分类：</label>
					<select name="a_type" id="a_type">
						<option value="0"<?php if($type==0) echo "selected='selected'";?>>全部</option>
					    <option value="1"<?php if($type==1) echo "selected='selected'";?>>新闻</option>
						<option value="2"<?php if($type==2) echo "selected='selected'";?>>论坛</option>
						<option value="3"<?php if($type==3) echo "selected='selected'";?>>博客</option>
						<option value="4"<?php if($type==4) echo "selected='selected'";?>>微博</option>
						<option value="5"<?php if($type==5) echo "selected='selected'";?>>视频</option>
						<option value="6"<?php if($type==6) echo "selected='selected'";?>>微信</option>
						<option value="7"<?php if($type==7) echo "selected='selected'";?>>知道</option>
						<option value="8"<?php if($type==8) echo "selected='selected'";?>>APP</option>

					</select>&nbsp;&nbsp;
					<div style="display:none" class="author_type">
					   <label for="isV">作者状态：</label>
					   <select name="author_type" id="author_type">    
						  <option value="0" <?php if($author_type==0) echo "selected='selected'";?>>非认证用户</option>
						  <option value="1" <?php if($author_type==1) echo "selected='selected'";?>>认证用户</option>
						  <option value="all" <?php if($author_type=='all') echo "selected='selected'";?>>全部</option>
					   </select>&nbsp;&nbsp;
					</div>
				    <label for="audit">文章状态：</label>
					<select name="audit" id="audit">					    
					    <option value="0" <?php if($audit==0) echo "selected='selected'";?>>未审核</option>
						<option value="1" <?php if($audit==1) echo "selected='selected'";?>>已审核</option>
						<option value="all" <?php if($audit=="all") echo "selected='selected'";?>>全部</option>
					</select>&nbsp;&nbsp;
					<label for="quchong">是否去重：</label>
					<select name="quchong" id="quchong">					    
					    <option value="0" <?php if($quchong==0) echo "selected='selected'";?>>不去重</option>
						<option value="1" <?php if($quchong==1) echo "selected='selected'";?>>去重</option>
					</select>&nbsp;&nbsp;
					<label for="property">文章调性：</label>
					<select name="property" id="property">					    
					    <option value="1" <?php if($property==1) echo "selected='selected'";?>>正</option>
					    <option value="0" <?php if($property==0) echo "selected='selected'";?>>中</option>
						<option value="2" <?php if($property==2) echo "selected='selected'";?>>负</option>
						<option value="3" <?php if($property==3) echo "selected='selected'";?>>不良</option>
						<option value="all"  <?php if($property=="all") echo "selected='selected'";?>>全部</option>
					</select>&nbsp;&nbsp;

					<label for="audit">排序方式：</label>
					<select name="order" id="order">					    
					    <option value="1" <?php if($order==1) echo "selected='selected'";?>>按文章发布时间降序排列</option>
						<option value="2" <?php if($order==2) echo "selected='selected'";?>>按文章采集时间降序排列</option>
					</select><br/>
					<label for="a_type2">文章类型：</label>
					<select name="a_type2" id="a_type2">
						<option value="-1" <?php if($a_type2==-1) echo "selected='selected'";?>>全部</option>
					    <option value="0" <?php if($a_type2==0) echo "selected='selected'";?>>非经销商发稿</option>
						<option value="1" <?php if($a_type2==1) echo "selected='selected'";?>>经销商发稿</option>
					</select>
                    <label for="media_type">媒体等级：</label>
                    <select name="media_type" id="media_type">
                        <option value="0" <?php if($media_type==0) echo "selected='selected'";?>>全部</option>
                        <option value="1" <?php if($media_type==1) echo "selected='selected'";?>>A级</option>
                        <option value="2" <?php if($media_type==2) echo "selected='selected'";?>>B级</option>
                        <option value="3" <?php if($media_type==3) echo "selected='selected'";?>>C级</option>
                    </select>
                </div>
				<div class="output">
				    <label for="filter_place">内容筛选：</label>
					<select name="filter_place">
					    <option value="1" <?php if($filter_place==1) echo "selected='selected'";?>>仅在标题中</option>
						<option value="2" <?php if($filter_place==2) echo "selected='selected'";?>>标题或摘要中</option>
						<option value="3" <?php if($filter_place==3) echo "selected='selected'";?>>标题或正文中</option>
					</select> 
					<select name="filter_type" id="filter_type">
					    <option value="1" <?php if($filter_type==1) echo "selected='selected'";?>>+</option>
						<option value="2" <?php if($filter_type==2) echo "selected='selected'";?>>-</option>
					</select>
					<input type="text" id="" name="filter_words" value="<?php echo $filter_words?>" />
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
					  <input type="hidden" name="order" value="<?php echo $order;?>" />
					  <input type="hidden" name="quchong" value="<?php echo $quchong;?>" />
					  <input type="hidden" name="author_type" value="<?php echo $author_type;?>" />
					  <input type="hidden" name="property" value="<?php echo $property;?>" />
					  <input type="hidden" name="filter_place" value="<?php echo $filter_place;?>" />
					  <input type="hidden" name="filter_type" value="<?php echo $filter_type;?>" />
					  <input type="hidden" name="filter_words" value="<?php echo $filter_words;?>" />
                    <input type="hidden" name="media_type" value="<?php echo $media_type;?>" />

                    <label for="file_name">文件名：</label>
				      <input type="text" name="file_name" id="file_name" size="60" value="<?php echo $date."_舆情报告"; ?>" />
					  <input type="submit" id="output" value="导出" />
				</div>
		</form>
		
		<form action="write_sheets_re.php" method="post">
				<div class="output">
					  <input type="hidden" name="start_date" value="<?php echo $start_date;?>" />
					  <input type="hidden" name="end_date" value="<?php echo $end_date;?>" />
					  <input type="hidden" name="uk_ids" value="<?php echo $uk_ids;?>" />
					  <input type="hidden" name="user_id" value="<?php echo $user_id;?>" />
					  <input type="hidden" name="audit" value="<?php echo $audit;?>" />
					  <input type="hidden" name="order" value="<?php echo $order;?>" />
					  <input type="hidden" name="quchong" value="<?php echo $quchong;?>" />
					  <input type="hidden" name="author_type" value="<?php echo $author_type;?>" />
					  <input type="hidden" name="property" value="<?php echo $property;?>" />
					  <input type="hidden" name="filter_place" value="<?php echo $filter_place;?>" />
					  <input type="hidden" name="filter_type" value="<?php echo $filter_type;?>" />
					  <input type="hidden" name="filter_words" value="<?php echo $filter_words;?>" />
                    <input type="hidden" name="media_type" value="<?php echo $media_type;?>" />

                    <label for="file_name">文件名：</label>
				      <input type="text" name="file_name" id="file_name" size="60" value="<?php echo $date."_舆情报告"; ?>" />
					  <input type="submit" id="output" value="直接导出调表后文件" />
				</div>
		</form>

        <form action="write_sheets_srcdata.php" method="post">
                <div class="output">
                    <input type="hidden" name="start_date" value="<?php echo $start_date;?>" />
                    <input type="hidden" name="end_date" value="<?php echo $end_date;?>" />
                    <input type="hidden" name="uk_ids" value="<?php echo $uk_ids;?>" />
                    <input type="hidden" name="user_id" value="<?php echo $user_id;?>" />
                    <input type="hidden" name="audit" value="<?php echo $audit;?>" />
                    <input type="hidden" name="order" value="<?php echo $order;?>" />
                    <input type="hidden" name="quchong" value="<?php echo $quchong;?>" />
                    <input type="hidden" name="author_type" value="<?php echo $author_type;?>" />
                    <input type="hidden" name="property" value="<?php echo $property;?>" />
                    <input type="hidden" name="filter_place" value="<?php echo $filter_place;?>" />
                    <input type="hidden" name="filter_type" value="<?php echo $filter_type;?>" />
                    <input type="hidden" name="filter_words" value="<?php echo $filter_words;?>" />
                    <input type="hidden" name="media_type" value="<?php echo $media_type;?>" />

                    <label for="file_name">文件名：</label>
                    <input type="text" name="file_name" id="file_name" size="60" value="<?php echo $date."_舆情报告"; ?>" />
                    <input type="submit" id="output" value="导出源数据" />
                </div>
        </form>
				  
	</div>
				<div class="Content navi" style="padding-bottom:0px;">
				    <ul style="display:inline;">
				    	<li <?php if($type==0)echo "class='active'";?>><a href="#" id="all">全部</a></li>
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
				<form action="query_result.php" method="post" name="resultByType">
				      <input type="hidden" name="start" value="<?php echo $start_date;?>" />
					  <input type="hidden" name="end" value="<?php echo $end_date;?>" />
					  <input type="hidden" name="kids" value="<?php print_r($_POST['kids']);?>" />
					  <input type="hidden" name="uk_ids" value="<?php echo $uk_ids;?>" />
					  <input type="hidden" name="audit" value="<?php echo $audit;?>" />
					  <input type="hidden" name="order" value="<?php echo $order;?>" />
					  <input type="hidden" name="quchong" value="<?php echo $quchong;?>" />
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
						      <td width="4%">选择</td><td width="30%">文章标题</td><td width="10%">媒体名称</td><td width="10%">文章调性</td><td width="6%">关键字</td><td width="10%">文章类型</td>
						      <td width="12%">
						      <?php
						      if ($order == 1)
						      {
						      	echo ("发表时间");
						      }
						      else
						      {
						      	echo ("采集时间");
						      }
						      ?>
						      </td>
                             <td width="10%">作者</td
                             ><td width="10%">类型</td>
						      <td width="4%">操作</td>
				         </tr>
					 </thead>
					 <tbody>
					    <?php					    
							 $start_time=strtotime($start_date);
						     $end_time=strtotime($end_date);
						     $time_item = "article_pubtime";
						     if($order==2)
						     {
						     	$time_item = "article_addtime";
						     }
						     $num=0;
						     foreach ($type_arr as $t) {
						     	
						     
						     
								 $query2="select article_id,article_property,id,uk_id,audit_status,a_type from ".$t."_key where uk_id in ($uk_ids) and $time_item>=$start_time and $time_item<=$end_time and status = 1 ";
								 if($audit!="all"){
								      $query2.="and audit_status=$audit ";
								 }
								 if($property!="all"){
								      $query2.="and article_property=$property ";
								 }
								 if( $a_type2 >= 0){
								 	$query2.=" and a_type=$a_type2 ";
								 }
//
								 if($quchong == 1 ){
								 	$query2 .= "  group by article_id,uk_id ";
								 }
								 if($order==1){
								     $query2.="order by article_pubtime desc";
								 }
								 if($order==2){
								     $query2.="order by article_addtime desc";
								 }
								 $query2.= " limit 2000";
//                                echo $query2;
//                                exit;
								 if($type == 0){
								 	//print_r($t);
								 	//print_r($article_table);
								 	//print_r($query2);
								 }
								 $res2=mysql_query($query2);
								// echo($query2);
								 
								 while($row2=mysql_fetch_array($res2)){
								 	
								 	//print_r($row2);
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
										  if($type==4){
										       $query3="select * from ".$t."_article where article_id=$article_id";
										  }
										  else{
										       $query3="select * from ".$t."_article where article_id=$article_id";
										  }
									 }else{							     
										  $query3="select * from ".$t."_article where article_id=$article_id and isV=$author_type";
									 }
                                     if( $media_type > 0){
//
                                         $query3.=" and article_grade = $media_type";


                                 }
									 //print_r($query3);
									 $res3=mysql_query($query3);
									
									 $x=mysql_num_rows($res3);
									 if($x==0){
                                         continue;
                                     }
									 
									 $row3=mysql_fetch_array($res3);
									 $article_title=mb_substr($row3['article_title'], 0, 80, 'utf-8');
									 $title   = 
									 $article_url=$row3['article_url'];
									 $article_pubtime=$row3['article_pubtime'];
									 if ($order == 2)
									 {
									 	$article_pubtime=$row3['article_addtime'];
									 }
									 $article_summary=$row3['article_summary'];
									 if (empty($article_summary)) {
									 	$article_summary=$row3['article_content'];
									 }
									 $article_author=$row3['article_author'];
									 if(!$article_author){
                                         $article_author="未知";
                                     }
                                     $article_is_repost=$row3['article_is_repost'];
									 if($article_is_repost==1){
                                         $article_is_repost="转载";
                                     }else if($article_is_repost==2){
                                         $article_is_repost="原创";
                                     }
                                     else
									 {
										 $article_is_repost="未知";
									 }
									 $media=$row3['media'];
									 $author = "";
									 if($type==4){
									     $media=$row3['author']."(".$media.")</br>".$row3['rz_info'];
									 }
									 $flag = false;
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
										    if($filter_type==2 ){
									           if(!strstr($article_title,$filter_words)){
										            $flag=true;
										       }
									        }
									    }
									    elseif($filter_place==2 ){
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
										 
									    }elseif( $filter_place == 3){  //按标题和正文
									    	//设置超时参数
											// $opts=array(
											//         "http"=>array(
											//                 "method"=>"GET",
											//                 "timeout"=>1
											//                 ),
											//         );
											// ////创建数据流上下文
											// $context = stream_context_create($opts);
											// $article_content = $article_summary;
									  //   	$article_summary = file_get_contents($article_url, false, $context);
									  //   	if(empty($article_summary)){
									  //   		$article_summary = $article_content; //get_html($article_url);
									  //   	}
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
									 	$num++;
				        ?>	  
						 <tr>
						     <input type="hidden" name="id" class="id" value="<?php echo $id;?>" />
							 <input type="hidden" name="article_type" class="article_type" value="<?php echo $type;?>" />
							 <td onclick="select_input(this);"><input type="checkbox" class="checkbox" name="checkbox" onclick="select_input2(this);"/></td>
					         <td style="text-align:left;">
							         <a href="<?php echo $article_url; ?>" target="_blank"  title="点击查看原文">
							                <?php echo str_replace($filter_words,"<font color='#FF0000'>$filter_words</font>",str_replace($keyword,"<font color='#FF0000'>$keyword</font>",$article_title));?>
							         </a>
							         <?php 
							         	if ($type == 1)
							         	{
							         ?>
							         <a href="news_search.php?w=<?php echo urlencode($article_title); ?>" target="_blank"><span style="color:#4a4a4a">(标题搜索)</span></a>
							 		 <?php } ?>
							 </td>
							 <td><?php echo $media;?></td>
							 <td>
							 <ul class="audit">
							    <li <?php if($audit_status==1)echo "class='active'";?>>审</li>
							 </ul>
							 <br>
							 <ul class="property">
							    <li <?php if($article_property==1)echo "class='active'";?>>正</li>
								<li <?php if($article_property==0)echo "class='active'";?>>中</li>
								<li <?php if($article_property==2)echo "class='active'";?>>负</li>
								<li <?php if($article_property==3)echo "class='active'";?>>不良</li>
							 </ul>
							 </td>
							 <td>
							     <select name="key" class="key" >
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
                             <td><?php echo $article_author;?></td>
                             <td><?php echo $article_is_repost;?></td>
							 <td><a href="#" class="del"><img src="adminv/images/dot_del.gif" width="9" height="9" alt="删除" /></a></td>
					 	 </tr>	
						 <?php
						     
						    }

						 }
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
						<input type="button" value="不良" id="badnews" />&nbsp;&nbsp;
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
	 }else if(active.text()=="不良"){
	     property=3;
	 }	 
	 var tr=$(this).parent().parent().parent();
	 var id=tr.find('.id').val();
	 var article_type=tr.find('.article_type').val();
    layer.confirm('是否更换文章调性?', {icon: 3, title:'提示'}, function(index){
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
        layer.close(index);
    });
	 
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
   var start_date=$('#start').val();
   var end_date=$('#end').val();
   var audit=$('#audit').val();
   var author_type=$('#author_type').val();
   var a_type=$('#a_type').val();
   var a_type2=$('#a_type2').val();
   var media_type=$('#media_type').val();
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
		 $("#select_all_top").attr("checked",true);
          $("input[name='checkbox']").attr("checked",true);
	}else{
	      $("input[name='checkbox']").attr("checked",false);
		 $("#select_all_top").attr("checked",false);

	}
})

function selectall(obj)
{
	$("#remove,#antiAll").attr("checked",false);
	if (obj.checked)
	{
		$("input[name='checkbox']").attr("checked",true);
		$("#select_all").attr("checked",true);
	}
	else
	{
		$("input[name='checkbox']").attr("checked",false);
		$("#select_all").attr("checked",false);
	}
}
function select_input(obj)
{
	var a = obj.children[0];
	if (a.checked)
	{
		a.checked = false;
	}
	else
	{
		a.checked = true;
	}
}
function select_input2(obj)
{
	if (obj.checked)
	{
		obj.checked = false;
	}
	else
	{
		obj.checked = true;
	}
}

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
$('#audit_select').click(function(){
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
$('#positive,#objective,#negative,#badnews').click(function(){
     var property;
	 var text;
     if($(this).attr("id")=="positive"){property=1;text="正";}
	 else if($(this).attr("id")=="objective"){property=0;text="中";}
	 else if($(this).attr("id")=="negative"){property=2;text="负";}
     else if($(this).attr("id")=="badnews"){property=3;text="不良";}
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

$('#all,#news,#bbs,#blog,#weibo,#video,#weixin,#zhidao,#app').click(function(){
      var type;
      if($(this).attr('id')=="all"){
	      type=0;
	  }else if($(this).attr('id')=="news"){
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