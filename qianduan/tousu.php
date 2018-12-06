<?php

	require_once('adminv/inc_function.php');
	require_once('Classes/PHPExcel.php');

	header("content-type:text/html;charset=utf-8");
	session_start();
	ob_start();
	
	//配置数据库连接参数
	define('WIIDBHOST','localhost');
	define('WIIDBUSER','root');
	define('WIIDBPASS','WiiYuqing');
	define('WIIDBNAME','tousu_tmp');
	
	$db_connect=mysql_connect(WIIDBHOST,WIIDBUSER,WIIDBPASS);
	if (!$db_connect){
		die ('数据库连接失败');
	}
	mysql_select_db(WIIDBNAME, $db_connect) or die ("没有找到数据库。");
	mysql_query("set names utf8;");

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

$sql ="select * from tousu group by url order by pubtime desc";
$res=mysql_query($query);
$array[0]['order']="序号";
$array[0]['media']="媒体名称";
$array[0]['chexi']="投诉车系";
$array[0]['chexing']="投诉车型";
$array[0]['title']="标题";
$array[0]['url']="链接";
$array[0]['time']="时间";
$array[0]['content']="内容";
$i=1;
$res = mysql_query($sql);
while($row=mysql_fetch_array($res))
{
	$array[$i]['order'] = $i;
	$array[$i]['media']= $row["media"];
	$array[$i]['chexi']=$row["chexi"];
	$array[$i]['chexing']=$row["chexing"];
	$array[$i]['title']=$row["title"];
	$array[$i]['url']=$row["url"];
	$array[$i]['time']=date("Y-m-d", $row["pubtime"]);
	$array[$i]['content']=$row["content"];
	$i++;
}
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

$objActSheet->setTitle('投诉');
$width_array=array('A'=>5,'B'=>10,'C'=>10,'D'=>20,'E'=>40,'F'=>40,'G'=>10,'H'=>40);
foreach($width_array as $k => $v){
   $objActSheet->getColumnDimension($k)->setWidth($v);
}
$color_array=array("A1","B1","C1","D1","E1","F1","G1","H1");
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
    $objActSheet->setcellvalue('C'.$k, excel_data($v['chexi']));
    $objActSheet->setcellvalue('D'.$k, excel_data($v['chexing']));
	$objActSheet->setcellvalue('E'.$k, excel_data($v['title']));
	$objActSheet->setcellvalue('F'.$k, excel_data($v['url']));
    $objActSheet->setcellvalue('G'.$k, excel_data($v['time']));
	$objActSheet->setcellvalue('H'.$k, excel_data($v['content']));
}
unset($array);
 
//写入类容
$obwrite = PHPExcel_IOFactory::createWriter($obpe, 'Excel5');
//保存文件

$filename="tmp/tousu.xls"; 
$download_filename="投诉.xls"; 
$obwrite->save($filename);
//下载文件
header('Content-Type: application/force-download; charset=utf8');
//header("Content-Disposition: attachment;filename='".basename($download_filename)."'");
header("Content-Disposition: attachment;filename='".$download_filename."'");
readfile($filename);
exit;

?>