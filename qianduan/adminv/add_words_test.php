<?php
/**
** 自动导入负面词或正面词
**/
require_once('inc_dbconn.php');
$file = 'chr-fu.txt';
$file_content = file_get_contents('chr-fu.txt');
$file_arr = explode("\r\n", trim($file_content));
//print_r($file_content);
//print_r($file_arr);
$num = 1;//词语序号
$success = 0;//成功个数
$fail = 0;//失败个数
foreach ($file_arr as $word) {
	$property = 2;//词性 负面词 2  正面词 1 
	$sql="insert into property(word,w_type) values('$word',$property)";
	if(mysql_query($sql))
	{
		$sql = "insert into auto_work(aw_type,aw_time) values(3,$time)";
		mysql_query($sql);
		echo $num."添加成功!\n";
		$success++;	
	}else{
		echo $num."添加失败!请重试\n";
		$fail++;
	}
	$num++;
}





?>