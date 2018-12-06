<?PHP
$date_array=array();
for($i=$start_time;$i<=$end_time;$i=$i+86400){
   $date_array[]=$i; 
}
if(!is_dir("graph/".$user_id)){
  mkdir("graph/".$user_id);
}
$query="select uk_id from user_keywords where user_id=$user_id";
$res=mysql_query($query);
foreach($kids_array as $uk_id){
   $query="select k_id from user_keywords where uk_id =$uk_id";
   $res=mysql_query($query);
   $row=mysql_fetch_array($res);
   $k_id=$row['k_id'];
   $query="select keyword from keyword where k_id=$k_id";
   $res=mysql_query($query);
   $row=mysql_fetch_array($res);
   $keyword=$row['keyword'];
   $path="graph/".$user_id."/Data_MSline_".$uk_id.".xml";
   $xmlstr="";
   $xmlstr.="<?xml version='1.0' encoding='utf8'?>"."\n";
   $xmlstr.="<chart caption='".$keyword."--总体统计图' xAxisName='日期' yAxisName='文章总数' numberSuffix='篇' showValues='0'>"."\n";
   $xmlstr.="<categories>"."\n";
   foreach($date_array as $v){
       $xmlstr.="<category label='".date('Y-m-d',$v)."' />"."\n";
   }
   $xmlstr.="</categories>"."\n";
   for($i=1;$i<6;$i++){
       if($i==1){$class="新闻";}elseif($i==2){$class="论坛";}elseif($i==3){$class="博客";}elseif($i==4){$class="微博";}elseif($i==5){$class="视频";}
	   $xmlstr.="<dataset seriesName='$class'>"."\n";
	   foreach($date_array as $v){
	        $query1="select total_num from info_stats where article_class=$i and uk_id=$uk_id and stats_time=$v order by stats_time asc";
            $res1=mysql_query($query1);
            $row1=mysql_fetch_array($res1);
            $num=$row1['total_num'];
			if($num==""){$num=0;}
            $xmlstr.="<set value='$num' />"."\n";
        }
      
       $xmlstr.="</dataset>"."\n";
    } 
	$xmlstr.="</chart>"."\n"; 
    file_put_contents($path,$xmlstr);
}   
?>