<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
function excel_data($str)
{
	while (strncmp($str, "=", 1) == 0)
	{
		$str = substr($str, 1);
	}
	return $str;
}
require_once('adminv/inc_dbconn.php');
require_once('Classes/PHPExcel.php');
$objReader = PHPExcel_IOFactory::createReader('Excel5');
$objPHPExcel = $objReader->load("templates/template4.xls");
$a_ids=$_POST['a_ids'];
$user_id=$_POST['user_id'];
$file_name=$_POST['file_name'];
$query="select article_title,article_url,article_summary,article_pubtime  from news_article where article_id in (".$a_ids.") order by article_pubtime desc";
$res=mysql_query($query);
$data=array();
while($rows=mysql_fetch_array($res)){
		$data[]=array('article_title'=>excel_data(trim($rows['article_title'])),'article_url'=>$rows['article_url'],'article_summary'=>excel_data(trim($rows['article_summary'])),'article_pubtime'=>date('Y-m-d H:i:s',$rows['article_pubtime']));
		
}			 
$baseRow =3;
foreach($data as $r => $dataRow) {
	$row = $baseRow + $r;
	$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $dataRow['article_title'])
	                              ->setCellValue('B'.$row, $dataRow['article_url'])
	                              ->setCellValue('C'.$row, $dataRow['article_summary'])
	                              ->setCellValue('D'.$row, $dataRow['article_pubtime']);

}
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
/*
//文件信息入库
$time=time();
$uk_ids=str_replace('0,','',$uk_ids);
$insert="insert into statsFile_list(file_name,export_time,start_time,end_time,user_id,uk_id) values('$file_name',$time,$start_time,$end_time,$user_id,'$uk_ids')";
mysql_query($insert);
$id=mysql_insert_id();    
*/ 
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

