<?PHP
require_once('adminv/inc_dbconn.php');
$user_id=1;
$start_time=1395504000;
$start=1395503999;
$end_time=1395763200;
$end=1395763201;
$date_array=array();
for($i=$start_time;$i<=$end_time;$i=$i+86400){
   $date_array[]=$i; 
}
$query="select uk_id from user_keywords where user_id=$user_id";
$res=mysql_query($query);
while($row=mysql_fetch_array($res)){
   $uk_id=$row['uk_id'];
   $path="graph/1/Data_MSline_".$uk_id.".xml";
   $xmlstr="";
   $xmlstr.="<?xml version='1.0' encoding='utf8'?>"."\n";
   $xmlstr.="<chart caption='总体统计' xAxisName='日期' yAxisName='文章总数' numberSuffix='篇'>"."\n";
   $xmlstr.="<categories>"."\n";
   foreach($date_array as $v){
       $xmlstr.="<category label='".date('Y-m-d',$v)."' />"."\n";
   }
   $xmlstr.="</categories>"."\n";
   for($i=1;$i<6;$i++){
       if($i==1){$class="新闻";}elseif($i==2){$class="论坛";}elseif($i==3){$class="博客";}elseif($i==4){$class="微博";}elseif($i==5){$class="视频";}
	   $xmlstr.="<dataset seriesName='$class'>"."\n";
       $query1="select total_num,article_class,stats_date from info_stats where article_class=$i and uk_id=$uk_id and stats_time>$start and stats_time<$end order by stats_time asc";
       $res1=mysql_query($query1);
       while($row1=mysql_fetch_array($res1)){
           $num=$row1['total_num'];
                $xmlstr.="<set value='$num' />"."\n";
       }
       $xmlstr.="</dataset>"."\n";
    } 
	$xmlstr.="</chart>"."\n"; 
    file_put_contents($path,$xmlstr);
}   
?>