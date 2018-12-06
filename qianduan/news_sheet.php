<?php
require_once('adminv/inc_dbconn.php');
require_once('Classes/PHPExcel.php');

function excel_data($str)
{
	while (strncmp($str, "=", 1) == 0)
	{
		$str = substr($str, 1);
	}
	return $str;
}
function filter($filter_words,$filter_place,$filter_type,$article_title,$article_summary){
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
					if($filter_type==2){
						if(!strstr($article_title,$filter_words)){
							 $flag=true;
						}
					}
				}
				if($filter_place==2){
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
	return $flag;
}
$user_id=$_COOKIE['user_id'];
$file_name=$_POST['file_name'];
$keyword=$_POST['w'];

$sql = "select article_id,article_url,article_title,article_pubtime,media from news_search where keyword = '".$keyword."' and user_id = ". $user_id . " order by article_pubtime desc";
//echo($sql);
$res=mysql_query($sql);
$array[0]['order']="序号";
$array[0]['media']="媒体名称";
$array[0]['time']="时间";
$array[0]['title']="标题";
$array[0]['link']="链接";
$i=1;
while($row=mysql_fetch_array($res))
{
		$array[$i]['order']=$row['article_id'];
		$array[$i]['media']=$row['media'];
		$array[$i]['title']=$row['article_title'];
		$array[$i]['link']=$row['article_url'];
		$array[$i]['time']=date('Y-m-d',$row['article_pubtime']);
		$i++;
}
/* @实例化 */
$obpe = new PHPExcel();
           
/* @func 设置文档基本属性 */
$obpe_pro = $obpe->getProperties();
$obpe_pro->setCreator('baw')//设置创建者
         ->setLastModifiedBy('2013/2/16 15:00')//设置时间
         ->setTitle('data')//设置标题
         ->setSubject('beizhu')//设置备注
         ->setDescription('miaoshu')//设置描述
         ->setKeywords('keyword')//设置关键字 | 标记
         ->setCategory('catagory');//设置类别
           
           
/* 设置宽度 */
//$obpe->getActiveSheet()->getColumnDimension()->setAutoSize(true);
//$obpe->getActiveSheet()->getColumnDimension('B')->setWidth(10);
           
//设置当前sheet索引,用于后续的内容操作
//一般用在对个Sheet的时候才需要显示调用
//缺省情况下,PHPExcel会自动创建第一个SHEET被设置SheetIndex=0
//设置SHEET
$obpe->setactivesheetindex(0);
$objActSheet = $obpe->getActiveSheet(); 
$objActSheet->setTitle('新闻');
$width_array=array('A'=>5,'B'=>10,'C'=>10,'D'=>40,'E'=>40);
foreach($width_array as $k => $v){
   $objActSheet->getColumnDimension($k)->setWidth($v);
}
$color_array=array("A1","B1","C1","D1","E1");
foreach($color_array as $v){
   $objStyle = $objActSheet->getStyle($v);
   $objFill = $objStyle->getFill();   
   $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);   
   $objFill->getStartColor()->setARGB('CCCCCCCC'); 
}
//写入多行数据
foreach($array as $k=>$v){
    $k = $k+1;
    /* @func 设置列 */
    $objActSheet->setcellvalue('A'.$k, excel_data($v['order']));
    $objActSheet->setcellvalue('B'.$k, excel_data($v['media']));
	$objActSheet->setcellvalue('C'.$k, excel_data($v['time']));
	$objActSheet->setcellvalue('D'.$k, excel_data($v['title']));
	$objActSheet->setcellvalue('E'.$k, excel_data($v['link']));
}
unset($array);

//写入类容
$obwrite = PHPExcel_IOFactory::createWriter($obpe, 'Excel5');
//保存文件
if(!is_dir($user_id)){
  mkdir($user_id);
}
$filename=$user_id."/search_".time().".xls"; 
//$download_filename=$user_id."/".iconv("utf8","gbk",$file_name).".xls";  
//$download_filename=iconv("utf8","gbk",$file_name).".xls";  
$download_filename=$file_name.".xls"; 
//$download_filename="中国.xls";
$obwrite->save($filename);
//下载文件
header('Content-Type: application/force-download; charset=utf8');
//header("Content-Disposition: attachment;filename='".basename($download_filename)."'");
header("Content-Disposition: attachment;filename='".$download_filename."'");
readfile($filename);
exit;
?>