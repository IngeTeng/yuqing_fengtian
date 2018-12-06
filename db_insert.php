<?php 
	header("content-type:text/html;charset=utf-8");
	session_start();
	ob_start();
	
	//配置数据库连接参数
	define('WIIDBHOST','47.92.204.34');
	define('WIIDBUSER','root');
	define('WIIDBPASS','*Wiipuyuqing#');
	define('WIIDBNAME','yuqing');
    define('WIIDBPRE','info');

    // $db_connect=mysqli_connect(WIIDBHOST,WIIDBUSER,WIIDBPASS);
	// if (!$db_connect){
	// 	die ('数据库连接失败');
    // }
    // mysqli_select_db( $db_connect,'yuqing') or die ("没有找到数据库。");
	// mysqli_query($db_connect,"set names utf8;");
	
	$db_connect=new PDO('mysql:host=47.92.204.34;dbname=yuqing',WIIDBUSER,WIIDBPASS, array(
		PDO::ATTR_PERSISTENT => true));

		// $medialist_sql='select * from media_list limit 0,5';
		// $ml_res=$db_connect->query($medialist_sql);
		// $option="";
		// foreach($ml_res as $row ){
		// 	$option .= "<option value ='' >".$row['media_name']."</option>";
		// }
		//	print $option;

	?>
	<html>
	<style>
	.news{
		width:700px;
		height:120px;
	}
	.s-news{
		width:300px;
		height:40px;
	}
	.s-select{
		width:200px;
		height:40px;
	}
	.form-list{

	}
	</style>
	<body>
    <div class="container" style="text-align:center">
	<form action ="" method="POST"> 
		<!-- <div class="form-list"> -->	
			<p>关键字：</p>
			<input class="s-news" id="keyword" name="keyword" type="text" />
			
		<!-- </div> -->
	<?php 
	 if(!empty($_POST['submit'])) {
		$keyword=$_POST['keyword'];
		$ky_sql ='select uk_id from user_keywords left join keyword on user_keywords.k_id=keyword.k_id where keyword.keyword='.'"'.$keyword.'"';
	   	$uk_id=$db_connect->query($ky_sql)->fetch()['uk_id'];
	  print "uk_id: ".$uk_id;
	 } //echo $_POST['keyword'];
	
	?>
		<div class="form-list">
			<p>媒体类型：</p>
			<select class="s-select" id="media_type" name="media_type" onclick="change(this)">
    			<!-- <option value="" name="" style="font-size: 15px">-请选择-</option> -->
				<option value="0"  style="font-size: 15px">新闻</option>
				<option value="1"  style="font-size: 15px">APP</option>
				<option value="2"  style="font-size: 15px">微信</option>
				<option value="3"  style="font-size: 15px">微博</option>

			</select>
		<!-- </div> -->
		<!-- <div class="form-list"> -->
			<p>is_repost：</p>
			<select class="s-select" id="is_repost" name="is_repost" onclick="change(this)">
    			<!-- <option value="" name="" style="font-size: 15px">-请选择-</option> -->
				<option value="0"  style="font-size: 15px">0</option>
				<option value="1"  style="font-size: 15px">1</option>
			
			</select>
		<!-- </div> -->
		<!-- <div class="form-list"> -->
			<p>grade:</p>
			<select class="s-select" id="grade" name="grade" onclick="change(this)">
    			<!-- <option value="" name="" style="font-size: 15px">-请选择-</option> -->
				<option value="1"  style="font-size: 15px">1</option>
				<option value="2"  style="font-size: 15px">2</option>
				<option value="3"  style="font-size: 15px">3</option>
			</select>
		</div>
		<!-- <div class="form-list"> -->
			<p> 媒体名称：</p>
			<!-- <select  class="s-select" id="media-name" name="" onclick="change1(this)">
    			
			</select> -->
			<input class="s-news" id="media_name"  name="media_name" type="text" placeholder="" />
		<!-- </div> -->
		<!-- <div class="form-list"> -->
			<p>发布时间：</p>
			<input class="s-news" id="pubtime" name="pubtime" type="text" placeholder="yyyy-MM-dd" />
		<!-- </div> -->
		<!-- <div class="form-text"> -->
			<p>新闻标题：</p>
			<input class="news" id="title" name="title" type="text" />
		<!-- </div> -->
		<!-- <div class="form-text"> -->
			<p>作者：</p>
			<input  class="s-news" id="author" name="author" type="text" />
		<!-- </div> -->
		<!-- <div class="form-text"> -->
			<p>内容</p>
			<input class="news" id="content" name="content" type="text" />
			<p>链接：</p>
			<input class="news" id="article_url" name="article_url" type="text" />
		<!-- </div> -->
		<input type="submit" value="提交" name="submit"/>
	</form>
	</div>
	<?php 
		$table="";$key="";
		if($_POST['media_type']==0){$table="news_article";$key="news_key";}
		if($_POST['media_type']==1){$table="app_article";$key="app_article";}
		if($_POST['media_type']==2){$table="weixin_article";$key="weixin_key";}
		if($_POST['media_type']==3){$table="weibo_article";$key="weibo_key";}
		//echo $_POST['media_type'];
		$article_url=$_POST['article_url'];
		$article_title=$_POST['title'];
	 	$article_content=$_POST['content'];
		$article_pubtime=strtotime($_POST['pubtime']);
		$article_addtime=time();
		$media=$_POST['media_name'];
		$article_author=$_POST['author'];
		$article_is_repost=$_POST['is_repost'];
		$article_grade=$_POST['grade'];
		
	    if($_POST['media_type']==0 || $_POST['media_type'] ==1 ){
			try{
				$db_connect->beginTransaction();
				$article_insert='insert into '.$table.'(article_site,article_url,article_title,article_content,
					article_pubtime,article_addtime,media,article_author,article_is_repost,article_grade) 
					VALUES(0,"'.$article_url.'","'.$article_title.'","'.$article_content.'","'.$article_pubtime.'","'.
					$article_addtime.'","'.$media.'","'.$article_author.'","'.$article_is_repost.
					'","'.$article_grade.'")';
				
				echo $article_insert."\n";
				$res=$db_connect->exec($article_insert);
				$article_id=$db_connect->lastInsertId();
				print "article_id : -- ".$article_id."\n";
				if($article_id != 0){
					$key_insert ='insert into '.$key.'(uk_id,article_id,article_property,article_pubtime,
					audit_status,audit_time,a_type,user_id,article_addtime,status)VALUES('.$uk_id.','.
					$article_id.',2,'.$article_pubtime.',0,0,0,1,'.$article_addtime.',1)';
			   		echo $key_insert."\n";
			   		$key_res=$db_connect->exec($key_insert);
					$key_id=$db_connect->lastInsertId();
					$db_connect->commit();
				}
				
				$db_connect->rollback();
				
			}catch(PDOException $e){
				$db_connect->rollback();
				echo $e->getMessage();
	
			}
		}
		if($_POST['media_type']== 3 ){//微博grade 3
			try{
				$db_connect->beginTransaction();
				$article_insert='insert into '.$table.'(article_url,article_title,
					article_pubtime,article_addtime,media,mid,article_author,article_is_repost,article_grade) 
					VALUES("'.$article_url.'","'.$article_title.'","'.$article_pubtime.'","'.
					$article_addtime.'","'.$media.'"," ","'.$article_author.'","'.$article_is_repost.
					'","'.$article_grade.'")';
				
				echo $article_insert."\n";
				$res=$db_connect->exec($article_insert);
				$article_id=$db_connect->lastInsertId();
				print "article_id : -- ".$article_id."\n";
				if($article_id != 0){
					$key_insert ='insert into '.$key.'(uk_id,article_id,article_property,article_pubtime,
					audit_status,audit_time,a_type,user_id,article_addtime,status)VALUES('.$uk_id.','.
					$article_id.',2,'.$article_pubtime.',0,0,0,1,'.$article_addtime.',1)';
			   		echo $key_insert."\n";
			   		$key_res=$db_connect->exec($key_insert);
					$key_id=$db_connect->lastInsertId();
					echo 'key_id: '.$key_id;
					$db_connect->commit();
				}
				
				
			}catch(PDOException $e){
				$db_connect->rollback();
				echo $e->getMessage();
	
			}
			if($_POST['media_type'] ==2 ){
				// try{
				// 	$db_connect->beginTransaction();
				// 	$article_insert='insert into '.$table.'(article_url,article_title,
				// 		article_pubtime,article_addtime,media,mid,article_author,article_is_repost,article_grade) 
				// 		VALUES("'.$article_url.'","'.$article_title.'","'.$article_pubtime.'","'.
				// 		$article_addtime.'","'.$media.'"," ","'.$article_author.'","'.$article_is_repost.
				// 		'","'.$article_grade.'")';
					
				// 	echo $article_insert."\n";
				// 	$res=$db_connect->exec($article_insert);
				// 	$article_id=$db_connect->lastInsertId();
				// 	print "article_id : -- ".$article_id."\n";
				// 	if($article_id != 0){
				// 		$key_insert ='insert into '.$key.'(uk_id,article_id,article_property,article_pubtime,
				// 		audit_status,audit_time,a_type,user_id,article_addtime,status)VALUES('.$uk_id.','.
				// 		$article_id.',2,'.$article_pubtime.',0,0,0,1,'.$article_addtime.',1)';
				// 		   echo $key_insert."\n";
				// 		   $key_res=$db_connect->exec($key_insert);
				// 		$key_id=$db_connect->lastInsertId();
				// 		echo 'key_id: '.$key_id;
				// 		$db_connect->commit();
				// 	}
					
					
				// }catch(PDOException $e){
				// 	$db_connect->rollback();
				// 	echo $e->getMessage();
		
				// }
			}
			$db_connect=null;
		}
  		
	?>
	</body>
	</html>
