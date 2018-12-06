<?php
require_once('adminv/inc_dbconn.php');
require_once('Classes/PHPExcel.php');

function excel_data($str)
{
    while (strncmp($str, "=", 1) == 0) {
        $str = substr($str, 1);
    }
    return $str;
}

function filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)
{
    if ($filter_words == "") {
        $flag = true;
    } else {
        $flag = false;
        if ($filter_place == 1) {
            if ($filter_type == 1) {
                if (strstr($article_title, $filter_words)) {
                    $flag = true;
                }
            }
            if ($filter_type == 2) {
                if (!strstr($article_title, $filter_words)) {
                    $flag = true;
                }
            }
        }
        if ($filter_place == 2) {
            if ($filter_type == 1) {
                if (strstr($article_title, $filter_words) || strstr($article_summary, $filter_words)) {
                    $flag = true;
                }
            }
            if ($filter_type == 2) {
                if (!strstr($article_title, $filter_words) && !strstr($article_summary, $filter_words)) {
                    $flag = true;
                }
            }

        }
    }
    return $flag;
}

$user_id = $_POST['user_id'];
$order = $_POST['order'];//排序方式，发布时间1或采集时间2
$quchong = $_POST['quchong'];//是否去重 0 不去重 1 去重
$uk_ids = $_POST['uk_ids'];
$audit = $_POST['audit'];
$author_type = $_POST['author_type'];
$property = $_POST['property'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$start_time = strtotime($start_date);
$end_time = strtotime($end_date);
$file_name = $_POST['file_name'];
$filter_place = $_POST['filter_place'];
$filter_type = $_POST['filter_type'];
$filter_words = $_POST['filter_words'];
$media_type = isset($_POST['media_type']) ? $_POST['media_type'] : 0;//媒体类型

$query = "select article_id,article_property,id,uk_id,a_type from news_key where uk_id in (" . $uk_ids . ") and status = 1 ";
if ($order == 1) { //按发布时间
    $query .= " and article_pubtime >= $start_time and article_pubtime <= $end_time  ";
} elseif ($order == 2) { //按采集时间
    $query .= " and article_addtime>=$start_time and article_addtime<=$end_time  ";
}
if ($audit != "all") {
    $query .= "and audit_status=$audit ";
}
if ($property != "all") {
    $query .= "and article_property=$property ";
}

if ($quchong == 1) {//去重
    $query .= "group by article_id,uk_id ";
}

if ($order == 1) { //按发布时间
    $query .= " order by article_pubtime desc";
} elseif ($order == 2) { //按采集时间
    $query .= "  order by article_addtime desc";
}

$res = mysql_query($query);
$array[0]['order'] = "序号";
$array[0]['brand'] = "品牌";
$array[0]['media'] = "媒体名称";
$array[0]['grade'] = "媒体等级";
$array[0]['time'] = "时间";
$array[0]['title'] = "标题";
$array[0]['channel'] = "所在频道";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['comment_num'] = "评论数";
$array[0]['summary'] = "摘要";
$array[0]['a_type'] = "文章类型";
$array[0]['source'] = "来源";
$i = 1;
while ($row = mysql_fetch_array($res)) {
    $article_id = $row['article_id'];
    $id = $row['id'];
    $a_type = $row['a_type'];
    if ($a_type == 0) {
        $type = "非经销商发稿";
    } elseif ($a_type == 1) {
        $type = "经销商发稿";
    } elseif ($a_type == 2) {
        $type = "竞品攻击";
    } elseif ($a_type == 3) {
        $type = "非车主投诉";
    } elseif ($a_type == 4) {
        $type = "车主投诉";
    }
    $uk_id = $row['uk_id'];
    $query1 = "select k_id from user_keywords where uk_id=$uk_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $k_id = $row1['k_id'];
    $query1 = "select keyword from keyword where k_id=$k_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $keyword = $row1['keyword'];
    $article_property = $row['article_property'];
    $query2 = "select * from news_article where article_id=$article_id";
    if ($media_type > 0) {
//
        $query2 .= " and article_grade = $media_type";

    }
    $res2 = mysql_query($query2);
    $x=mysql_num_rows($res2);
    if($x==0){
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $article_title = $row2['article_title'];
    $article_summary = $row2['article_content'];
    if (filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)) {
        $media = $row2['media'];
        $array[$i]['order'] = $i;
        $array[$i]['brand'] = $keyword;
        $array[$i]['media'] = $media;
        $array[$i]['grade'] = "D级";
        $query3 = "select grade from media_list where media_name='$media'";
        $res3 = mysql_query($query3);
        $row3 = mysql_fetch_array($res3);
        if ($row3['grade'] == 1) {
            $array[$i]['grade'] = "A级";
        } elseif ($row3['grade'] == 2) {
            $array[$i]['grade'] = "B级";
        } elseif ($row3['grade'] == 3) {
            $array[$i]['grade'] = "C级";
        } elseif ($row3['grade'] == 4) {
            $array[$i]['grade'] = "D级";
        }

        if ($media_type > 0) {
//
            switch ($media_type) {
                case "1":
                    $array[$i]['grade'] = "A级";
                    break;
                case '2':
                    $array[$i]['grade'] = "B级";
                    break;
                case '3':
                    $array[$i]['grade'] = "C级";
                    break;

            }

        }

        if ($order == 1) { //按发布时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
        } elseif ($order == 2) { //按采集时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
        }
        $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_title']);
        $array[$i]['link'] = $row2['article_url'];
        $array[$i]['channel'] = $row2['article_channel'];
        if ($article_property == 0) {
            $array[$i]['property'] = "中性";
        } elseif ($article_property == 1) {
            $array[$i]['property'] = "正面";
        } elseif ($article_property == 2) {
            $array[$i]['property'] = "负面";
        } elseif ($article_property == 3) {
            $array[$i]['property'] = "不良";
        }
        $array[$i]['comment_num'] = $row2['article_comment'];
        $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_content']);
        $array[$i]['a_type'] = $type;
        $array[$i]['source'] = $row2['article_source'];

    }
    $i++;
}
/* @实例化 */
$obpe = new PHPExcel();
$time = time();
$time_str = date('Y/m/d H:i', $time);
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
/* 设置宽度 */
//$obpe->getActiveSheet()->getColumnDimension()->setAutoSize(true);
//$obpe->getActiveSheet()->getColumnDimension('B')->setWidth(10);

//设置当前sheet索引,用于后续的内容操作
//一般用在对个Sheet的时候才需要显示调用
//缺省情况下,PHPExcel会自动创建第一个SHEET被设置SheetIndex=0
//设置SHEET
$obpe->setactivesheetindex(0);
$objActSheet = $obpe->getActiveSheet();
$objActSheet->getDefaultRowDimension()->setRowHeight(15);

$objActSheet->setTitle('新闻');
$width_array = array('A' => 5, 'B' => 10, 'C' => 10, 'D' => 10, 'E' => 17, 'F' => 50, 'G' => 11, 'H' => 67, 'I' => 10, 'J' => 8, 'K' => 30, 'L' => 15, 'M' => 15);
foreach ($width_array as $k => $v) {
    $objActSheet->getColumnDimension($k)->setWidth($v);
}
$color_array = array("A1", "B1", "C1", "D1", "E1", "F1", "G1", "H1", "I1", "J1", "K1", "L1", "M1");
foreach ($color_array as $v) {
    $objStyle = $objActSheet->getStyle($v);
    $objFill = $objStyle->getFill();
    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objFill->getStartColor()->setARGB('CCCCCCCC');
}
//写入多行数据
foreach ($array as $k => $v) {
    $k = $k + 1;
    /* @func 设置列 */
    $objActSheet->setcellvalue('A' . $k, excel_data($v['order']));
    $objActSheet->setcellvalue('B' . $k, excel_data($v['brand']));
    $objActSheet->setcellvalue('C' . $k, excel_data($v['media']));
    $objActSheet->setcellvalue('D' . $k, excel_data($v['grade']));
    $objActSheet->setcellvalue('E' . $k, excel_data($v['time']));
    $objActSheet->setcellvalue('F' . $k, excel_data($v['title']));
    $objActSheet->setcellvalue('G' . $k, excel_data($v['channel']));
    $objActSheet->setcellvalue('H' . $k, excel_data($v['link']));
    $objActSheet->setcellvalue('I' . $k, excel_data($v['property']));
    $objActSheet->setcellvalue('J' . $k, excel_data($v['comment_num']));
    $objActSheet->setcellvalue('K' . $k, excel_data($v['summary']));
    $objActSheet->setcellvalue('L' . $k, excel_data($v['a_type']));
    $objActSheet->setcellvalue('M' . $k, excel_data($v['source']));
}
unset($array);


$query = "select article_id,article_property,id,uk_id,a_type from video_key where uk_id in (" . $uk_ids . ")  and status = 1 ";
if ($order == 1) { //按发布时间
    $query .= " and article_pubtime >= $start_time and article_pubtime <= $end_time  ";
} elseif ($order == 2) { //按采集时间
    $query .= " and article_addtime>=$start_time and article_addtime<=$end_time  ";
}
if ($audit != "all") {
    $query .= "and audit_status=$audit ";
}
if ($property != "all") {
    $query .= "and article_property=$property ";
}

if ($quchong == 1) {//去重
    $query .= "group by article_id,uk_id ";
}

if ($order == 1) { //按发布时间
    $query .= " order by article_pubtime desc";
} elseif ($order == 2) { //按采集时间
    $query .= " order by article_addtime desc";
}

$res = mysql_query($query);
$array[0]['order'] = "序号";
$array[0]['brand'] = "品牌";
$array[0]['media'] = "媒体名称";
$array[0]['grade'] = "媒体等级";
$array[0]['time'] = "时间";
$array[0]['title'] = "标题";
$array[0]['channel'] = "所在频道";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['summary'] = "摘要";
$array[0]['a_type'] = "文章类型";
$i = 1;
while ($row = mysql_fetch_array($res)) {
    $article_id = $row['article_id'];
    $id = $row['id'];
    $a_type = $row['a_type'];
    if ($a_type == 0) {
        $type = "非经销商发稿";
    } elseif ($a_type == 1) {
        $type = "经销商发稿";
    } elseif ($a_type == 2) {
        $type = "竞品攻击";
    } elseif ($a_type == 3) {
        $type = "非车主投诉";
    } elseif ($a_type == 4) {
        $type = "车主投诉";
    }
    $uk_id = $row['uk_id'];
    $query1 = "select k_id from user_keywords where uk_id=$uk_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $k_id = $row1['k_id'];
    $query1 = "select keyword from keyword where k_id=$k_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $keyword = $row1['keyword'];
    $article_property = $row['article_property'];
    $query2 = "select * from video_article where article_id=$article_id";
    if ($media_type > 0) {
//
        $query2 .= " and article_grade = $media_type";

    }
    $res2 = mysql_query($query2);
    $x=mysql_num_rows($res2);
    if($x==0){
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $article_title = $row2['article_title'];
    $article_summary = $row2['article_summary'];
    if (filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)) {
        $media = $row2['media'];
        $array[$i]['order'] = $i;
        $array[$i]['brand'] = $keyword;
        $array[$i]['media'] = $media;
        $array[$i]['grade'] = "D级";
        $query3 = "select grade from media_list where media_name='$media'";
        $res3 = mysql_query($query3);
        $row3 = mysql_fetch_array($res3);
        if ($row3['grade'] == 1) {
            $array[$i]['grade'] = "A级";
        } elseif ($row3['grade'] == 2) {
            $array[$i]['grade'] = "B级";
        } elseif ($row3['grade'] == 3) {
            $array[$i]['grade'] = "C级";
        } elseif ($row3['grade'] == 4) {
            $array[$i]['grade'] = "D级";
        }

        if ($media_type > 0) {
//
            switch ($media_type) {
                case "1":
                    $array[$i]['grade'] = "A级";
                    break;
                case '2':
                    $array[$i]['grade'] = "B级";
                    break;
                case '3':
                    $array[$i]['grade'] = "C级";
                    break;

            }

        }

        $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_title']);
        $array[$i]['link'] = $row2['article_url'];
        if ($order == 1) { //按发布时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
        } elseif ($order == 2) { //按采集时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
        }
        if ($article_property == 0) {
            $array[$i]['property'] = "中性";
        } elseif ($article_property == 1) {
            $array[$i]['property'] = "正面";
        } elseif ($article_property == 2) {
            $array[$i]['property'] = "负面";
        } elseif ($article_property == 3) {
            $array[$i]['property'] = "不良";
        }
        $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_content']);
        $i++;
    }
}
//创建一个新的工作空间(sheet)
$obpe->createSheet();
$obpe->setactivesheetindex(1);
$objActSheet = $obpe->getActiveSheet();
$objActSheet->getDefaultRowDimension()->setRowHeight(15);
$objActSheet->setTitle('视频');
$blog_width_array = array('A' => 5, 'B' => 10, 'C' => 10, 'D' => 10, 'E' => 17, 'F' => 50, 'G' => 15, 'H' => 67, 'I' => 10, 'J' => 30, 'L' => 15);
foreach ($blog_width_array as $k => $v) {
    $objActSheet->getColumnDimension($k)->setWidth($v);
}
foreach ($color_array as $v) {
    $objStyle = $objActSheet->getStyle($v);
    $objFill = $objStyle->getFill();
    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objFill->getStartColor()->setARGB('CCCCCCCC');
}
//写入多行数据
foreach ($array as $k => $v) {
    $k = $k + 1;
    // @func 设置列
    $objActSheet->setcellvalue('A' . $k, excel_data($v['order']));
    $objActSheet->setcellvalue('B' . $k, excel_data($v['brand']));
    $objActSheet->setcellvalue('C' . $k, excel_data($v['media']));
    $objActSheet->setcellvalue('D' . $k, excel_data($v['grade']));
    $objActSheet->setcellvalue('E' . $k, excel_data($v['time']));
    $objActSheet->setcellvalue('F' . $k, excel_data($v['title']));
    $objActSheet->setcellvalue('G' . $k, excel_data($v['channel']));
    $objActSheet->setcellvalue('H' . $k, excel_data($v['link']));
    $objActSheet->setcellvalue('I' . $k, excel_data($v['property']));
    $objActSheet->setcellvalue('J' . $k, excel_data($v['summary']));
    $objActSheet->setcellvalue('L' . $k, excel_data($v['a_type']));

}
unset($array);

$query = "select article_id,article_property,id,uk_id,a_type from bbs_key where uk_id in (" . $uk_ids . ")  and status = 1 ";
if ($order == 1) { //按发布时间
    $query .= " and article_pubtime >= $start_time and article_pubtime <= $end_time  ";
} elseif ($order == 2) { //按采集时间
    $query .= " and article_addtime>=$start_time and article_addtime<=$end_time  ";
}
if ($audit != "all") {
    $query .= "and audit_status=$audit ";
}
if ($property != "all") {
    $query .= "and article_property=$property ";
}

if ($quchong == 1) {//去重
    $query .= "group by article_id,uk_id ";
}

if ($order == 1) { //按发布时间
    $query .= "  order by article_pubtime desc";
} elseif ($order == 2) { //按采集时间
    $query .= " order by article_addtime desc";
}

$res = mysql_query($query);
$array[0]['order'] = "序号";
$array[0]['brand'] = "品牌";
$array[0]['media'] = "媒体名称";
$array[0]['grade'] = "媒体等级";
$array[0]['time'] = "时间";
$array[0]['title'] = "标题";
$array[0]['forum'] = "所在版块";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['click_num'] = "点击量";
$array[0]['reply_num'] = "回复量";
$array[0]['summary'] = "摘要";
$array[0]['a_type'] = "文章类型";
$array[0]['author'] = "发帖ID";
$i = 1;
while ($row = mysql_fetch_array($res)) {
    $article_id = $row['article_id'];
    $id = $row['id'];
    $a_type = $row['a_type'];
    if ($a_type == 0) {
        $type = "非经销商发稿";
    } elseif ($a_type == 1) {
        $type = "经销商发稿";
    } elseif ($a_type == 2) {
        $type = "竞品攻击";
    } elseif ($a_type == 3) {
        $type = "非车主投诉";
    } elseif ($a_type == 4) {
        $type = "车主投诉";
    }
    $uk_id = $row['uk_id'];
    $query1 = "select k_id from user_keywords where uk_id=$uk_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $k_id = $row1['k_id'];
    $query1 = "select keyword from keyword where k_id=$k_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $keyword = $row1['keyword'];
    $article_property = $row['article_property'];
    $query2 = "select * from bbs_article where article_id=$article_id";
    if ($media_type > 0) {
//
        $query2 .= " and article_grade = $media_type";

    }
    $res2 = mysql_query($query2);
    $x=mysql_num_rows($res2);
    if($x==0){
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $article_title = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_title']);
    $article_summary = $row2['article_content'];
    if (filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)) {
        $media = $row2['media'];
        $array[$i]['order'] = $i;
        $array[$i]['brand'] = $keyword;
        $array[$i]['media'] = $media;
        $array[$i]['grade'] = "D级";
        $query3 = "select grade from media_list where media_name='$media'";
        $res3 = mysql_query($query3);
        $row3 = mysql_fetch_array($res3);
        if ($row3['grade'] == 1) {
            $array[$i]['grade'] = "A级";
        } elseif ($row3['grade'] == 2) {
            $array[$i]['grade'] = "B级";
        } elseif ($row3['grade'] == 3) {
            $array[$i]['grade'] = "C级";
        } elseif ($row3['grade'] == 4) {
            $array[$i]['grade'] = "D级";
        }
        if ($media_type > 0) {
//
            switch ($media_type) {
                case "1":
                    $array[$i]['grade'] = "A级";
                    break;
                case '2':
                    $array[$i]['grade'] = "B级";
                    break;
                case '3':
                    $array[$i]['grade'] = "C级";
                    break;

            }

        }


        $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_title']);
        $array[$i]['link'] = $row2['article_url'];
        if ($order == 1) { //按发布时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
        } elseif ($order == 2) { //按采集时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
        }
        $array[$i]['forum'] = $row2['forum'];
        if ($article_property == 0) {
            $array[$i]['property'] = "中性";
        } elseif ($article_property == 1) {
            $array[$i]['property'] = "正面";
        } elseif ($article_property == 2) {
            $array[$i]['property'] = "负面";
        } elseif ($article_property == 3) {
            $array[$i]['property'] = "不良";
        }
        $array[$i]['click_num'] = $row2['article_click'];
        $array[$i]['reply_num'] = $row2['article_reply'];
        $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_content']);
        $array[$i]['a_type'] = $type;
        $array[$i]['author'] = $row2['author'];

        $i++;
    }
}
//创建一个新的工作空间(sheet)
$obpe->createSheet();
$obpe->setactivesheetindex(2);
$objActSheet = $obpe->getActiveSheet();
$objActSheet->getDefaultRowDimension()->setRowHeight(15);
$objActSheet->setTitle('论坛');
$bbs_width_array = array('A' => 5, 'B' => 10, 'C' => 10, 'D' => 10, 'E' => 17, 'F' => 50, 'G' => 15, 'H' => 67, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 30, 'M' => 15, 'N' => 15);
foreach ($bbs_width_array as $k => $v) {
    $objActSheet->getColumnDimension($k)->setWidth($v);
}
foreach ($color_array as $v) {
    $objStyle = $objActSheet->getStyle($v);
    $objFill = $objStyle->getFill();
    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objFill->getStartColor()->setARGB('CCCCCCCC');
}
//写入多行数据
foreach ($array as $k => $v) {
    $k = $k + 1;
    // @func 设置列
    $objActSheet->setcellvalue('A' . $k, excel_data($v['order']));
    $objActSheet->setcellvalue('B' . $k, excel_data($v['brand']));
    $objActSheet->setcellvalue('C' . $k, excel_data($v['media']));
    $objActSheet->setcellvalue('D' . $k, excel_data($v['grade']));
    $objActSheet->setcellvalue('E' . $k, excel_data($v['time']));
    $objActSheet->setcellvalue('F' . $k, excel_data($v['title']));
    $objActSheet->setcellvalue('G' . $k, excel_data($v['forum']));
    $objActSheet->setcellvalue('H' . $k, excel_data($v['link']));
    $objActSheet->setcellvalue('I' . $k, excel_data($v['property']));
    $objActSheet->setcellvalue('J' . $k, excel_data($v['click_num']));
    $objActSheet->setcellvalue('K' . $k, excel_data($v['reply_num']));
    $objActSheet->setcellvalue('L' . $k, excel_data($v['summary']));
    $objActSheet->setcellvalue('M' . $k, excel_data($v['a_type']));
    $objActSheet->setcellvalue('N' . $k, excel_data($v['author']));
}
unset($array);


$query = "select article_id,article_property,id,uk_id,a_type from blog_key where uk_id in (" . $uk_ids . ") and status = 1 ";
if ($order == 1) { //按发布时间
    $query .= " and article_pubtime >= $start_time and article_pubtime <= $end_time  ";
} elseif ($order == 2) { //按采集时间
    $query .= " and article_addtime>=$start_time and article_addtime<= $end_time  ";
}
if ($audit != "all") {
    $query .= "and audit_status=$audit ";
}
if ($property != "all") {
    $query .= "and article_property=$property ";
}

if ($quchong == 1) {//去重
    $query .= "group by article_id,uk_id ";
}

if ($order == 1) { //按发布时间
    $query .= " order by article_pubtime desc";
} elseif ($order == 2) { //按采集时间
    $query .= " order by article_addtime desc";
}

$res = mysql_query($query);
$array[0]['order'] = "序号";
$array[0]['brand'] = "品牌";
$array[0]['media'] = "媒体名称";
$array[0]['grade'] = "媒体等级";
$array[0]['time'] = "时间";
$array[0]['title'] = "标题";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['read_num'] = "阅读量";
$array[0]['comment_num'] = "评论量";
$array[0]['summary'] = "摘要";
$array[0]['a_type'] = "文章类型";
$array[0]['author'] = "发帖ID";
$i = 1;
while ($row = mysql_fetch_array($res)) {
    $article_id = $row['article_id'];
    $id = $row['id'];
    $a_type = $row['a_type'];
    if ($a_type == 0) {
        $type = "非经销商发稿";
    } elseif ($a_type == 1) {
        $type = "经销商发稿";
    } elseif ($a_type == 2) {
        $type = "竞品攻击";
    } elseif ($a_type == 3) {
        $type = "非车主投诉";
    } elseif ($a_type == 4) {
        $type = "车主投诉";
    }
    $uk_id = $row['uk_id'];
    $query1 = "select k_id from user_keywords where uk_id=$uk_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $k_id = $row1['k_id'];
    $query1 = "select keyword from keyword where k_id=$k_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $keyword = $row1['keyword'];
    $article_property = $row['article_property'];
    $query2 = "select * from blog_article where article_id=$article_id";
    if ($media_type > 0) {
//
        $query2 .= " and article_grade = $media_type";

    }
    $res2 = mysql_query($query2);
    $x=mysql_num_rows($res2);
    if($x==0){
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $article_title = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_title']);
    $article_summary = $row2['article_content'];
    if (filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)) {
        $media = $row2['media'];
        $array[$i]['order'] = $i;
        $array[$i]['brand'] = $keyword;
        $array[$i]['media'] = $media;
        $array[$i]['grade'] = "D级";
        $query3 = "select grade from media_list where media_name='$media'";
        $res3 = mysql_query($query3);
        $row3 = mysql_fetch_array($res3);
        if ($row3['grade'] == 1) {
            $array[$i]['grade'] = "A级";
        } elseif ($row3['grade'] == 2) {
            $array[$i]['grade'] = "B级";
        } elseif ($row3['grade'] == 3) {
            $array[$i]['grade'] = "C级";
        } elseif ($row3['grade'] == 4) {
            $array[$i]['grade'] = "D级";
        }

        if ($media_type > 0) {
//
            switch ($media_type) {
                case "1":
                    $array[$i]['grade'] = "A级";
                    break;
                case '2':
                    $array[$i]['grade'] = "B级";
                    break;
                case '3':
                    $array[$i]['grade'] = "C级";
                    break;

            }

        }

        $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_title']);
        $array[$i]['link'] = $row2['article_url'];
        if ($order == 1) { //按发布时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
        } elseif ($order == 2) { //按采集时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
        }
        if ($article_property == 0) {
            $array[$i]['property'] = "中性";
        } elseif ($article_property == 1) {
            $array[$i]['property'] = "正面";
        } elseif ($article_property == 2) {
            $array[$i]['property'] = "负面";
        } elseif ($article_property == 3) {
            $array[$i]['property'] = "不良";
        }
        $array[$i]['read_num'] = $row2['article_click'];
        $array[$i]['comment_num'] = $row2['article_comment'];
        $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_content']);
        $array[$i]['a_type'] = $type;
        $array[$i]['author'] = $row2['author'];
        $i++;
    }
}
//创建一个新的工作空间(sheet)
$obpe->createSheet();
$obpe->setactivesheetindex(3);
$objActSheet = $obpe->getActiveSheet();
$objActSheet->getDefaultRowDimension()->setRowHeight(15);
$objActSheet->setTitle('博客');
$blog_width_array = array('A' => 5, 'B' => 10, 'C' => 10, 'D' => 10, 'E' => 17, 'F' => 50, 'G' => 67, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 30, 'L' => 15, 'M' => 15);
foreach ($blog_width_array as $k => $v) {
    $objActSheet->getColumnDimension($k)->setWidth($v);
}
foreach ($color_array as $v) {
    $objStyle = $objActSheet->getStyle($v);
    $objFill = $objStyle->getFill();
    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objFill->getStartColor()->setARGB('CCCCCCCC');
}
//写入多行数据
foreach ($array as $k => $v) {
    $k = $k + 1;
    // @func 设置列
    $objActSheet->setcellvalue('A' . $k, excel_data($v['order']));
    $objActSheet->setcellvalue('B' . $k, excel_data($v['brand']));
    $objActSheet->setcellvalue('C' . $k, excel_data($v['media']));
    $objActSheet->setcellvalue('D' . $k, excel_data($v['grade']));
    $objActSheet->setcellvalue('E' . $k, excel_data($v['time']));
    $objActSheet->setcellvalue('F' . $k, excel_data($v['title']));
    $objActSheet->setcellvalue('G' . $k, excel_data($v['link']));
    $objActSheet->setcellvalue('H' . $k, excel_data($v['property']));
    $objActSheet->setcellvalue('I' . $k, excel_data($v['read_num']));
    $objActSheet->setcellvalue('J' . $k, excel_data($v['comment_num']));
    $objActSheet->setcellvalue('K' . $k, excel_data($v['summary']));
    $objActSheet->setcellvalue('L' . $k, excel_data($v['a_type']));
    $objActSheet->setcellvalue('M' . $k, excel_data($v['author']));
}
unset($array);
$query = "select article_id,article_property,id,uk_id,a_type from weibo_key where uk_id in (" . $uk_ids . ")  and status = 1 ";
if ($order == 1) { //按发布时间
    $query .= " and article_pubtime >= $start_time and article_pubtime <= $end_time  ";
} elseif ($order == 2) { //按采集时间
    $query .= " and article_addtime>=$start_time and article_addtime<=$end_time  ";
}
if ($audit != "all") {
    $query .= "and audit_status=$audit ";
}
if ($property != "all") {
    $query .= "and article_property=$property ";
}

if ($quchong == 1) {//去重
    $query .= "group by article_id,uk_id ";
}

if ($order == 1) { //按发布时间
    $query .= " order by article_pubtime desc";
} elseif ($order == 2) { //按采集时间
    $query .= " order by article_addtime desc";
}

$res = mysql_query($query);
$array[0]['order'] = "序号";
$array[0]['brand'] = "品牌";
$array[0]['media'] = "媒体名称";
$array[0]['grade'] = "媒体等级";
$array[0]['time'] = "时间";
$array[0]['content'] = "内容";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['repost_num'] = "转发量";
$array[0]['comment_num'] = "评论量";
$array[0]['author'] = "发帖ID";
$array[0]['isV'] = "是否加V";
$array[0]['rz_info'] = "认证类型";
$array[0]['fans'] = "粉丝量";
$array[0]['a_type'] = "文章类型";
$i = 1;
while ($row = mysql_fetch_array($res)) {
    $article_id = $row['article_id'];
    $id = $row['id'];
    $a_type = $row['a_type'];
    if ($a_type == 0) {
        $type = "非经销商发稿";
    } elseif ($a_type == 1) {
        $type = "经销商发稿";
    } elseif ($a_type == 2) {
        $type = "竞品攻击";
    } elseif ($a_type == 3) {
        $type = "非车主投诉";
    } elseif ($a_type == 4) {
        $type = "车主投诉";
    }
    $uk_id = $row['uk_id'];
    $query1 = "select k_id from user_keywords where uk_id=$uk_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $k_id = $row1['k_id'];
    $query1 = "select keyword from keyword where k_id=$k_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $keyword = $row1['keyword'];
    $article_property = $row['article_property'];
    if ($author_type == "all") {
        $query2 = "select * from weibo_article where article_id=$article_id";
    } else {
        $query2 = "select * from weibo_article where article_id=$article_id and isV=$author_type";
    }
    if ($media_type > 0) {
//
        $query2 .= " and article_grade = $media_type";

    }
    $res2 = mysql_query($query2);
    $x = mysql_num_rows($res2);
    if ($x == 0) {
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $article_title = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_title']);
    if ($filter_words == "") {
        $flag = true;
    } else {
        $flag = false;
        if ($filter_type == 1) {
            if (strstr($article_title, $filter_words)) {
                $flag = true;
            }
        }
        if ($filter_type == 2) {
            if (!strstr($article_title, $filter_words)) {
                $flag = true;
            }
        }
    }
    if ($flag) {
        $media = $row2['media'];
        $array[$i]['order'] = $i;
        $array[$i]['brand'] = $keyword;
        $array[$i]['media'] = $media;
        $array[$i]['grade'] = "D级";
        $query3 = "select grade from media_list where media_name='$media'";
        $res3 = mysql_query($query3);
        $row3 = mysql_fetch_array($res3);
        if ($row3['grade'] == 1) {
            $array[$i]['grade'] = "A级";
        } elseif ($row3['grade'] == 2) {
            $array[$i]['grade'] = "B级";
        } elseif ($row3['grade'] == 3) {
            $array[$i]['grade'] = "C级";
        } elseif ($row3['grade'] == 4) {
            $array[$i]['grade'] = "D级";
        }

        if ($media_type > 0) {
//
            switch ($media_type) {
                case "1":
                    $array[$i]['grade'] = "A级";
                    break;
                case '2':
                    $array[$i]['grade'] = "B级";
                    break;
                case '3':
                    $array[$i]['grade'] = "C级";
                    break;

            }

        }

        $array[$i]['content'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_title']);
        $array[$i]['link'] = $row2['article_url'];
        if ($order == 1) { //按发布时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
        } elseif ($order == 2) { //按采集时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
        }
        if ($article_property == 0) {
            $array[$i]['property'] = "中性";
        } elseif ($article_property == 1) {
            $array[$i]['property'] = "正面";
        } elseif ($article_property == 2) {
            $array[$i]['property'] = "负面";
        } elseif ($article_property == 3) {
            $array[$i]['property'] = "不良";
        }
        $array[$i]['repost_num'] = $row2['article_repost'];
        $array[$i]['comment_num'] = $row2['article_comment'];
        $array[$i]['author'] = $row2['author'];
        if ($row2['isV'] == 0) {
            $array[$i]['isV'] = "否";
        } elseif ($row2['isV'] == 1) {
            $array[$i]['isV'] = "是";
        }
        $array[$i]['rz_info'] = $row2['rz_info'];
        $array[$i]['fans'] = $row2['fans'];
        $array[$i]['a_type'] = $type;
        $i++;
    }
}
//创建一个新的工作空间(sheet)
$obpe->createSheet();
$obpe->setactivesheetindex(4);
$objActSheet = $obpe->getActiveSheet();
$objActSheet->getDefaultRowDimension()->setRowHeight(15);
$objActSheet->setTitle('微博');
$blog_width_array = array('A' => 5, 'B' => 10, 'C' => 10, 'D' => 10, 'E' => 17, 'F' => 50, 'G' => 50, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 12, 'L' => 10, 'M' => 20, 'N' => 10, 'O' => 15);
foreach ($blog_width_array as $k => $v) {
    $objActSheet->getColumnDimension($k)->setWidth($v);
}
$color_array = array("A1", "B1", "C1", "D1", "E1", "F1", "G1", "H1", "I1", "J1", "K1", "L1", "M1", "N1", "O1");
foreach ($color_array as $v) {
    $objStyle = $objActSheet->getStyle($v);
    $objFill = $objStyle->getFill();
    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objFill->getStartColor()->setARGB('CCCCCCCC');
}
//写入多行数据
foreach ($array as $k => $v) {
    $k = $k + 1;
    // @func 设置列
    $objActSheet->setcellvalue('A' . $k, excel_data($v['order']));
    $objActSheet->setcellvalue('B' . $k, excel_data($v['brand']));
    $objActSheet->setcellvalue('C' . $k, excel_data($v['media']));
    $objActSheet->setcellvalue('D' . $k, excel_data($v['grade']));
    $objActSheet->setcellvalue('E' . $k, excel_data($v['time']));
    $objActSheet->setcellvalue('F' . $k, excel_data($v['content']));
    $objActSheet->setcellvalue('G' . $k, excel_data($v['link']));
    $objActSheet->setcellvalue('H' . $k, excel_data($v['property']));
    $objActSheet->setcellvalue('I' . $k, excel_data($v['repost_num']));
    $objActSheet->setcellvalue('J' . $k, excel_data($v['comment_num']));
    $objActSheet->setcellvalue('K' . $k, excel_data($v['author']));
    $objActSheet->setcellvalue('L' . $k, excel_data($v['isV']));
    $objActSheet->setcellvalue('M' . $k, excel_data($v['rz_info']));
    $objActSheet->setcellvalue('N' . $k, excel_data($v['fans']));
    $objActSheet->setcellvalue('O' . $k, excel_data($v['a_type']));
}
unset($array);


if ($audit == "all" && $property == "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from weixin_key where uk_id in (" . $uk_ids . ")  and status = 1 ";
} elseif ($audit == "all" && $property != "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from weixin_key where uk_id in (" . $uk_ids . ")  and article_property=$property and status = 1 ";
} elseif ($audit != "all" && $property == "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from weixin_key where uk_id in (" . $uk_ids . ")  and audit_status=$audit and status =1 ";
} elseif ($audit != "all" && $property != "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from weixin_key where uk_id in (" . $uk_ids . ")  and audit_status=$audit and article_property=$property and status = 1 ";
}
if ($order == 1) { //按发布时间
    $query .= " and article_pubtime >= $start_time and article_pubtime <= $end_time ";

    if ($quchong == 1) {//去重
        $query .= "group by article_id,uk_id ";
    }

    $query .= " order by article_pubtime desc";
} elseif ($order == 2) { //按采集时间
    $query .= " and article_addtime>=$start_time and article_addtime<=$end_time ";

    if ($quchong == 1) {//去重
        $query .= "group by article_id,uk_id ";
    }

    $query .= " order by article_addtime desc";
}

$res = mysql_query($query);
$array[0]['order'] = "序号";
$array[0]['brand'] = "品牌";
$array[0]['media'] = "媒体名称";
$array[0]['time'] = "时间";
$array[0]['title'] = "标题";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['a_type'] = "文章类型";
$array[0]['read_num'] = "阅读量";
$array[0]['like_num'] = "点赞量";
$i = 1;
while ($row = mysql_fetch_array($res)) {
    $article_id = $row['article_id'];
    $id = $row['id'];
    $a_type = $row['a_type'];
    if ($a_type == 0) {
        $type = "非经销商发稿";
    } elseif ($a_type == 1) {
        $type = "经销商发稿";
    } elseif ($a_type == 2) {
        $type = "竞品攻击";
    } elseif ($a_type == 3) {
        $type = "非车主投诉";
    } elseif ($a_type == 4) {
        $type = "车主投诉";
    }
    $uk_id = $row['uk_id'];
    $query1 = "select k_id from user_keywords where uk_id=$uk_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $k_id = $row1['k_id'];
    $query1 = "select keyword from keyword where k_id=$k_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $keyword = $row1['keyword'];
    $article_property = $row['article_property'];
    if ($author_type == "all") {
        $query2 = "select * from weixin_article where article_id=$article_id";
    } else {
        $query2 = "select * from weixin_article where article_id=$article_id";
    }
    if ($media_type > 0) {
//
        $query2 .= " and article_grade = $media_type";

    }
    $res2 = mysql_query($query2);
    $x = mysql_num_rows($res2);
    if ($x == 0) {
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $media = $row2['media'];
    $array[$i]['order'] = $i;
    $array[$i]['brand'] = $keyword;
    $array[$i]['media'] = $media;
    $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_title']);
    $array[$i]['link'] = $row2['article_url'];
    $array[$i]['read_num'] = $row2['read_num'];
    $array[$i]['like_num'] = $row2['like_num'];
    if ($order == 1) { //按发布时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
    } elseif ($order == 2) { //按采集时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
    }
    if ($article_property == 0) {
        $array[$i]['property'] = "中性";
    } elseif ($article_property == 1) {
        $array[$i]['property'] = "正面";
    } elseif ($article_property == 2) {
        $array[$i]['property'] = "负面";
    } elseif ($article_property == 3) {
        $array[$i]['property'] = "不良";
    }
    $array[$i]['a_type'] = $type;
    $i++;
}
//创建一个新的工作空间(sheet)
$obpe->createSheet();
$obpe->setactivesheetindex(5);
$objActSheet = $obpe->getActiveSheet();
$objActSheet->getDefaultRowDimension()->setRowHeight(15);
$objActSheet->setTitle('微信');
$blog_width_array = array('A' => 5, 'B' => 10, 'C' => 10, 'D' => 17, 'E' => 50, 'F' => 67, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10);
foreach ($blog_width_array as $k => $v) {
    $objActSheet->getColumnDimension($k)->setWidth($v);
}
foreach ($color_array as $v) {
    $objStyle = $objActSheet->getStyle($v);
    $objFill = $objStyle->getFill();
    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objFill->getStartColor()->setARGB('CCCCCCCC');
}
//写入多行数据
foreach ($array as $k => $v) {
    $k = $k + 1;
    // @func 设置列
    $objActSheet->setcellvalue('A' . $k, excel_data($v['order']));
    $objActSheet->setcellvalue('B' . $k, excel_data($v['brand']));
    $objActSheet->setcellvalue('C' . $k, excel_data($v['media']));
    $objActSheet->setcellvalue('D' . $k, excel_data($v['time']));
    $objActSheet->setcellvalue('E' . $k, excel_data($v['title']));
    $objActSheet->setcellvalue('F' . $k, excel_data($v['link']));
    $objActSheet->setcellvalue('G' . $k, excel_data($v['property']));
    $objActSheet->setcellvalue('H' . $k, excel_data($v['a_type']));
    $objActSheet->setcellvalue('I' . $k, excel_data($v['read_num']));
    $objActSheet->setcellvalue('J' . $k, excel_data($v['like_num']));

}
unset($array);


if ($audit == "all" && $property == "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from zhidao_key where uk_id in (" . $uk_ids . ")  and status = 1 ";
} elseif ($audit == "all" && $property != "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from zhidao_key where uk_id in (" . $uk_ids . ")  and article_property=$property and status = 1 ";
} elseif ($audit != "all" && $property == "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from zhidao_key where uk_id in (" . $uk_ids . ") and audit_status=$audit and status = 1 ";
} elseif ($audit != "all" && $property != "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from zhidao_key where uk_id in (" . $uk_ids . ") and audit_status=$audit and article_property=$property and status = 1 ";
}
if ($order == 1) { //按发布时间
    $query .= " and article_pubtime >= $start_time and article_pubtime <= $end_time ";

    if ($quchong == 1) {//去重
        $query .= "group by article_id,uk_id ";
    }

    $query .= " order by article_pubtime desc";
} elseif ($order == 2) { //按采集时间
    $query .= " and article_addtime>=$start_time and article_addtime<=$end_time ";

    if ($quchong == 1) {//去重
        $query .= "group by article_id,uk_id ";
    }

    $query .= " order by article_addtime desc";
}

$res = mysql_query($query);
$array[0]['order'] = "序号";
$array[0]['brand'] = "品牌";
$array[0]['media'] = "媒体名称";
$array[0]['time'] = "时间";
$array[0]['title'] = "标题";
$array[0]['summary'] = "摘要";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['a_type'] = "文章类型";
$i = 1;
while ($row = mysql_fetch_array($res)) {
    $article_id = $row['article_id'];
    $id = $row['id'];
    $a_type = $row['a_type'];
    if ($a_type == 0) {
        $type = "非经销商发稿";
    } elseif ($a_type == 1) {
        $type = "经销商发稿";
    } elseif ($a_type == 2) {
        $type = "竞品攻击";
    } elseif ($a_type == 3) {
        $type = "非车主投诉";
    } elseif ($a_type == 4) {
        $type = "车主投诉";
    }
    $uk_id = $row['uk_id'];
    $query1 = "select k_id from user_keywords where uk_id=$uk_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $k_id = $row1['k_id'];
    $query1 = "select keyword from keyword where k_id=$k_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $keyword = $row1['keyword'];
    $article_property = $row['article_property'];
    if ($author_type == "all") {
        $query2 = "select * from zhidao_article where article_id=$article_id";
    } else {
        $query2 = "select * from zhidao_article where article_id=$article_id";
    }
    if ($media_type > 0) {
//
        $query2 .= " and article_grade = $media_type";

    }
    $res2 = mysql_query($query2);
    $x = mysql_num_rows($res2);
    if ($x == 0) {
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $media = $row2['media'];
    $array[$i]['order'] = $i;
    $array[$i]['brand'] = $keyword;
    $array[$i]['media'] = $media;
    $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_title']);
    $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_summary']);
    $array[$i]['link'] = $row2['article_url'];
    if ($order == 1) { //按发布时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
    } elseif ($order == 2) { //按采集时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
    }
    if ($article_property == 0) {
        $array[$i]['property'] = "中性";
    } elseif ($article_property == 1) {
        $array[$i]['property'] = "正面";
    } elseif ($article_property == 2) {
        $array[$i]['property'] = "负面";
    } elseif ($article_property == 3) {
        $array[$i]['property'] = "不良";
    }
    $array[$i]['a_type'] = $type;
    $i++;
}
//创建一个新的工作空间(sheet)
$obpe->createSheet();
$obpe->setactivesheetindex(6);
$objActSheet = $obpe->getActiveSheet();
$objActSheet->getDefaultRowDimension()->setRowHeight(15);
$objActSheet->setTitle('知道');
$blog_width_array = array('A' => 5, 'B' => 10, 'C' => 10, 'D' => 17, 'E' => 50, 'F' => 40, 'G' => 67, 'H' => 10, 'I' => 10);
foreach ($blog_width_array as $k => $v) {
    $objActSheet->getColumnDimension($k)->setWidth($v);
}
foreach ($color_array as $v) {
    $objStyle = $objActSheet->getStyle($v);
    $objFill = $objStyle->getFill();
    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objFill->getStartColor()->setARGB('CCCCCCCC');
}
//写入多行数据
foreach ($array as $k => $v) {
    $k = $k + 1;
    // @func 设置列
    $objActSheet->setcellvalue('A' . $k, excel_data($v['order']));
    $objActSheet->setcellvalue('B' . $k, excel_data($v['brand']));
    $objActSheet->setcellvalue('C' . $k, excel_data($v['media']));
    $objActSheet->setcellvalue('D' . $k, excel_data($v['time']));
    $objActSheet->setcellvalue('E' . $k, excel_data($v['title']));
    $objActSheet->setcellvalue('F' . $k, excel_data($v['summary']));
    $objActSheet->setcellvalue('G' . $k, excel_data($v['link']));
    $objActSheet->setcellvalue('H' . $k, excel_data($v['property']));
    $objActSheet->setcellvalue('I' . $k, excel_data($v['a_type']));
}
unset($array);


//app
if ($audit == "all" && $property == "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from app_key where uk_id in (" . $uk_ids . ") and status = 1 ";
} elseif ($audit == "all" && $property != "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from app_key where uk_id in (" . $uk_ids . ")  and article_property=$property and status = 1 ";
} elseif ($audit != "all" && $property == "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from app_key where uk_id in (" . $uk_ids . ") and audit_status=$audit and status = 1 ";
} elseif ($audit != "all" && $property != "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from app_key where uk_id in (" . $uk_ids . ")  and audit_status=$audit and article_property=$property and status = 1 ";
}
if ($order == 1) { //按发布时间
    $query .= " and article_pubtime >= $start_time and article_pubtime <= $end_time ";

    if ($quchong == 1) {//去重
        $query .= "group by article_id,uk_id ";
    }

    $query .= " order by article_pubtime desc";
} elseif ($order == 2) { //按采集时间
    $query .= " and article_addtime>=$start_time and article_addtime<=$end_time ";

    if ($quchong == 1) {//去重
        $query .= "group by article_id,uk_id ";
    }

    $query .= " order by article_addtime desc";
}

$res = mysql_query($query);
$array[0]['order'] = "序号";
$array[0]['brand'] = "品牌";
$array[0]['media'] = "媒体名称";
$array[0]['time'] = "时间";
$array[0]['title'] = "标题";
$array[0]['summary'] = "摘要";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['a_type'] = "文章类型";
$i = 1;
while ($row = mysql_fetch_array($res)) {
    $article_id = $row['article_id'];
    $id = $row['id'];
    $a_type = $row['a_type'];
    if ($a_type == 0) {
        $type = "非经销商发稿";
    } elseif ($a_type == 1) {
        $type = "经销商发稿";
    } elseif ($a_type == 2) {
        $type = "竞品攻击";
    } elseif ($a_type == 3) {
        $type = "非车主投诉";
    } elseif ($a_type == 4) {
        $type = "车主投诉";
    }
    $uk_id = $row['uk_id'];
    $query1 = "select k_id from user_keywords where uk_id=$uk_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $k_id = $row1['k_id'];
    $query1 = "select keyword from keyword where k_id=$k_id";
    $res1 = mysql_query($query1);
    $row1 = mysql_fetch_array($res1);
    $keyword = $row1['keyword'];
    $article_property = $row['article_property'];
    if ($author_type == "all") {
        $query2 = "select * from app_article where article_id=$article_id";
    } else {
        $query2 = "select * from app_article where article_id=$article_id";
    }
    if ($media_type > 0) {
//
        $query2 .= " and article_grade = $media_type";

    }
    $res2 = mysql_query($query2);
    $x = mysql_num_rows($res2);
    if ($x == 0) {
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $media = $row2['media'];
    $array[$i]['order'] = $i;
    $array[$i]['brand'] = $keyword;
    $array[$i]['media'] = $media;
    $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_title']);
    $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '',$row2['article_content']);
    $array[$i]['link'] = $row2['article_url'];
    if ($order == 1) { //按发布时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
    } elseif ($order == 2) { //按采集时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
    }
    if ($article_property == 0) {
        $array[$i]['property'] = "中性";
    } elseif ($article_property == 1) {
        $array[$i]['property'] = "正面";
    } elseif ($article_property == 2) {
        $array[$i]['property'] = "负面";
    } elseif ($article_property == 3) {
        $array[$i]['property'] = "不良";
    }
    $array[$i]['a_type'] = $type;
    $i++;
}
//创建一个新的工作空间(sheet)
$obpe->createSheet();
$obpe->setactivesheetindex(7);
$objActSheet = $obpe->getActiveSheet();
$objActSheet->getDefaultRowDimension()->setRowHeight(15);
$objActSheet->setTitle('APP');
$blog_width_array = array('A' => 5, 'B' => 10, 'C' => 10, 'D' => 17, 'E' => 50, 'F' => 40, 'G' => 67, 'H' => 10, 'I' => 10);
foreach ($blog_width_array as $k => $v) {
    $objActSheet->getColumnDimension($k)->setWidth($v);
}
foreach ($color_array as $v) {
    $objStyle = $objActSheet->getStyle($v);
    $objFill = $objStyle->getFill();
    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objFill->getStartColor()->setARGB('CCCCCCCC');
}
//写入多行数据
foreach ($array as $k => $v) {
    $k = $k + 1;
    // @func 设置列
    $objActSheet->setcellvalue('A' . $k, excel_data($v['order']));
    $objActSheet->setcellvalue('B' . $k, excel_data($v['brand']));
    $objActSheet->setcellvalue('C' . $k, excel_data($v['media']));
    $objActSheet->setcellvalue('D' . $k, excel_data($v['time']));
    $objActSheet->setcellvalue('E' . $k, excel_data($v['title']));
    $objActSheet->setcellvalue('F' . $k, excel_data($v['summary']));
    $objActSheet->setcellvalue('G' . $k, excel_data($v['link']));
    $objActSheet->setcellvalue('H' . $k, excel_data($v['property']));
    $objActSheet->setcellvalue('I' . $k, excel_data($v['a_type']));
}
unset($array);


//文件信息入库
$time = time();
$uk_ids = str_replace('0,', '', $uk_ids);
$insert = "insert into file_list(file_name,export_time,start_time,end_time,user_id,uk_id) values('$file_name',$time,$start_time,$end_time,$user_id,'$uk_ids')";
mysql_query($insert);
$id = mysql_insert_id();
//写入类容
$obwrite = PHPExcel_IOFactory::createWriter($obpe, 'Excel5');
//保存文件
if (!is_dir($user_id)) {
    mkdir($user_id);
}
$filename = $user_id . "/" . $id . ".xls";
//$download_filename=$user_id."/".iconv("utf8","gbk",$file_name).".xls";
//$download_filename=iconv("utf8","gbk",$file_name).".xls";
$download_filename = $file_name . ".xls";
//$download_filename="中国.xls";
$obwrite->save($filename);
//下载文件
header('Content-Type: application/force-download; charset=utf8');
//header("Content-Disposition: attachment;filename='".basename($download_filename)."'");
header("Content-Disposition: attachment;filename='" . $download_filename . "'");
readfile($filename);
exit;
?>