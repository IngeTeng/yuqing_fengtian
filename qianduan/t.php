<?php
require_once('Classes/PHPExcel.php');

/* @实例化 */
$obpe = new PHPExcel();
$time = time();
$time_str = date('Y/m/d H:i',$time);
/* @func 设置文档基本属性 */
$obpe_pro = $obpe->getProperties();
$obpe_pro->setCreator('WiipuXian')//设置创建者
         ->setLastModifiedBy($time_str)//设置时间
         ->setTitle('data')//设置标题
         ->setSubject('beizhu')//设置备注
         ->setDescription('miaoshu')//设置描述
         ->setKeywords('keyword')//设置关键字 | 标记
         ->setCategory('catagory');//设置类别
$obpe->getDefaultStyle()->getFont()->setName('宋体');          
$obpe->getDefaultStyle()->getFont()->setSize(10); 

$obpe->setactivesheetindex(0);
$objActSheet = $obpe->getActiveSheet(); 
$objActSheet->getDefaultRowDimension()->setRowHeight(15);

$objActSheet->setTitle('新闻');
$width_array=array('A'=>5,'B'=>10,'C'=>10,'D'=>10,'E'=>11,'F'=>50,'G'=>11,'H'=>67,'I'=>10,'J'=>8,'K'=>30,'L'=>15,'M'=>15);
foreach($width_array as $k => $v){
   $objActSheet->getColumnDimension($k)->setWidth($v);
}
$color_array=array("A1","B1","C1","D1","E1","F1","G1","H1","I1","J1","K1","L1","M1");
foreach($color_array as $v){
   $objStyle = $objActSheet->getStyle($v);
   $objFill = $objStyle->getFill();   
   $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);   
   $objFill->getStartColor()->setARGB('CCCCCCCC'); 
}

$obwrite = PHPExcel_IOFactory::createWriter($obpe, 'Excel5');
$download_filename="123.xls"; 
$filename="123.xls"; 
$obwrite->save($filename);

//下载文件
header('Content-Type: application/force-download; charset=utf8');
//header("Content-Disposition: attachment;filename='".basename($download_filename)."'");
header("Content-Disposition: attachment;filename='".$download_filename."'");
readfile($filename);
exit;
?>