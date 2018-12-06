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
$uk_ids = $_POST['uk_ids'];
$order = $_POST['order'];//排序方式，发布时间1或采集时间2
$quchong = $_POST['quchong'];//是否去重 0 不去重 1 去重
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

$query = "select article_id,article_property,id,uk_id,a_type from news_key where uk_id in (" . $uk_ids . ")  and status = 1 ";
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
/*('序号', '车型','媒体类型','媒体名称','报道日期','标题/链接','摘要','连接', '调性','所在频道', '原发/转载','信息类别/文章类型'))*/
$res = mysql_query($query);

$array[0]['order'] = "序号";
$array[0]['brand'] = "车型";
$array[0]['media_type'] = "媒体类型";
$array[0]['media'] = "媒体名称";
$array[0]['time'] = "报道日期";
if($order!=1)
{
    $array[0]['time'] = "采集日期";

}
$array[0]['title'] = "标题";
$array[0]['summary'] = "摘要";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['channel'] = "作者";
$array[0]['yuanfa'] = "原发/转载";
$array[0]['a_type'] = "文章类型";

$yuanfa = array(
    '0' => '原创',
    '1' => '转载'
);

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
    $x = mysql_num_rows($res2);
    if ($x == 0) {
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $article_title = $row2['article_title'];
    $article_summary = $row2['article_content'];
    if (filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)) {
        //$media = $row2['media'];
        $array[$i]['order'] = $i;
        $array[$i]['brand'] = $keyword;
        if(preg_match('/微博/s',$row2['media']))
        {
            $array[$i]['media_type'] = "微博";
        }else{
            $array[$i]['media_type'] = "新闻";
        }

        $array[$i]['media'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['media']);


//        $array[$i]['media'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['media']);


        $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_title']);
        $array[$i]['link'] = $row2['article_url'];
        if ($order == 1) { //按发布时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
        } elseif ($order == 2) { //按采集时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
        }
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
        $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_content']);
        $array[$i]['channel'] = $row2['author'];
//        $array[$i]['channel'] = $row2['author'];
//        $array[$i]['yuanfa'] = $yuanfa[$row2['article_is_repost']];
        $array[$i]['yuanfa'] = $yuanfa[$row2['article_is_repost']];
        $array[$i]['a_type'] = $type;
        $i++;
    }
}

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
    $query .= " group by article_id,uk_id ";
}

if ($order == 1) { //按发布时间
    $query .= " order by article_pubtime desc";
} elseif ($order == 2) { //按采集时间
    $query .= "  order by article_addtime desc";
}
$res = mysql_query($query);
$array[0]['order'] = "序号";
$array[0]['brand'] = "车型";
$array[0]['media_type'] = "媒体类型";
$array[0]['media'] = "媒体名称";
$array[0]['time'] = "报道日期";
if($order!=1)
{
    $array[0]['time'] = "采集日期";

}
$array[0]['title'] = "标题";
$array[0]['summary'] = "摘要";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['channel'] = "作者";
$array[0]['yuanfa'] = "原发/转载";
$array[0]['a_type'] = "文章类型";

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
    $x = mysql_num_rows($res2);
    if ($x == 0) {
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $article_title = $row2['article_title'];
    $article_summary = $row2['article_content'];
    if (filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)) {
        //$media = $row2['media'];
        $array[$i]['order'] = $i;
        $array[$i]['brand'] = $keyword;
        $array[$i]['media_type'] = "视频";
        $array[$i]['media'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['media']);
        $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_title']);
        $array[$i]['link'] = $row2['article_url'];
        if ($order == 1) { //按发布时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
        } elseif ($order == 2) { //按采集时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
        }
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
        $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_summary']);
        $array[$i]['channel'] = $row2['author'];
        $array[$i]['yuanfa'] = $yuanfa[$row2['article_is_repost']];
        $array[$i]['a_type'] = $type;
        $i++;
    }
}

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
    $query .= " order by article_pubtime desc";
} elseif ($order == 2) { //按采集时间
    $query .= "  order by article_addtime desc";
}
$res = mysql_query($query);
$array[0]['order'] = "序号";
$array[0]['brand'] = "车型";
$array[0]['media_type'] = "媒体类型";
$array[0]['media'] = "媒体名称";
$array[0]['time'] = "报道日期";
if($order!=1)
{
    $array[0]['time'] = "采集日期";

}
$array[0]['title'] = "标题";
$array[0]['summary'] = "摘要";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['channel'] = "作者";
$array[0]['yuanfa'] = "原发/转载";
$array[0]['a_type'] = "文章类型";

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
    $x = mysql_num_rows($res2);
    if ($x == 0) {
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $article_title = $row2['article_title'];
    $article_summary = $row2['article_content'];
    if (filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)) {
        $array[$i]['order'] = $i;
        $array[$i]['brand'] = $keyword;
        $array[$i]['media_type'] = "论坛";
        $array[$i]['media'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['media']);
        $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_title']);
        $array[$i]['link'] = $row2['article_url'];
        if ($order == 1) { //按发布时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
        } elseif ($order == 2) { //按采集时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
        }
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
        $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_content']);
        $array[$i]['channel'] = $row2['author'];
        $array[$i]['yuanfa'] = $yuanfa[$row2['article_is_repost']];
        $array[$i]['a_type'] = $type;
        $i++;
    }
}

$query = "select article_id,article_property,id,uk_id,a_type from blog_key where uk_id in (" . $uk_ids . ")  and status = 1 ";
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
$array[0]['brand'] = "车型";
$array[0]['media_type'] = "媒体类型";
$array[0]['media'] = "媒体名称";
$array[0]['time'] = "报道日期";
if($order!=1)
{
    $array[0]['time'] = "采集日期";

}
$array[0]['title'] = "标题";
$array[0]['summary'] = "摘要";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['channel'] = "作者";
$array[0]['yuanfa'] = "原发/转载";
$array[0]['a_type'] = "文章类型";

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
    $x = mysql_num_rows($res2);
    if ($x == 0) {
        continue;
    }
    $row2 = mysql_fetch_array($res2);
    $article_title = $row2['article_title'];
    $article_summary = $row2['article_content'];
    if (filter($filter_words, $filter_place, $filter_type, $article_title, $article_summary)) {
        $array[$i]['order'] = $i;
        $array[$i]['brand'] = $keyword;
        $array[$i]['media_type'] = "博客";
        $array[$i]['media'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['media']);
        $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_title']);
        $array[$i]['link'] = $row2['article_url'];
        if ($order == 1) { //按发布时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
        } elseif ($order == 2) { //按采集时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
        }
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
        $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_content']);
        $array[$i]['channel'] = $row2['author'];
        $array[$i]['yuanfa'] = $yuanfa[$row2['article_is_repost']];
        $array[$i]['a_type'] = $type;
        $i++;


    }
}

$query = "select article_id,article_property,id,uk_id,a_type from weibo_key where uk_id in (" . $uk_ids . ")   and status = 1 ";
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
$array[0]['brand'] = "车型";
$array[0]['media_type'] = "媒体类型";
$array[0]['media'] = "媒体名称";
$array[0]['time'] = "报道日期";
if($order!=1)
{
    $array[0]['time'] = "采集日期";

}
$array[0]['title'] = "标题";
$array[0]['summary'] = "摘要";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['channel'] = "作者";
$array[0]['yuanfa'] = "原发/转载";
$array[0]['a_type'] = "文章类型";

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
    $article_title = $row2['article_title'];
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
        $array[$i]['order'] = $i;
        $array[$i]['brand'] = $keyword;
        $array[$i]['media_type'] = "微博";
        $array[$i]['media'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['media']);
        $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_title']);
        $array[$i]['link'] = $row2['article_url'];
        if ($order == 1) { //按发布时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
        } elseif ($order == 2) { //按采集时间
            $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
        }
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
        $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_content']);
        $array[$i]['channel'] = $row2['author'];
        $array[$i]['yuanfa'] = $yuanfa[$row2['article_is_repost']];
        $array[$i]['a_type'] = $type;
        $i++;
    }
}


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
$array[0]['brand'] = "车型";
$array[0]['media_type'] = "媒体类型";
$array[0]['media'] = "媒体名称";
$array[0]['time'] = "报道日期";
if($order!=1)
{
    $array[0]['time'] = "采集日期";

}
$array[0]['title'] = "标题";
$array[0]['summary'] = "摘要";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['channel'] = "作者";
$array[0]['yuanfa'] = "原发/转载";
$array[0]['a_type'] = "文章类型";

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
    $array[$i]['order'] = $i;
    $array[$i]['brand'] = $keyword;
    $array[$i]['media_type'] = "微信";
    $array[$i]['media'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['media']);
    $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_title']);
    $array[$i]['link'] = $row2['article_url'];
    if ($order == 1) { //按发布时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
    } elseif ($order == 2) { //按采集时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
    }
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
    $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_summary']);
    $array[$i]['channel'] = $row2['author'];
    $array[$i]['yuanfa'] = $yuanfa[$row2['article_is_repost']];
    $array[$i]['a_type'] = $type;
    $i++;
}

if ($audit == "all" && $property == "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from zhidao_key where uk_id in (" . $uk_ids . ")  and status = 1 ";
} elseif ($audit == "all" && $property != "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from zhidao_key where uk_id in (" . $uk_ids . ")  and article_property=$property and status = 1 ";
} elseif ($audit != "all" && $property == "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from zhidao_key where uk_id in (" . $uk_ids . ")  and audit_status=$audit and status = 1 ";
} elseif ($audit != "all" && $property != "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from zhidao_key where uk_id in (" . $uk_ids . ")  and audit_status=$audit and article_property=$property and status = 1 ";
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
$array[0]['brand'] = "车型";
$array[0]['media_type'] = "媒体类型";
$array[0]['media'] = "媒体名称";
$array[0]['time'] = "报道日期";
if($order!=1)
{
    $array[0]['time'] = "采集日期";

}
$array[0]['title'] = "标题";
$array[0]['summary'] = "摘要";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['channel'] = "作者";
$array[0]['yuanfa'] = "原发/转载";
$array[0]['a_type'] = "文章类型";

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
    $array[$i]['order'] = $i;
    $array[$i]['brand'] = $keyword;
    $array[$i]['media_type'] = "知道";
    $array[$i]['media'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['media']);
    $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_title']);
    $array[$i]['link'] = $row2['article_url'];
    if ($order == 1) { //按发布时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
    } elseif ($order == 2) { //按采集时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
    }
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
    $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_summary']);
    $array[$i]['channel'] = $row2['author'];
    $array[$i]['yuanfa'] = $yuanfa[$row2['article_is_repost']];
    $array[$i]['a_type'] = $type;
    $i++;
}

//app
if ($audit == "all" && $property == "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from app_key where uk_id in (" . $uk_ids . ")  and status = 1 ";
} elseif ($audit == "all" && $property != "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from app_key where uk_id in (" . $uk_ids . ")  and article_property=$property and status = 1 ";
} elseif ($audit != "all" && $property == "all") {
    $query = "select article_id,article_property,id,uk_id,a_type from app_key where uk_id in (" . $uk_ids . ")  and audit_status=$audit and status = 1 ";
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
$array[0]['brand'] = "车型";
$array[0]['media_type'] = "媒体类型";
$array[0]['media'] = "媒体名称";
$array[0]['time'] = "报道日期";
if($order!=1)
{
    $array[0]['time'] = "采集日期";

}
$array[0]['title'] = "标题";
$array[0]['summary'] = "摘要";
$array[0]['link'] = "链接";
$array[0]['property'] = "调性";
$array[0]['channel'] = "作者";
$array[0]['yuanfa'] = "原发/转载";
$array[0]['a_type'] = "文章类型";
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
    $array[$i]['order'] = $i;
    $array[$i]['brand'] = $keyword;
    $array[$i]['media_type'] = "app";
    $array[$i]['media'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['media']);
    $array[$i]['title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_title']);
    $array[$i]['link'] = $row2['article_url'];
    if ($order == 1) { //按发布时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_pubtime']);
    } elseif ($order == 2) { //按采集时间
        $array[$i]['time'] = date('Y-m-d H:i', $row2['article_addtime']);
    }
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
    $array[$i]['summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row2['article_content']);
    $array[$i]['channel'] = $row2['author'];
    $array[$i]['yuanfa'] = $yuanfa[$row2['article_is_repost']];
    $array[$i]['a_type'] = $type;
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

//所有单元格居中
//$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
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
$objActSheet->getStyle('A1:L1')->getFont()->setBold(true);//标题用粗体

$objActSheet->setTitle('数据源');
$width_array = array('A' => 5, 'B' => 10, 'C' => 10, 'D' => 10, 'E' => 17, 'F' => 50, 'G' => 11, 'H' => 67, 'I' => 10, 'J' => 8, 'K' => 30, 'L' => 15);
foreach ($width_array as $k => $v) {
    $objActSheet->getColumnDimension($k)->setWidth($v);
}
$color_array = array("A1", "B1", "C1", "D1", "E1", "F1", "G1", "H1", "I1", "J1", "K1", "L1");
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
    $objActSheet->setcellvalue('C' . $k, excel_data($v['media_type']));
    $objActSheet->setcellvalue('D' . $k, excel_data($v['media']));
    $objActSheet->setcellvalue('E' . $k, excel_data($v['time']));
    $objActSheet->setcellvalue('F' . $k, excel_data($v['title']));
    $objActSheet->setcellvalue('G' . $k, excel_data($v['summary']));
    $objActSheet->setcellvalue('H' . $k, excel_data($v['link']));
    $objActSheet->setcellvalue('I' . $k, excel_data($v['property']));
    $objActSheet->setcellvalue('J' . $k, excel_data($v['channel']));
    $objActSheet->setcellvalue('K' . $k, excel_data($v['yuanfa']));
    $objActSheet->setcellvalue('L' . $k, excel_data($v['a_type']));
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