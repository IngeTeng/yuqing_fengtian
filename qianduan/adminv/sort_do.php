<?php
/**
 * 文章分类的增、删、改处理页- sort_do.php
 *
 * @version       v0.01
 * @create time   2012-4-28
 * @update time   
 * @author        liuxiao
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 */
	require_once('admincheck.php');
	require_once('inc_dbconn.php');
	//得到GET方法传递的值，确定进行的操作
	$act = sqlReplace(trim($_GET['act']));
	//检测$act的值
	if(!$act=='add'||!$act=='update'||!$act=='del'){
		alertInfo('非法操作！','',1);
	}
	switch($act){
		//*增加
		case 'add':
			//得到sort_add传递的值，并检测
			$sort_name = sqlReplace(trim($_POST['sort_name']));
			$sort_order = sqlReplace(trim($_POST['sort_order']));
			$has_parent = sqlReplace(trim($_POST['has_parent']));
			$sql_add = "";
			if ($has_parent == "0")
			{
				$sql_add = "insert into info_sort (sort_name,sort_order)values('$sort_name','$sort_order')";
			}
			else
			{
				$parent_id = sqlReplace(trim($_POST['parent_id']));
				$sql = "select sort_name from info_sort where sort_id = ". $parent_id;
				$rs = mysql_query($sql);
				$row = mysql_fetch_array($rs);
				$parent_name = $row["sort_name"];
				mysql_free_result($rs);
				$sql_add = "insert into info_sort(sort_name,sort_order, parent_id, parent_name) values('$sort_name','$sort_order', '$parent_id', '$parent_name')";
			}
			if(mysql_query($sql_add)){
				alertInfo('分类添加成功','sortlist.php',0);
			}else{
				alertInfo('分类添加失败','',1);
			}
			break;
		case 'update':
			//得到sortlist传递的值，并检测
			$id = sqlReplace(trim($_GET['id']));
			$sort_name = sqlReplace(trim($_POST['sort_name']));
			$sort_order = sqlReplace(trim($_POST['sort_order']));
			if($id==""){
				alertInfo('非法操作','sortlist.php',0);
			}
			$has_parent = sqlReplace(trim($_POST['has_parent']));
			$sql_update =  "";
			if ($has_parent == "0")
			{
				$sql_update = "update info_sort set sort_name='$sort_name',sort_order = $sort_order,parent_id=0,parent_name='' where sort_id = ".$id;
			}
			else
			{
				$parent_id = sqlReplace(trim($_POST['parent_id']));
				$sql = "select sort_name from info_sort where sort_id = ". $parent_id;
				$rs = mysql_query($sql);
				$row = mysql_fetch_array($rs);
				$parent_name = $row["sort_name"];
				mysql_free_result($rs);
				$sql_update = "update info_sort set sort_name='$sort_name',sort_order = $sort_order,parent_id=$parent_id,parent_name='$parent_name' where sort_id = ".$id;
			}
			if(mysql_query($sql_update)){
				alertInfo('修改成功！','sortlist.php',0);
			}else{
				alertInfo('修改失败！','',1);
			}
			break;
		case 'del':
			//得到sortlist传递的值，并检测
			$id = sqlReplace(trim($_GET['id']));
			if($id==""){
				alertInfo('非法操作','sortlist.php',0);
			}
			$sql_del = "delete from info_sort where sort_id = $id";
			if(mysql_query($sql_del)){
				alertInfo('分类删除成功','sortlist.php',0);
			}
			break;
		//保存排序
		case 'save':
			$i=$_POST['i'];
		echo $i;
		for($x=1;$x<=$i;$x++){
				$sort_id = $_POST['sort_id'.$x];
				$sort_order = $_POST['sort_order'.$x];

				checkData($sort_id,'分类ID',0);
				checkData($sort_order,'分类排序',0);

				$sql = "update info_sort set sort_order = ".$sort_order." where sort_id = ".$sort_id;

				if(!mysql_query($sql)){
					alertInfo('未知原因保存失败! ',"sortlist.php",0);
				}
			}
			alertInfo('保存排序成功!',"sortlist.php",0);

			break;
			
	}
	

	
?>