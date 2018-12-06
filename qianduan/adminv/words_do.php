<?php
require_once('inc_dbconn.php');
$op = $_GET["op"];
$time = time();
switch($op){
	case 'add':
		$word=trim($_POST['word']);
        $property=$_POST['type'];

		$sql="insert into property(word,w_type) values('$word',$property)";
		if(mysql_query($sql))
		{
			$sql = "insert into auto_work(aw_type,aw_time) values(3,$time)";
			mysql_query($sql);
			echo "<script>alert('添加成功!');location.href='words_list.php?type=$property';</script>";	
		}else{
			echo "<script>alert('添加失败!请重试');location.href='add_word.php?type=$property';</script>";
		}
	break;
        
    case 'del':
        $w_id=$_GET['w_id'];
		$type=$_GET['type'];
        $sql="delete from property where w_id=$w_id";
        if(mysql_query($sql)){
            $sql = "insert into auto_work(aw_type,aw_time) values(3,$time)";
			mysql_query($sql);
			echo "<script>alert('删除成功');location.href='words_list.php?type=$type';</script>";	
		}else{		
			echo "<script>alert('删除失败');location.href='words_list.php?type=$type';</script>";
		}
    break;
    
    case 'edit':
        $w_id=$_POST['w_id'];
		$type=$_POST['type'];
		$word=$_POST['word'];

       $sql="update property set word='$word' where w_id=$w_id";
        if(mysql_query($sql))
        {
            $sql = "insert into auto_work(aw_type,aw_time) values(3,$time)";
			mysql_query($sql);
			echo "<script language='javascript'>alert('编辑成功');window.location.href='words_list.php?type=$type'</script>";	
        }else{
            echo "<script language='javascript'>alert('编辑失败，请重新编辑');window.location.href='edit_word.php?w_id=$w_id&type=$type'</script>";
        }     
    break;
}

?>