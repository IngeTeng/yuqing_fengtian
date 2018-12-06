<?php
	require_once('adminv/inc_dbconn.php');
	require_once('Classes/PHPExcel.php');
    $filename = "/home/automoni/yuqing/tmp/m2.xls";
    $objReader = PHPExcel_IOFactory::createReader('Excel5');
    $objPHPExcel = $objReader->load($filename); 
    $sheet = $objPHPExcel->getSheet(1); 
    $highestRow = $sheet->getHighestRow(); 
    $mlist = array();
    for ($i = 2; $i <= $highestRow; $i++)
    {
    	 $url = $sheet->getCell("C$i")->getValue();//读取单元格
    	 $type = $sheet->getCell("D$i")->getValue();
    	 if ($url == "" || $type == "")
    	 {
    	 	break;
    	 }
    	 $mlist[] = array("url"=>$url, "type"=>$type);
    }
 print_r($mlist);
    $sql = "select * from media_list";
    $res = mysql_query($sql);
    while ($row = mysql_fetch_array($res))
    {
    	$id = $row["m_id"];
    	$domain = $row["domain"];
  		if ($domain == "")
  		{
  			echo($domain."\n");
  			continue;
  		}
  		$grade = 4;
    	for ($i = 0; $i < count($mlist); $i++)
    	{
    		if (strstr($mlist[$i]["url"],$domain))
    		{
    			if ($mlist[$i]["type"] == 'A')
    			{
    				$grade = 1;
    			}
    			else if ($mlist[$i]["type"] == 'B')
    			{
    				$grade = 2;
    			}
    			else if ($mlist[$i]["type"] == 'C')
    			{
    				$grade = 3;
    			}
    			else if ($mlist[$i]["type"] == 'D')
    			{
    				$grade = 4;
    			}
    			break;
    		}
    	}
    	if ($grade != 4)
    	{
    		$sql = "update media_list set grade = $grade where m_id = $id";
    echo($sql."\n");
    		mysql_query($sql);
    	}
    }
    
    

?>