<?php
require_once('inc_dbconn.php');
$op = $_GET["op"];
switch($op)
{
	
	case 'add':		
		$name=trim($_POST['name']);
		$url=trim($_POST['url']);
		$crawl=trim($_POST['crawl']);
		$update=trim($_POST['update']);
		$stress=trim($_POST['stress']);
		$intv=rand(1800,7200);
		
		$hr1=trim($_POST['hr1']);
		$hr2=trim($_POST['hr2']);
		$hr3=trim($_POST['hr3']);
		$hr4=trim($_POST['hr4']);

		$tr1=trim($_POST['tr1']);
		$tr2=trim($_POST['tr2']);
		$tr3=trim($_POST['tr3']);
		$tr4=trim($_POST['tr4']);
		
		$site_type =$_POST['site_type'];
		if($site_type==5){
		   $intv=rand(7200,14400); 
		}
		$time=time();
		$sql="insert into spider_site(site_name,site_url,site_crawldepth,site_updatedepth,site_interval,site_urlhold1,site_urlhold2,site_urlhold3,site_urlhold4,site_urlthrow1,site_urlthrow2,site_urlthrow3,site_urlthrow4,site_addtime,site_type,stress) values('$name','$url',$crawl,$update,$intv,'$hr1','$hr2','$hr3','$hr4','$tr1','$tr2','$tr3','$tr4',$time,$site_type,$stress)";
		if(mysql_query($sql))
		{   
			$sql = "insert into auto_work(aw_type,aw_time) values(1,".$time.")";
			mysql_query($sql);
			echo "<script>alert('添加成功!');location.href='sitelist.php';</script>";
			
		}
		else
		{
		     echo "<script>alert('模板添加失败，请重试!');location.href='addsite.php';</script>";
		}

		

		break;
	case 'edit':
		$site_id=trim($_GET['site_id']);
		$name=trim($_POST['name']);
		$url=trim($_POST['url']);
		$crawl=trim($_POST['crawl']);
		$update=trim($_POST['update']);
		$stress=trim($_POST['stress']);
		$intv=trim($_POST['intv']);
		
		$hr1=trim($_POST['hr1']);
		$hr2=trim($_POST['hr2']);
		$hr3=trim($_POST['hr3']);
		$hr4=trim($_POST['hr4']);

		$tr1=trim($_POST['tr1']);
		$tr2=trim($_POST['tr2']);
		$tr3=trim($_POST['tr3']);
		$tr4=trim($_POST['tr4']);
		$site_type=$_POST['site_type'];
		$time=time();
		$sql_u="update spider_site set site_name='$name',site_url='$url',site_crawldepth='$crawl',site_updatedepth='$update',site_interval='$intv',site_urlhold1='$hr1',site_urlhold2='$hr2',site_urlhold3='$hr3',site_urlhold4='$hr4',site_urlthrow1='$tr1',site_urlthrow2='$tr2',site_urlthrow3='$tr3',site_urlthrow4='$tr4',site_type=$site_type,stress=$stress where site_id=$site_id";
		if(mysql_query($sql_u))
		{
		    $sql = "insert into auto_work(aw_type,aw_time) values(1,".$time.")";
			mysql_query($sql);
			echo "<script language='javascript'>alert('编辑成功');window.location.href='sitelist.php'</script>";
		}else{
			echo "<script language='javascript'>alert('编辑失败，请重新编辑');window.location.href='editsite.php?site_id=$site_id'</script>";
		}

		break;

	case 'del':
		$site_id=$_GET['site_id'];
		$time=time();
		$sql = "delete from spider_site where site_id=$site_id";
		if(mysql_query($sql)){
		   $sql = "insert into auto_work(aw_type,aw_time) values(1,$time)";
		   mysql_query($sql);
		   echo "<script>alert('删除成功');location.href='sitelist.php';</script>";
	    }else{
		   echo "<script>alert('删除失败');location.href='sitelist.php';</script>";
		}
		break;
 }

?>