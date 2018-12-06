<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once('adminv/inc_dbconn.php');
require_once('Classes/PHPExcel.php');
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load("templates/template3.xls");
$uk_ids=$_POST['uk_ids'];
$user_id=$_POST['user_id'];
$file_name=$_POST['file_name'];
$start_date=$_POST['start_date'];
$end_date=$_POST['end_date'];
$start_time=strtotime(date($start_date." 00:00:00"))-1;
$end_time=strtotime(date($end_date." 00:00:00"))+1;
$query="select sum(total_num) as total_num,sum(positive_num) as positive_num,sum(negative_num) as negative_num,sum(neutral_num) as neutral_num,uk_id,isV,media from weibo_stats where uk_id in (".$uk_ids.") and stats_time>$start_time and stats_time<$end_time and isV=1 group by uk_id,media order by uk_id asc";
$res=mysql_query($query);
$data=array();
while($rows=mysql_fetch_array($res)){
		$uk_id=$rows['uk_id'];
	    $query1="select k_id from user_keywords where uk_id=$uk_id";
		$res1=mysql_query($query1);
		$row1=mysql_fetch_array($res1);
		$k_id=$row1['k_id'];
		$query1="select keyword from keyword where k_id=$k_id";
		$res1=mysql_query($query1);
		$row1=mysql_fetch_array($res1);
		$keyword=$row1['keyword'];
		$data[]=array('keyword'=>$keyword,'media'=>$rows['media'],'total_num'=>$rows['total_num'],'positive_num'=>$rows['positive_num'],'neutral_num'=>$rows['neutral_num'],'negative_num'=>$rows['negative_num']);
		
}			 
$baseRow =3;
foreach($data as $r => $dataRow) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $dataRow['keyword'])
	                              ->setCellValue('B'.$row, $dataRow['media'])
	                              ->setCellValue('C'.$row, $dataRow['total_num'])
	                              ->setCellValue('D'.$row, $dataRow['positive_num'])
								  ->setCellValue('E'.$row, $dataRow['neutral_num'])
								  ->setCellValue('F'.$row, $dataRow['negative_num']);
}
$query="select sum(total_num) as total_num,sum(positive_num) as positive_num,sum(negative_num) as negative_num,sum(neutral_num) as neutral_num,uk_id,isV,media from weibo_stats where uk_id in (".$uk_ids.") and stats_time>$start_time and stats_time<$end_time and isV=0 group by uk_id,media order by uk_id asc";
$res=mysql_query($query);
$data=array();
while($rows=mysql_fetch_array($res)){
		$uk_id=$rows['uk_id'];
	    $query1="select k_id from user_keywords where uk_id=$uk_id";
		$res1=mysql_query($query1);
		$row1=mysql_fetch_array($res1);
		$k_id=$row1['k_id'];
		$query1="select keyword from keyword where k_id=$k_id";
		$res1=mysql_query($query1);
		$row1=mysql_fetch_array($res1);
		$keyword=$row1['keyword'];
		$data[]=array('keyword'=>$keyword,'media'=>$rows['media'],'total_num'=>$rows['total_num'],'positive_num'=>$rows['positive_num'],'neutral_num'=>$rows['neutral_num'],'negative_num'=>$rows['negative_num']);
		
}			 
$baseRow =3;
foreach($data as $r => $dataRow) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $dataRow['media'])
	                              ->setCellValue('H'.$row, $dataRow['total_num'])
	                              ->setCellValue('I'.$row, $dataRow['positive_num'])
								  ->setCellValue('J'.$row, $dataRow['neutral_num'])
								  ->setCellValue('K'.$row, $dataRow['negative_num']);
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//文件信息入库
$time=time();
$uk_ids=str_replace('0,','',$uk_ids);
$insert="insert into statsFile_list(file_name,export_time,start_time,end_time,user_id,uk_id) values('$file_name',$time,$start_time,$end_time,$user_id,'$uk_ids')";
mysql_query($insert);
$id=mysql_insert_id();     
//保存文件
if(!is_dir($user_id)){
  mkdir($user_id);
}
$filename=$user_id."/".iconv('utf8','gbk',$file_name).".xls";  
$objWriter->save($filename);
//下载文件
header('Content-Type: application/force-download');
header("Content-Disposition: attachment;filename='".basename($filename)."'");
readfile($filename);
exit;
?>

