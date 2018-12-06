<?PHP
require_once('adminv/inc_dbconn.php');
$user_id=1;
$query="select uk_id from user_keywords where user_id=$user_id";
$res=mysql_query($query);
while($row=mysql_fetch_array($res)){
   $uk_id=$row['uk_id'];
   $path="graph/1/Data_column_".$uk_id.".xml";
   $xmlstr="";
   $xmlstr.="<?xml version='1.0' encoding='utf8'?>"."\n";
   $xmlstr.="<chart caption='正面文章分类统计' xAxisName='日期' yAxisName='文章数' numberSuffix='篇'>"."\n";
   $query1="select sum(total_num) as total_num,stats_date from info_stats where stats_time>1395503999 and stats_time<1395763201 and uk_id=$uk_id group by uk_id,stats_time order by stats_time asc";
   $res1=mysql_query($query1);
   while($row1=mysql_fetch_array($res1)){
        $num=$row1['total_num'];
		$stats_date=$row1['stats_date'];
	    if($num>0){
             $xmlstr.="<set label='$stats_date' value='$num' />"."\n";
	    }
   }
   $xmlstr.="</chart>";
   file_put_contents($path,$xmlstr);
}   
?>