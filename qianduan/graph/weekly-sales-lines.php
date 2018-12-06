<html>
  <head>        
    <title>My First chart using FusionCharts XT</title>         
    <script type="text/javascript" src="FusionCharts/FusionCharts.js">
    </script>
  </head>   
  <body>     
    <div id="chartContainer1">FusionCharts XT will load here!</div> 
	<div id="chartContainer2">FusionCharts XT will load here!</div> 
	<div id="chartContainer3">FusionCharts XT will load here!</div>          
    <script type="text/javascript"> 
	<?php
	   for($i=1;$i<11;$i++){
	?>     
          var myChart<?php echo $i;?> = new FusionCharts( "FusionCharts/Line.swf", 
          "myChartId<?php echo $i;?>", "600", "450", "0" );
          myChart<?php echo $i;?>.setXMLUrl("1/Data_column_<?php echo $i;?>.xml");
          myChart<?php echo $i;?>.render("chartContainer<?php echo $i;?>");
	<?php
	    }
    ?>
    </script>      
  </body> 
</html>