<?PHP
foreach($kids_array as $uk_id){
   $uk_id=$uk_id;
   $query="select k_id from user_keywords where uk_id =$uk_id";
   $res=mysql_query($query);
   $row=mysql_fetch_array($res);
   $k_id=$row['k_id'];
   $query="select keyword from keyword where k_id=$k_id";
   $res=mysql_query($query);
   $row=mysql_fetch_array($res);
   $keyword=$row['keyword'];
   $path="graph/".$user_id."/Data_pie_".$uk_id."_total.xml";
   $xmlstr="";
   $xmlstr.="<?xml version='1.0' encoding='utf8'?>"."\n";
   $xmlstr.="<chart caption='".$keyword."--分类统计图' xAxisName='日期' yAxisName='文章数' numberSuffix='篇'>"."\n";
   $query1="select sum(total_num) as total_num from info_stats where stats_time>$start and stats_time<$end and uk_id=$uk_id group by uk_id,article_class order by article_class asc";
   $res1=mysql_query($query1);
   $class=array("新闻","论坛","博客","微博","视频");
   $i=0;
   while($row1=mysql_fetch_array($res1)){
        $num=$row1['total_num'];
	    if($num>0){
             $xmlstr.="<set label='$class[$i]' value='$num' />"."\n";
	    }
	    $i++;
   }
   $xmlstr.="</chart>";
   file_put_contents($path,$xmlstr);
}   
?>