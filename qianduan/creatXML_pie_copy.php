<?PHP
require_once('adminv/inc_dbconn.php');
$user_id=1;
$query="select uk_id from user_keywords where user_id=$user_id";
$res=mysql_query($query);
while($row=mysql_fetch_array($res)){
   $uk_id=$row['uk_id'];
   $path="graph/1/Data_pie_".$uk_id.".xml";
   $xmlstr="";
   $xmlstr.="<?xml version='1.0' encoding='utf8'?>"."\n";
   $xmlstr.="<chart caption='正面文章分类统计' xAxisName='日期' yAxisName='文章数' numberSuffix='篇'>"."\n";
   $query1="select positive_num from info_stats where stats_time=1395676800 and uk_id=$uk_id order by article_class asc";
   $res1=mysql_query($query1);
   $class=array("新闻","论坛","博客","微博","视频");
   $i=0;
   while($row1=mysql_fetch_array($res1)){
        $num=$row1['positive_num'];
	    if($num>0){
             $xmlstr.="<set label='$class[$i]' value='$num' />"."\n";
	    }
	    $i++;
   }
   $xmlstr.="</chart>";
   file_put_contents($path,$xmlstr);
}   
?>