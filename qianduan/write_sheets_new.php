<?php
require_once('adminv/inc_dbconn.php');
require_once('Classes/PHPExcel.php');
ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  

$json       = $_POST['data'];
$data       = json_decode($json, true);
//print_r($data);
$start_date	= $data['start_date'];
$end_date	= $data['end_date'];
$date       = $data['date'];
$type  		= $data['type'];
$uk_ids     = $data['uk_id'];
// $uk_ids = '169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,
//   189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,
//   214,215,216,217,218,219,234';
// $type = 1;
// $start_date = '2018-08-22 16:00';
// $end_date   = '2018-08-23 16:00';
// $date       = '2018-08-23';
// $file_name = iconv('utf-8', 'gb2312','集团日报');
$file_name	= iconv('utf-8', 'gb2312',urldecode($data['filename']));
//print_r($file_name);
$filter_place   = isset($data['filter_place'])? $data['filter_place'] : '';
$filter_type	= isset($data['filter_type'])? $data['filter_type'] : '';
$filter_words	= isset($data['filter_words'])? $data['filter_words'] : '';
//$start_date = '2018-08-13 16:00';
//$end_date   = '2018-08-14 16:00';
$start_time	= strtotime($start_date);
$end_time	= strtotime($end_date);
$property   = 2;//负面
//global $objActSheet;

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




//开始输出数据

$data[0][]  = array(
	'order' => '',  //'序号', 
	'source'=> '',  //'源发媒体',
	'media' => '',  //'转载媒体',
	'time'  => '',	//'发布时间',
	'title' => '',	//'稿件标题',
	'link'  => '', 	//'发布链接',
	'read_num' => '',  //'阅读量',
	'location' => '',  //'位置',
	//'brand'    => '',  //品牌，即关键字，暂时用不到
	);

$data_type  = array('news', 'video', 'blog', 'weibo', 'weixin', 'zhidao', 'app');
$type_name  = array('新闻', '视频', '博客', '微博', '微信', '知道', 'app');
//$x = ; //表的数目
for($y=0; $y<count($data_type); $y++) {
	$query = "select * from $data_type[$y]_key as a join $data_type[$y]_article as b on a.article_id=b.article_id where a.uk_id in (".$uk_ids.") and a.article_pubtime>=$start_time and a.article_pubtime<$end_time and a.status = 1 and a.article_property=$property ";
	$query .= " group by a.article_id order by b.article_title desc";
	//echo $query;
	$res    = mysql_query($query);
	//print_r($res);
	$i = 1;
	while($row=mysql_fetch_array($res)){
		//print_r($row);
		$article_id	= $row['article_id'];
		$id  	  	= $row['id'];
		//获取关键字
		$uk_id  	= $row['uk_id'];
		$query1	    = "select k_id from user_keywords where uk_id=$uk_id";
	    $res1 		= mysql_query($query1);
	    $row1		= mysql_fetch_array($res1);
	    $k_id	    = $row1['k_id'];
	    $query1	    = "select keyword from keyword where k_id=$k_id";
	    $res1		= mysql_query($query1);
	    $row1	    = mysql_fetch_array($res1);
	    $keyword    = $row1['keyword'];
	    //获取文章详情
		//$query2	    = "select * from $data_type[$y]_article where article_id=$article_id";
		///$res2	    = mysql_query($query2);
		//$row2	    = mysql_fetch_array($res2);
		$article_title      = $row['article_title'];
		$article_summary    = !empty($row['article_summary']) ? $row['article_summary'] : $row['article_content'];				 
		if(filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)){	
			$media		=	$row['media'];
			$data[$i]   =  array(
				'order' => $i,  
				'source'=> $media, 
				'media' => !empty($row['article_source'])?$row['article_source']:"/",
				'time'  => date('Y-m-d',$row['article_pubtime']),
				'title' => $row['article_title'],
				'link'  => $row['article_url'],
				'read_num' => '-', 
				'location' => '-',
				//'brand'    => $keyword,
			);
			if ($data_type[$y] == 'app') {
				$data[$i]['source'] = !empty($row2['article_source'])?$row2['article_source']:"/";
				$data[$i]['media']  = $media;
			}
			if($type == 1){
				$fu_data[$i] = array(
					'order' => $i,  
					'title' => $row['article_title'],
					'link'  => $row['article_url'],
					); 
			}
			$i++;
		}

	}
	$obpe->createSheet();
	$obpe->setactivesheetindex((int)$y);
	$objActSheet = $obpe->getActiveSheet(); 
	$objActSheet->setTitle($type_name[$y]);
	set_excel($objActSheet);//设置当前表的属性
	$line = 3;  //数据起始行
	for ($j=1; $j < $i; $j++) { 
		write_row($objActSheet, $line, $data[$j]);
		$line++;
	}
	unset($data);
}







$filename = iconv('utf-8', 'gb2312', '2018年舆情监测');
//写入类容
$obwrite = PHPExcel_IOFactory::createWriter($obpe, 'Excel5');
//保存文件
if(!is_dir('auto_files/'.$date)){
  mkdir('auto_files/'.$date);
}
$filename='auto_files/'.$date."/".$filename.$file_name.substr($date, 5).".xls"; 
if(!file_exists($filename)){
	$obwrite->save($filename);
}


if($type == 1){//需要导出word doc文件
	$property = 1; //正面
	$zheng_limit = 18;
	$data_type  = array('news', 'video', 'blog', 'weibo', 'weixin', 'zhidao', 'app');
	//$x = ; //表的数目
	for($y=0; $y<count($data_type); $y++) {
		$query = "select * from $data_type[$y]_key as a join $data_type[$y]_article as b on a.article_id=b.article_id where a.uk_id in (".$uk_ids.") and a.article_pubtime>=$start_time and a.article_pubtime<$end_time and a.status = 1 and a.article_property=$property ";
		$query .= " group by a.article_id order by a.article_pubtime desc limit $zheng_limit";
		//echo $query;
		$res    = mysql_query($query);
		$i = 1;
		while($row=mysql_fetch_array($res)){
			
			$article_title      = $row['article_title'];
			$article_summary    = !empty($row['article_summary']) ? $row['article_summary'] : $row['article_content'];				 
			if(filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)){	
				$media		=	$row['media'];
				$zheng_data[$i]   =  array(
					'order' => $i,  
					'title' => $row['article_title'],
					'link'  => $row['article_url'],
				);
				$i++;
			}

		}
		if ($i > $zheng_limit) {
			break;
		}else{
			$zheng_limit = $zheng_limit - $i +1;
		}
	}
	$uk_id = '220,221,222,223,224,225';//江淮竞品
	$jing_limit = 50;
	$data_type  = array('news', 'video', 'blog', 'weibo', 'weixin', 'zhidao', 'app');
	//$x = ; //表的数目
	for($y=0; $y<count($data_type); $y++) {
		$query = "select * from $data_type[$y]_key as a join $data_type[$y]_article as b on a.article_id=b.article_id where a.uk_id in (".$uk_ids.") and a.article_pubtime>=$start_time and a.article_pubtime<$end_time and a.status = 1 ";
		$query .= " group by a.article_id order by a.article_pubtime desc limit $jing_limit";
		$res    = mysql_query($query);
		$i = 1;
		while($row=mysql_fetch_array($res)){
			
			$article_title      = $row['article_title'];
			$article_summary    = !empty($row['article_summary']) ? $row['article_summary'] : $row['article_content'];				 
			if(filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)){	
				$media		=	$row['media'];
				$jing_data[$i]   =  array(
					'order' => $i,  
					'title' => $row['article_title'],
					'link'  => $row['article_url'],
				);
				$i++;
			}

		}
		if ($i > $jing_limit) {
			break;
		}else{
			$jing_limit = $jing_limit - $i +1;
		}
	}
	$filename2 = $date. iconv('utf-8', 'gb2312', '江淮汽车舆情监测');
	$html = '<h2>'.$date.'江淮汽车舆情监测'.'</h2>';
	foreach ($zheng_data as $key => $value) {
		$html .= '<p>'.$value['order'].'、'.$value['title'].'</p>';
		$html .= '<a>'.$value['link'].'</a>';
	}
	$html .= '<br/><h2>'.'二、相关负面'.'</h2>';
	foreach ($fu_data as $key => $value) {
		$html .= '<p>'.$value['order'].'、'.$value['title'].'</p>';
		$html .= '<a>'.$value['link'].'</a>';
	}
	$html .= '<br/><h2>'.'三、竞品新闻'.'</h2>';
	foreach ($jing_data as $key => $value) {
		$html .= '<p>'.$value['order'].'、'.$value['title'].'</p>';
		$html .= '<a>'.$value['link'].'</a>';
	}

  
	 $word = new word(); 
	 $word->start(); 
	 //$html = "aaa".$i; 
	 $wordname = 'auto_files/'.$date."/".$filename2.'.doc'; 
	 echo $html; 
	 $word->save($wordname); 
	 ob_flush();//每次执行前刷新缓存 
	 flush(); 


}




//写一行
function write_row($objActSheet, $row, $data){
	global $objActSheet;
	$arr  = array('A','B','C','D','E','F','G','H','I','J','K',
		'L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	$len = count($data);
	$i = 0;
	foreach ($data as $key => $value) {
		if ($i == $len)
			break;
		if (empty($value)) {
			$i++;
			continue;
		}
		$objActSheet->setcellvalue($arr[$i].$row, trim(excel_data($value)));
		$i++;
	}
	//print_r($data);
}
function set_excel($objActSheet){
	$excel_title  = array('序号', '源发媒体','转载媒体','发布日期','稿件标题','发布链接','阅读量','位置', '责任单位','舆情处理沟通情况', '媒体影响力评估');
	$objActSheet->getDefaultRowDimension()->setRowHeight(50);
	$objActSheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_style_Alignment::VERTICAL_CENTER);
	$objActSheet->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objActSheet->getStyle('A1:K2')->getFont()->setBold(true);//标题用粗体
	$objActSheet->getStyle('E:F')->getAlignment()->setWrapText(true);
	//$objActSheet->getStyle('A1:K2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	// $objActSheet->getStyle('A1:K2')->getFill()->getStartColor()->setARGB('EEEEEEEE');//背景浅灰
	//$objActSheet->getstyle('A1:K2')->getBorders()->getTop()->setBorderstyle(PHPExcel_style_Border::BORDER_THIN);
	$objActSheet->getstyle('A1:K2')->getBorders()->getAllBorders()->setBorderstyle(PHPExcel_style_Border::BORDER_THIN);
	$width_array=array('A'=>6,'B'=>20,'C'=>20,'D'=>15,'E'=>40,'F'=>40,'G'=>8,'H'=>10,'I'=>15,'J'=>50,'K'=>50);//列宽度
	foreach($width_array as $k => $v){
	   $objActSheet->getColumnDimension($k)->setWidth($v);
	}
	//输出标题
	$objActSheet->mergeCells('B1:C1');//合并单元格
	$objActSheet->mergeCells('G1:H1');
	$objActSheet->mergeCells('A1:A2');
	$objActSheet->mergeCells('D1:D2');
	$objActSheet->mergeCells('E1:E2');
	$objActSheet->mergeCells('F1:F2');
	$objActSheet->mergeCells('I1:I2');
	$objActSheet->mergeCells('J1:J2');
	$objActSheet->mergeCells('K1:K2');
	write_row($objActSheet, 1, array('序号', '发布媒体','','发布日期','稿件标题','发布链接','关注度','', '责任单位','舆情处理沟通情况', '媒体影响力评估'));
	write_row($objActSheet, 2, $excel_title);
}

//过滤数据
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
function excel_data($str)
{
	while (strncmp($str, "=", 1) == 0)
	{
		$str = substr($str, 1);
	}
	return $str;
}

class word
{ 
	function start()
	{
		ob_start();
		echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
		xmlns:w="urn:schemas-microsoft-com:office:word"
		xmlns="http://www.w3.org/TR/REC-html40">';
	}
	function save($path)
	{
		  
		echo "</html>";
		$data = ob_get_contents();
		ob_end_clean();
		  
		$this->wirtefile ($path,$data);
	}
  
	function wirtefile ($fn,$data)
	{
		$fp=fopen($fn,"wb");
		fwrite($fp,$data);
		fclose($fp);
	}
}




?>