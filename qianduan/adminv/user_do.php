<?php
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	$act =$_GET['act'];
	switch($act){
	    case 'add':
               $user_name=addslashes(trim($_POST['user_name']));
               $password=md5(addslashes(trim($_POST['password'])));
               $user_type=addslashes($_POST['user_type']);
               $key_num=addslashes(trim($_POST['key_num']));
			   $query="select user_id from user where email='$user_name'";
			   $res=mysql_query($query);
			   $num=mysql_num_rows($res);
			   if($num>0){
			      echo "<script>alert('此用户名已被使用，换个用户名试试！');location.href='add_user.php';</script>"; 
				  return;
			   }
			   if($key_num==""){
			       $key_num=0;
			   }
               $time=time();
               $insert="insert into user(email,password,key_num,reg_time,user_type) values('$user_name','$password',$key_num,$time,$user_type)";
            if(mysql_query($insert)){
                  echo "<script>alert('添加成功！');location.href='userlist.php';</script>";
            }else{
                  echo "<script>alert('添加失败！');location.href='add_user.php';</script>";
            }
            break;
		case 'end':
			
			$id = sqlReplace(trim($_GET['id']));
			if($id==""){
				alertInfo('非法操作','userlist.php',0);
			}
			$sql_end = "update user set status=1 where user_id = $id";
			if(mysql_query($sql_end)){
				alertInfo('停止服务成功','userlist.php',0);
			}
			break;
		case 'start':
			$id = sqlReplace(trim($_GET['id']));
			if($id==""){
				alertInfo('非法操作','userlist.php',0);
			}
			$sql_end = "update user set status=0 where user_id = $id";
			if(mysql_query($sql_end)){
				alertInfo('开启服务成功','userlist.php',0);
			}
			break;
		//保存排序
		case 'edit_pass':
		    $password = sqlReplace(trim($_POST['password']));
			$id =$_POST['user_id'];
			$pass=md5($password);
			$update = "update user set password='$pass' where user_id = $id";
			if(mysql_query($update)){
				alertInfo('修改密码成功','userlist.php',0);
			}
			break;
		case 'save':
			$i=$_POST['i'];
		for($x=1;$x<=$i;$x++){
				$user_id = $_POST['user_id'.$x];
				$key_num = $_POST['key_num'.$x];
				$sql = "update user set key_num = ".$key_num." where user_id = ".$user_id;

				if(!mysql_query($sql)){
					alertInfo('未知原因保存失败! ',"userlist.php",0);
				}
			}
			alertInfo('保存关键字数目成功!',"userlist.php",0);

			break;
			
	}
?>