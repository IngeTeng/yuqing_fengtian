<?php
  include_once('check_user.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="Author" content="微普科技http://www.wiipu.com"/>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="styles/global.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="styles/style.css" type="text/css"/>
  <link rel="stylesheet" type="text/css" href="styles/jquery-ui.css" />
  <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui.js"></script>
  <script type="text/javascript" src="js/jquery-ui-slide.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
  <title> 微普舆情监控系统</title>
</head>
<body>
<?php
require_once('header.php');
$a_type_list = array();
$a_type_list[] = array("id"=>1,"name"=>"新闻");
$a_type_list[] = array("id"=>2,"name"=>"论坛");
$a_type_list[] = array("id"=>4,"name"=>"微博");
$a_type_list[] = array("id"=>6,"name"=>"微信");

$keyword = addslashes($_GET["w"]);
$a_type = urlencode($_GET["a_type"]);
if ($a_type == "")
{
	$a_type = 1;
}


$sql = "select uk_id, keyword from keyword,user_keywords where keyword.k_id = user_keywords.k_id and user_keywords.user_id = $user_id order by uk_id asc";
//echo($sql);
$res = mysql_query($sql);
$klist = array();
$klist[] = array("uk_id"=>0, "keyword"=>"未选择");
while ($row = mysql_fetch_array($res))
{
	$klist[] = array("uk_id"=> $row["uk_id"], "keyword"=>$row["keyword"]);
}
mysql_free_result($res);
?>
<div class="Navigation">
	<ul>
    	<li><a href="#">首页</a></li>
        <li><a href="#">新闻搜索</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">
	<form action="" method="get" name="queryForm">
				<div class="output">
				    <label for="filter_place">关键字：</label>
					<input type="text" id="keyword" name="w" value="<?php echo $keyword ?>" style="width:500px"/>
					<select name="a_type" id="search_a_type">
					<?php
						for ($i = 0; $i < count($a_type_list); $i++)
						{
							$item = $a_type_list[$i];
							$selected = "";
							if ($item["id"] == $a_type)
							{
								$selected = "selected";
							}
					?>
						<option value="<?php echo $item["id"] ?>" <?php echo $selected ?>><?php echo $item["name"] ?></option>
					<?php
						}
					?>
					</select>
					<input type="button" id="query" onclick="search()" value=" 搜索 " />
				</div>

		</form>
		<form action="news_sheet.php" method="post" name="queryForm">
				<input type="hidden" id="" name="w" value="<?php echo $keyword ?>"/>

				<div class="output">
				    <label for="filter_place">文件名：</label>
					<input type="text" id="" name="file_name" value="<?php echo $keyword ?>导出文件" style="width:500px"/>
					<input type="submit" id="query" value=" 导出 " />
				</div>

		</form>
		<form action="news_import.php" method="post" name="listForm" id="listForm">
				<input type="hidden" name="a_type" id="a_type" value="<?php echo $a_type ?>">
				<div class="output">
				    <label for="filter_place">导入：</label>
					请先将需要导入的信息选择对应关键字和文章调性，然后点击右侧导入按钮，一次性将搜索结果导入到系统中。
					<input type="submit" id="import" value=" 导入 " />
				</div>
				  <table border="0" cellspacing="0" cellpadding="0" width="900">
				     <thead class="Header Center">
				         <tr>
						      <td width="4%">选择</td><td width="45%">标题</td><td width="8%">媒体/作者</td><td width="8%">搜索来源</td><td width="10%">发表时间</td><td width="10%">关键字</td><td width="10%">文章调性</td><td width="4%">操作</td>
				         </tr>
					 </thead>
					 <tbody id="show_list">
					 </tbody>
				  </table>
				   <input type="hidden" value="0" name="search_num" id="search_num">
		</form>
				  	 <div class="loading" id="loading"><img src="images/loading.gif"></div>
				</div>
                <div class="Content_bottom"><img src="images/content_bottom.png" /></div>
				<div id="bottom_block" style="width:600px">
				        <input type="checkbox" id="select_all" />全选&nbsp;
						<input type="checkbox" id="remove" />取消&nbsp;
						<input type="checkbox" id="antiAll" />反选&nbsp;&nbsp;	
						<select id="uk_id_All">
							 <?php
							 		 for ($j = 0; $j < count($klist); $j++)
							 		 {
							 ?>
							 	<option value="<?php echo $klist[$j]["uk_id"]?>"><?php echo $klist[$j]["keyword"]?></option>
							 <?php
							 		 }
							 ?>
						</select>&nbsp;&nbsp;
						<input type="button" value="正面" id="positive" />&nbsp;&nbsp;
						<input type="button" value="中性" id="objective" />&nbsp;&nbsp;
						<input type="button" value="负面" id="negative" />&nbsp;&nbsp;
						<input type="button" value="不良" id="badnews" />&nbsp;&nbsp;
						<input type="button" value="删除所选" id="delete" />&nbsp;&nbsp;
						<input type="button" value="导入" id="importB" />&nbsp;&nbsp;
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<?php include_once('footer.php');?>	
		<div class="clear"></div>
	</div>
 </div>


</body>
</html>

<script>
$('#importB').click(function()
{
    $("#listForm").submit();
})

$('#select_all').click(function()
{
    $("#remove,#antiAll").attr("checked",false);
    if($(this).attr("checked")){
          $("input[name='checkbox']").attr("checked",true);
	}else{
	      $("input[name='checkbox']").attr("checked",false);
	}
})

$('#remove').click(function(){
     $(this).attr("checked",true);
     $("#select_all,#antiAll").attr("checked",false);
     $("input[name='checkbox']").attr("checked",false);
})

$("#antiAll").click(function(){
     $("#select_all,#remove").attr("checked",false);
     $("input[name='checkbox']").each(function(){
           $(this).attr("checked",!this.checked);  
	 })            
});

$('#delete').click(function(){
     $("input[name='checkbox']").each(function()
     {
          if($(this).attr("checked"))
          {
          	
			  var tr=$(this).parent().parent();
	       	  var a_id=tr.find('.id').val();
			  $.ajax({
                   url: "del_search_ajax.php",  
                   type: "POST",
                   data:{id:a_id},
                   dataType: "json",
                   error: function(){},  
                   success: function(data){
					      tr.remove();
			        } 					   
              });
            
		  }  
	 })  
});

$('#positive,#objective,#negative,#badnews').click(function(){
     var property;
	 var text;
     if($(this).attr("id")=="positive"){property=1;text="正";}
	 else if($(this).attr("id")=="objective"){property=0;text="中";}
	 else if($(this).attr("id")=="negative"){property=2;text="负";}
     else if($(this).attr("id")=="badnews"){property=3;text="不良";}
     $("input[name='checkbox']").each(function(){
          if($(this).attr("checked")){
			  var tr=$(this).parent().parent();
	          var pselect =tr.find('.property');
	          pselect.val(property);
	          return;
		  }  
	 })  
});

$("#uk_id_All").change(function(){
	var uk_id = $(this).val();
	$("input[name='checkbox']").each(function(){
          if($(this).attr("checked"))
          {
			  var tr=$(this).parent().parent();
	          var ukobj =tr.find('.uk_id');
	          ukobj.val(uk_id);
	          return;
		  }  
	 }) 
});


function delete_result(i)
{
    var tr= $("#tr_"+i);
	var a_id=tr.find('.id').val();
    $.ajax({
                   url: "del_search_ajax.php",  
                   type: "POST",
                   data:{id:a_id},
                   dataType: "json",
                   error: function(){},  
                   success: function(data){
					        tr.remove(); 
			         } 					   
    });
};

var g_baidunews_flag = 0;
var g_360news_flag = 0;
var g_sogounews_flag = 0;

var g_baidubbs_flag = 0;
var g_sogoubbs_flag = 0;

var g_weixin_flag = 0;

var g_weibo_flag = 0;
var g_tecent_flag = 0;

var g_i = 0;

function append_result(data)
{
	var html = "";
	for (var i = 0; i < data.length; i++)
	{
		var node = data[i];
		var tr = '<tr id="tr_'+g_i+'">';
		
		tr += '<input type="hidden" name="id_'+ g_i +'" class="id" value="'+node.id+'" />';
		tr += '<td><input type="checkbox" class="checkbox" name="checkbox" /></td>';
		tr += '<td style="text-align:left;">';
		tr += '<a href="' + node.url +'" target="_blank"  title="点击查看原文">';
		tr += node.title;
		tr += '</a>';
		tr += '</td>';
		tr += '<td>'+ node.media +'</td>';
		tr += '<td>'+ node.search_engine +'</td>';
		tr += '<td><span class="time">'+node.time_str+'</span></td>';
		tr += '<td>';
		tr += '<select name="uk_id_'+g_i+'" class="uk_id">';
							<?php
							 		 for ($j = 0; $j < count($klist); $j++)
							 		 {
							 ?>
		tr += '<option value="<?php echo $klist[$j]["uk_id"]?>"><?php echo $klist[$j]["keyword"]?></option>';
							 <?php
							 		 }
							 ?>
		tr += '</select>';
		tr += '</td>';
		tr += '<td>';
		tr += '<select name="property_'+g_i+'" class="property">';
		tr += '<option value="0">中</option>';
		tr += '<option value="1">正</option>';
		tr += '<option value="2">负</option>';
		tr += '<option value="3">不良</option>';
		tr += '</select>';
		tr += '</td>';
		tr += '<td><a href="javascript:delete_result('+g_i+');" class="del"><img src="adminv/images/dot_del.gif" width="9" height="9" alt="删除" /></a></td>';
		tr += '</tr>';
		
		g_i++;
		html += tr;
	}
	//alert(html);
	var show = $("#show_list").html();
	show += html;
	$("#show_list").html(show);
	$("#search_num").val(g_i);
}



function loading_360news()
{
	var w = $("#keyword").val();
	if (w == "")
	{
		g_360news_flag = 0;
		return;
	}
	
	$.ajax({
    		url: "360news_search_ajax.php",  
            type: "POST",
            data:{w:w},
            dataType: "json",
            error: function()
            {
            	g_360news_flag = 0;
            	hide_loading();
            },  
            success: function(data)
            {
				append_result(data);
				g_360news_flag = 0;
				hide_loading();
			} 					   
    });
}
function loading_sogounews()
{
	var w = $("#keyword").val();
	if (w == "")
	{
		g_sogounews_flag = 0;
		return;
	}
	
	$.ajax({
    		url: "sogounews_search_ajax.php",  
            type: "POST",
            data:{w:w},
            dataType: "json",
            error: function()
            {
            	g_sogounews_flag = 0;
            	loading_360news();
            },  
            success: function(data)
            {
            	
				append_result(data);
				g_sogounews_flag = 0;
				loading_360news();
			} 					   
    });
}


function loading_baidunews()
{
	var w = $("#keyword").val();
	if (w == "")
	{
		g_baidunews_flag = 0;
		return;
	}
	$.ajax({
    		url: "baidunews_search_ajax.php",  
            type: "POST",
            data:{w:w},
            dataType: "json",
            error: function()
            {
            	g_baidunews_flag = 0;
            	loading_sogounews();
            },  
            success: function(data)
            {
				append_result(data);
				g_baidunews_flag = 0;
				loading_sogounews();
			} 					   
    });
}

function loading_sogoubbs()
{
	var w = $("#keyword").val();
	if (w == "")
	{
		g_sogoubbs_flag = 0;
		return;
	}
	
	$.ajax({
    		url: "sogoubbs_search_ajax.php",  
            type: "POST",
            data:{w:w},
            dataType: "json",
            error: function()
            {
            	g_sogoubbs_flag = 0;
            	hide_loading();
            },  
            success: function(data)
            {
				append_result(data);
				g_sogoubbs_flag = 0;
				hide_loading();
			} 					   
    });
}

function loading_baidubbs()
{
	var w = $("#keyword").val();
	if (w == "")
	{
		g_baidubbs_flag = 0;
		return;
	}
	$.ajax({
    		url: "baidubbs_search_ajax.php",  
            type: "POST",
            data:{w:w},
            dataType: "json",
            error: function()
            {
            	g_baidubbs_flag = 0;
            	loading_sogoubbs();
            },  
            success: function(data)
            {
				append_result(data);
				g_baidubbs_flag = 0;
				//alert(g_baidbbs_flag);
				loading_sogoubbs();
			} 					   
    });
}

function loading_tencent()
{
	var w = $("#keyword").val();
	if (w == "")
	{
		g_tencent_flag = 0;
		return;
	}
	
	$.ajax({
    		url: "tencent_search_ajax.php",  
            type: "POST",
            data:{w:w},
            dataType: "json",
            error: function()
            {
            	//alert("error");
            	g_tencent_flag = 0;
            	hide_loading();
            },  
            success: function(data)
            {
            	//alert(data);
				append_result(data);
				g_tencent_flag = 0;
				hide_loading();
			} 					   
    });
}

function loading_weibo()
{
	var w = $("#keyword").val();
	if (w == "")
	{
		g_baidubbs_flag = 0;
		return;
	}
	$.ajax({
    		url: "weibo_search_ajax.php",  
            type: "POST",
            data:{w:w},
            dataType: "json",
            error: function()
            {
            	g_weibo_flag = 0;
            	loading_tencent();
            },  
            success: function(data)
            {
				append_result(data);
				g_weibo_flag = 0;
				loading_tencent();
			} 					   
    });
}

function loading_weixin()
{
	var w = $("#keyword").val();
	if (w == "")
	{
		g_weixin_flag = 0;
		return;
	}
	
	$.ajax({
    		url: "weixin_search_ajax.php",  
            type: "POST",
            data:{w:w},
            dataType: "json",
            error: function()
            {
            	//alert("error");
            	g_weixin_flag = 0;
            	hide_loading();
            },  
            success: function(data)
            {
            	//alert(data);
				append_result(data);
				g_weixin_flag = 0;
				hide_loading();
			} 					   
    });
}

function hide_loading()
{
	var a_type = $("#a_type").val();
	if (a_type == 1)
	{
		if (g_baidunews_flag == 0 &&
			g_360news_flag == 0 &&
			g_sogounews_flag == 0)
		{
				$("#loading").hide();
		}
	}
	else if (a_type == 2)
	{
		if (g_baidubbs_flag == 0 &&
			g_sogoubbs_flag == 0)
		{
				$("#loading").hide();
		}
	}
	else if (a_type == 4)
	{
		if (g_weibo_flag == 0 &&
			g_tencent_flag == 0)
		{
				$("#loading").hide();
		}
	}
	else if (a_type == 6)
	{
		if (g_weixin_flag == 0)
		{
				$("#loading").hide();
		}
	}
}

function clean_result()
{
	$.ajax({
    		url: "clean_search_ajax.php",  
            type: "POST",
            data:{user_id:<?php echo $user_id ?>},
            dataType: "json",
            error: function()
            {
            },  
            success: function(data)
            {
            	//alert(data);
			} 					   
    });
    g_i = 0;
    $("#uk_id_All").val(0);
}

function search()
{	
	var w = $("#keyword").val();
	if (w == "")
	{
		hide_loading();
		return;
	}
	clean_result();
	$("#show_list").html("");
	$("#loading").show();
	var a_type_cur = $("#search_a_type").val();
	$("#a_type").val(a_type_cur);
	var a_type = $("#a_type").val();
	if (a_type == 1)
	{
		g_baidunews_flag = 1;
		g_sogounews_flag = 1;
		g_360news_flag = 1;
		loading_baidunews();
	}
	else if (a_type == 2)
	{
		g_baidubbs_flag = 1;
		g_sogoubbs_flag = 1;
		loading_baidubbs();
	}
	else if (a_type == 4)
	{
		g_weibo_flag = 1;
		g_tecent_flag = 1;
		loading_weibo();
	}
	else if (a_type == 6)
	{
		g_weixin_flag = 1;
		loading_weixin();
	}
	
	hide_loading();
}

search();

</script>