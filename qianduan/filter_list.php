<?php
  include_once('check_user.php');
include_once('function.inc.php');

set_time_limit(300);//设置运行时间，防止数据多时运行时间过长而终止
ini_set('memory_limit', '256M');//修改php的运行内存限制,因为app层面导出数据出现内存不足的问题
  // 获取的html,带模拟登陆

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="Author" content="微普科技http://www.wiipu.com"/>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="styles/global.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="styles/style.css" type="text/css"/>
    <link rel="stylesheet" href="style/css/common.css" />
    <link rel="stylesheet" href="style/css/index.css" />
    <link rel="stylesheet" href="style/css/other.css" />
    <link rel="stylesheet" href="style/js/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="style/js/layer/skin/default/layer.css" />
  <link rel="stylesheet" type="text/css" href="styles/jquery-ui.css" />
  <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="js/jquery-ui.js"></script>
  <script type="text/javascript" src="js/jquery-ui-slide.min.js"></script>
  <script type="text/javascript" src="js/layer/layer.js"></script>
  <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
    <script type="text/javascript">
        $(function(){
            $(".add").click(function(){
                var thisid = $(this).parent('td').find('#gid').val();
                var thistype = $(this).parent('td').find('#gtype').val();
                var url = $(this).parent('td').find('#gurl').val();
//                alert(url);
                layer.confirm('确认通过吗？', {
                        btn: ['确认','取消']
                    }, function(){
                        var index = layer.load(0, {shade: false});
                        $.ajax({
                            type        : 'POST',
                            data        : {
                                id : thisid,
                                type    : thistype,
                                url:url
                            },
                            dataType : 'json',
                            url : 'filter_list_do.php?act=add',
                            success : function(data){
//                                alert(data);
                                layer.close(index);

                                var code = data.code;
                                var msg  = data.msg;
                                switch(code){
                                    case 1:
                                        layer.alert(msg, {icon: 6}, function(index){
                                            location.reload();
                                        });
                                        break;
                                    default:
                                        layer.alert(msg, {icon: 5});
                                }
                            }
                        });
                    }, function(){}
                );

            });
            //添加
            $(".delete").click(function(){
                var thisid = $(this).parent('td').find('#gid').val();
                var kind=1;

                layer.confirm('确认删除？', {
                        btn: ['确认','取消']
                    }, function(){
                        var index = layer.load(0, {shade: false});
                        $.ajax({
                            type        : 'POST',
                            data        : {
                                id : thisid,
                                kind:kind
                            },
                            dataType : 'json',
                            url : 'filter_list_do.php?act=del',
                            success : function(data){

                                layer.close(index);

                                var code = data.code;
                                var msg  = data.msg;
                                switch(code){
                                    case 1:
                                        layer.alert(msg, {icon: 6}, function(index){
                                            location.reload();
                                        });
                                        break;
                                    default:
                                        layer.alert(msg, {icon: 5});
                                }
                            }
                        });
                    }, function(){}
                );

            });

        });
    </script>
  <title> 微普舆情监控系统</title>
</head>
<body>
<?php
require_once('header.php');
?>
<div class="Navigation">
	<ul>
    	<li><a href="#">首页</a></li>
        <li><a href="#">过滤列表</a></li>
    </ul>
</div>
<p class="ClearBoth "></p>
<div class="Content">

				<div class="Content" style="padding-top:0px;">
                    <?php
                    require_once('adminv/inc_dbconn.php');

                    $sql = "select count(1) from filter_list where filter_status=1 ";
                    $res = mysql_query($sql);
                    //
                    //                               echo $sql;

                    $totalcount = mysql_fetch_array($res)[0];


                    echo "共".$totalcount."条";


                    $shownum   = 20;

                    $pagecount = ceil($totalcount / $shownum);

                    $page      = getPage($pagecount);



                    ?>
				  <table border="0" cellspacing="0" cellpadding="0" width="900">
				     <thead class="Header Center">

				         <tr>
                             <td width="4%">序号</td>
                             <td width="30%">文章标题</td>
                             <td width="10%">媒体名称</td>
                             <td width="6%">关键字</td>
                             <td width="6%">类型</td>
						     <td width="14%">操作</td>
				         </tr>
					 </thead>
					 <tbody>
						 <tr>
                         <?php
                             require_once('adminv/inc_dbconn.php');
                             $i = ($page-1)*$shownum+1;
                             $start = ($page-1)*$shownum;

                         $sql = "select * from filter_list where filter_status =1  limit {$start} ,{$shownum}";

                             $res = mysql_query($sql);
                             //                                echo $sql;
                             while ($row = mysql_fetch_array($res))
                         {
                         ?>

                             <td><?php echo $i;?></td>

                             <td ><a href="<?php echo $row['filter_url']; ?>" target="_blank" title="点击查看原文">
                                     <?php echo  $row['filter_title']; ?>
                                 </a>

                             </td>
                             <td><?php echo $row['filter_media']; ?></td>
                             <td><?php echo $row['filter_keyword']; ?></td>

                             <td><?php
                                 switch ($row['filter_type']){
                                     case 1:
                                         echo "新闻";
                                         break;
                                     case 2:
                                         echo "论坛";
                                         break;
                                     case 3:
                                         echo "博客";
                                         break;
                                     case 4:
                                         echo "微博";
                                         break;
                                     case 5:
                                         echo "视频";
                                         break;
                                     case 6:
                                         echo "微信";
                                         break;
                                     case 7:
                                         echo "知道";
                                         break;
                                     case 8:
                                         echo "app";
                                         break;


                                 }



                                 ?></td>
                             <td>
                                 <input type="hidden" id="gurl" value=<?php echo $row['filter_url'];?> />
                                 <input type="hidden" id="gid" value=<?php echo $row['filter_id'];?> />
                                 <input type="hidden" id="gtype" value=<?php echo $row['filter_type'];?> />
                                 <a  href = "javascript:void(0)" class="add" title = "审核通过" > 过滤</a >
                                 <a  href = "javascript:void(0)" class="delete" title = "删除" > 不过滤</a >
                             </td>
                         </tr>
                         <?php
                         $i++;
                         }
                         ?>
						 </tbody>
						 </table>

                    <?php
                    if($pagecount>=0)
                    {
                        echo dspPagesForMin(getPageUrl(), $page, $shownum, $totalcount, $pagecount);
                    }
                    ?>

				</div>

			</div>

		<?php include_once('footer.php');?>	
		<div class="clear"></div>

 </div>
 </body>
</html>
