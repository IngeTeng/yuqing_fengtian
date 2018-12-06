<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/21
 * Time: 18:02
 */



require_once 'adminv/inc_dbconn.php';



$page = 1;

$limit = 1000;

$start = ($page-1)*$limit;

$sql = "select * from filter_list where filter_sync=0 limit {$start},{$limit}";


$res = mysql_query($sql);

while ($res) {

    print_r("正在处理第{$page}页\n");

    while ($row = mysql_fetch_array($res)) {


        $flag = 1;
        $time = time();
        switch ($row["filter_type"]) {
            case 1:


                $sql2 = "INSERT INTO news_article (article_title, article_url, article_content, article_pubtime, "
                    . "article_addtime, article_summary, article_comment, article_source, article_channel, media, article_author, article_is_repost,article_grade) "
                    . "VALUES ('{$row["filter_title"]}', '{$row["filter_url"]}', '{$row["filter_content"]}', {$row["filter_pubtime"]}, "
                    . "{$time}, '{$row["filter_content"]}', 0,'', '{$row["filter_media"]}', '{$row["filter_media"]}', '{$row["filter_author"]}', {$row["filter_is_repost"]}, {$row["filter_grade"]})";

                if (mysql_query($sql2)) {
                    $last_id = mysql_insert_id($db_connect);

                    if (!empty($row['filter_keyword'])) {
                        $keyword = $row['filter_keyword'];
                        // 查找当前关键词id
                        $query = "SELECT * FROM keyword WHERE keyword = '{$row['filter_keyword']}'";

                        $res = mysql_query($query);
                        while ($rows = mysql_fetch_array($res)) {
                            $k_id = $rows['k_id'];
                        }
                    }

                    $query = "SELECT * FROM user_keywords WHERE k_id = '$k_id'";
                    $res = mysql_query($query);
                    while ($rows = mysql_fetch_array($res)) {
                        // 在信息索引表中查找是否已经记录
                        $query = "SELECT id FROM news_key WHERE user_id = '{$rows['user_id']}' and article_id = '{$last_id}' and c_id = '{$rows['c_id']}'";
                        $res_key = mysql_query($query);
                        $res_key = mysql_fetch_array($res_key);
                        // 如果没有记录
                        if (empty($res_key)) {
                            $sql3 = "INSERT INTO news_key (uk_id, article_id, article_property, article_pubtime, a_type, user_id, article_addtime, c_id) "
                                . "VALUES ({$rows['uk_id']}, $last_id, {$row['filter_property']}, {$row['filter_pubtime']}, {$row['filter_atype']}, {$rows['user_id']}, $time, {$rows['c_id']})";

                            if (mysql_query($sql3)) {
                                $str = "success";
                                print_r($str);

                            } else {
                                $str = "error";
                                print_r($str);
                            }
                        }

                    }


                } else {
                    $str = "error";
                    print_r('链接已存在\n');

                    $flag = 2;

//                         print_r($str) ;
                }


                break;
            case 2 :
                $sql2 = "INSERT INTO bbs_article(article_url, article_title, article_content, article_pubtime,"
                    . " article_addtime, article_summary, article_reply, article_click, media, forum, article_author, article_is_repost,article_grade)"
                    . "VALUES ('{$row["filter_url"]}', '{$row["filter_title"]}', '{$row["filter_content"]}', {$row["filter_pubtime"]}, "
                    . "{$time}, '{$row["filter_content"]}', 0,0, '{$row["filter_media"]}','{$row["filter_media"]}', '{$row["filter_author"]}', {$row["filter_is_repost"]}, {$row["filter_grade"]})";

                if (mysql_query($sql2)) {
                    $last_id = mysql_insert_id($db_connect);

                    if (!empty($row['filter_keyword'])) {
                        $keyword = $row['filter_keyword'];
                        // 查找当前关键词id
                        $query = "SELECT * FROM keyword WHERE keyword = '{$row['filter_keyword']}'";

                        $res = mysql_query($query);
                        while ($rows = mysql_fetch_array($res)) {
                            $k_id = $rows['k_id'];
                        }
                    }

                    $query = "SELECT * FROM user_keywords WHERE k_id = '$k_id'";
                    $res = mysql_query($query);
                    while ($rows = mysql_fetch_array($res)) {
                        // 在信息索引表中查找是否已经记录
                        $query = "SELECT id FROM bbs_key WHERE user_id = '{$rows['user_id']}' and article_id = '{$last_id}' and c_id = '{$rows['c_id']}'";
                        $res_key = mysql_query($query);
                        $res_key = mysql_fetch_array($res_key);
                        // 如果没有记录
                        if (empty($res_key)) {
                            $sql3 = "INSERT INTO bbs_key (uk_id, article_id, article_property, article_pubtime, a_type, user_id, article_addtime, c_id) "
                                . "VALUES ({$rows['uk_id']}, $last_id, {$row['filter_property']}, {$row['filter_pubtime']}, {$row['filter_atype']}, {$rows['user_id']}, $time, {$rows['c_id']})";
                            if (mysql_query($sql3)) {
                                echo "success";

                            }
                        }

                    }


                } else {
                    $str = "error";
                    print_r('链接已存在\n');
                    $flag = 2;

                }
                break;
            case 3:
                $sql2 = "INSERT INTO blog_article (article_title, article_url, article_content, article_pubtime, "
                    . "article_addtime, article_summary, media, author, article_author, article_is_repost,article_grade) "
                    . "VALUES ('{$row["filter_title"]}', '{$row["filter_url"]}', '{$row["filter_content"]}', {$row["filter_pubtime"]}, "
                    . "{$time}, '{$row["filter_content"]}', '{$row["filter_media"]}', '{$row["filter_author"]}','{$row["filter_author"]}' {$row["filter_is_repost"]}, {$row["filter_grade"]})";

                if (mysql_query($sql2)) {
                    $last_id = mysql_insert_id($db_connect);

                    if (!empty($row['filter_keyword'])) {
                        $keyword = $row['filter_keyword'];
                        // 查找当前关键词id
                        $query = "SELECT * FROM keyword WHERE keyword = '{$row['filter_keyword']}'";

                        $res = mysql_query($query);
                        while ($rows = mysql_fetch_array($res)) {
                            $k_id = $rows['k_id'];
                        }
                    }

                    $query = "SELECT * FROM user_keywords WHERE k_id = '$k_id'";
                    $res = mysql_query($query);
                    while ($rows = mysql_fetch_array($res)) {
                        // 在信息索引表中查找是否已经记录
                        $query = "SELECT id FROM news_key WHERE user_id = '{$rows['user_id']}' and article_id = '{$last_id}' and c_id = '{$rows['c_id']}'";
                        $res_key = mysql_query($query);
                        $res_key = mysql_fetch_array($res_key);
                        // 如果没有记录
                        if (empty($res_key)) {
                            $sql3 = "INSERT INTO blog_key (uk_id, article_id, article_property, article_pubtime, a_type, user_id, article_addtime, c_id) "
                                . "VALUES ({$rows['uk_id']}, $last_id, {$row['filter_property']}, {$row['filter_pubtime']}, {$row['filter_atype']}, {$rows['user_id']}, $time, {$rows['c_id']})";
                            if (mysql_query($sql3)) {
                                echo "success";
                            }
                        }

                    }
                } else {
                    $str = "error";
                    print_r('链接已存在\n');
                    $flag = 2;

                }
                break;
            case 4:
                $sql2 = "INSERT INTO weibo_article (article_url, article_title, article_pubtime, article_addtime,"
                    . " article_comment, article_repost, author, isV, rz_info, fans, media, mid, article_author, article_is_repost,article_grade) "
                    . "VALUES ('{$row["filter_url"]}', '{$row["filter_title"]}', '{$row["filter_pubtime"]}', {$time}, "
                    . "0, 0,{$row["filter_author"]},0, '',0, '{$row["filter_media"]}','', '{$row["filter_author"]}', {$row["filter_is_repost"]}, {$row["filter_grade"]})";

                if (mysql_query($sql2)) {
                    $last_id = mysql_insert_id($db_connect);

                    if (!empty($row['filter_keyword'])) {
                        $keyword = $row['filter_keyword'];
                        // 查找当前关键词id
                        $query = "SELECT * FROM keyword WHERE keyword = '{$row['filter_keyword']}'";

                        $res = mysql_query($query);
                        while ($rows = mysql_fetch_array($res)) {
                            $k_id = $rows['k_id'];
                        }
                    }

                    $query = "SELECT * FROM user_keywords WHERE k_id = '$k_id'";
                    $res = mysql_query($query);
                    while ($rows = mysql_fetch_array($res)) {
                        // 在信息索引表中查找是否已经记录
                        $query = "SELECT id FROM weibo_key WHERE user_id = '{$rows['user_id']}' and article_id = '{$last_id}' and c_id = '{$rows['c_id']}'";
                        $res_key = mysql_query($query);
                        $res_key = mysql_fetch_array($res_key);
                        // 如果没有记录
                        if (empty($res_key)) {
                            $sql3 = "INSERT INTO weibo_key (uk_id, article_id, article_property, article_pubtime, a_type, user_id, article_addtime, c_id) "
                                . "VALUES ({$rows['uk_id']}, $last_id, {$row['filter_property']}, {$row['filter_pubtime']}, {$row['filter_atype']}, {$rows['user_id']}, $time, {$rows['c_id']})";
                            if (mysql_query($sql3)) {
                                echo "success";
                            }
                        }

                    }
                } else {
                    $str = "error";
                    print_r('链接已存在\n');
                    $flag = 2;
                }
                break;
            case 5:
                $sql2 = "INSERT INTO video_article(article_url, article_title, article_pubtime,"
                    . " article_addtime, article_summary, media, article_author, article_is_repost, article_grade)"
                    . "VALUES ('{$row["filter_url"]}', '{$row["filter_title"]}',  {$row["filter_pubtime"]}, "
                    . "{$time}, '{$row["filter_content"]}', '{$row["filter_media"]}', '{$row["filter_media"]}', '{$row["filter_author"]}', {$row["filter_is_repost"]}, {$row["filter_grade"]})";

                if (mysql_query($sql2)) {
                    $last_id = mysql_insert_id($db_connect);

                    if (!empty($row['filter_keyword'])) {
                        $keyword = $row['filter_keyword'];
                        // 查找当前关键词id
                        $query = "SELECT * FROM keyword WHERE keyword = '{$row['filter_keyword']}'";

                        $res = mysql_query($query);
                        while ($rows = mysql_fetch_array($res)) {
                            $k_id = $rows['k_id'];
                        }
                    }

                    $query = "SELECT * FROM user_keywords WHERE k_id = '$k_id'";
                    $res = mysql_query($query);
                    while ($rows = mysql_fetch_array($res)) {
                        // 在信息索引表中查找是否已经记录
                        $query = "SELECT id FROM video_key WHERE user_id = '{$rows['user_id']}' and article_id = '{$last_id}' and c_id = '{$rows['c_id']}'";
                        $res_key = mysql_query($query);
                        $res_key = mysql_fetch_array($res_key);
                        // 如果没有记录
                        if (empty($res_key)) {
                            $sql3 = "INSERT INTO video_key (uk_id, article_id, article_property, article_pubtime, a_type, user_id, article_addtime, c_id) "
                                . "VALUES ({$rows['uk_id']}, $last_id, {$row['filter_property']}, {$row['filter_pubtime']}, {$row['filter_atype']}, {$rows['user_id']}, $time, {$rows['c_id']})";
                            if (mysql_query($sql3)) {
                                echo "success";
                            }

                        }

                    }

                } else {
                    $str = "error";
                    print_r('链接已存在\n');
                    $flag = 2;
                }
                break;
            case 6:
                $sql2 = "INSERT INTO weixin_article (article_url, article_title, article_summary, "
                    . "article_pubtime, article_addtime, author, media, sign, read_num, like_num  ,article_author, article_is_repost,article_grade) "
                    . "VALUES ('{$row["filter_url"]}', '{$row["filter_title"]}', '{$row["filter_content"]}', {$row["filter_pubtime"]}, "
                    . "{$time}, '{$row["filter_author"]}', '{$row["filter_media"]}','',0,0, '{$row["filter_author"]}', {$row["filter_is_repost"]}, {$row["filter_grade"]})";

                if (mysql_query($sql2)) {
                    $last_id = mysql_insert_id($db_connect);

                    if (!empty($row['filter_keyword'])) {
                        $keyword = $row['filter_keyword'];
                        // 查找当前关键词id
                        $query = "SELECT * FROM keyword WHERE keyword = '{$row['filter_keyword']}'";

                        $res = mysql_query($query);
                        while ($rows = mysql_fetch_array($res)) {
                            $k_id = $rows['k_id'];
                        }
                    }

                    $query = "SELECT * FROM user_keywords WHERE k_id = '$k_id'";
                    $res = mysql_query($query);
                    while ($rows = mysql_fetch_array($res)) {
                        // 在信息索引表中查找是否已经记录
                        $query = "SELECT id FROM weixin_key WHERE user_id = '{$rows['user_id']}' and article_id = '{$last_id}' and c_id = '{$rows['c_id']}'";
                        $res_key = mysql_query($query);
                        $res_key = mysql_fetch_array($res_key);
                        // 如果没有记录
                        if (empty($res_key)) {
                            $sql3 = "INSERT INTO weixin_key (uk_id, article_id, article_property, article_pubtime, a_type, user_id, article_addtime, c_id) "
                                . "VALUES ({$rows['uk_id']}, $last_id, {$row['filter_property']}, {$row['filter_pubtime']}, {$row['filter_atype']}, {$rows['user_id']}, $time, {$rows['c_id']})";
                            if (mysql_query($sql3)) {
                                echo "success";
                            }

                        }

                    }

                } else {
                    $str = "error";
                    print_r('链接已存在\n');
                    $flag = 2;
                }
                break;
            case 7:
                $sql2 = "INSERT INTO zhidao_article(article_url, article_title, article_pubtime,"
                    . " article_addtime, article_summary, media, author, mid, article_author, article_is_repost,article_grade)"
                    . "VALUES ('{$row["filter_url"]}', '{$row["filter_title"]}',  {$row["filter_pubtime"]}, "
                    . "{$time}, '{$row["filter_content"]}', '{$row["filter_media"]}', '{$row["filter_author"]}','', '{$row["filter_author"]}', {$row["filter_is_repost"]}, {$row["filter_grade"]})";

                if (mysql_query($sql2)) {
                    $last_id = mysql_insert_id($db_connect);

                    if (!empty($row['filter_keyword'])) {
                        $keyword = $row['filter_keyword'];
                        // 查找当前关键词id
                        $query = "SELECT * FROM keyword WHERE keyword = '{$row['filter_keyword']}'";

                        $res = mysql_query($query);
                        while ($rows = mysql_fetch_array($res)) {
                            $k_id = $rows['k_id'];
                        }
                    }

                    $query = "SELECT * FROM user_keywords WHERE k_id = '$k_id'";
                    $res = mysql_query($query);
                    while ($rows = mysql_fetch_array($res)) {
                        // 在信息索引表中查找是否已经记录
                        $query = "SELECT id FROM zhidao_key WHERE user_id = '{$rows['user_id']}' and article_id = '{$last_id}' and c_id = '{$rows['c_id']}'";
                        $res_key = mysql_query($query);
                        $res_key = mysql_fetch_array($res_key);
                        // 如果没有记录
                        if (empty($res_key)) {
                            $sql3 = "INSERT INTO zhidao_key (uk_id, article_id, article_property, article_pubtime, a_type, user_id, article_addtime, c_id) "
                                . "VALUES ({$rows['uk_id']}, $last_id, {$row['filter_property']}, {$row['filter_pubtime']}, {$row['filter_atype']}, {$rows['user_id']}, $time, {$rows['c_id']})";
                            if (mysql_query($sql3)) {
                                echo "success";
                            }

                        }

                    }

                } else {
                    $str = "error";
                    print_r('链接已存在\n');
                    $flag = 2;
                }
                break;

            case 8:
                $sql2 = "INSERT INTO app_article (article_url, article_title, article_content, article_pubtime,"
                    . " article_addtime, media, article_summary, article_channel, article_author, article_is_repost,article_grade) "
                    . "VALUES ('{$row["filter_url"]}', '{$row["filter_title"]}', '{$row["filter_content"]}', {$row["filter_pubtime"]}, "
                    . "{$time}, '{$row["filter_media"]}','{$row["filter_content"]}','', '{$row["filter_author"]}', {$row["filter_is_repost"]}, {$row["filter_grade"]})";

                if (mysql_query($sql2)) {
                    $last_id = mysql_insert_id($db_connect);

                    if (!empty($row['filter_keyword'])) {
                        $keyword = $row['filter_keyword'];
                        // 查找当前关键词id
                        $query = "SELECT * FROM keyword WHERE keyword = '{$row['filter_keyword']}'";

                        $res = mysql_query($query);
                        while ($rows = mysql_fetch_array($res)) {
                            $k_id = $rows['k_id'];
                        }
                    }

                    $query = "SELECT * FROM user_keywords WHERE k_id = '$k_id'";
                    $res = mysql_query($query);
                    while ($rows = mysql_fetch_array($res)) {
                        // 在信息索引表中查找是否已经记录
                        $query = "SELECT id FROM app_key WHERE user_id = '{$rows['user_id']}' and article_id = '{$last_id}' and c_id = '{$rows['c_id']}'";
                        $res_key = mysql_query($query);
                        $res_key = mysql_fetch_array($res_key);
                        // 如果没有记录
                        if (empty($res_key)) {
                            $sql3 = "INSERT INTO app_key (uk_id, article_id, article_property, article_pubtime, a_type, user_id, article_addtime, c_id) "
                                . "VALUES ({$rows['uk_id']}, $last_id, {$row['filter_property']}, {$row['filter_pubtime']}, {$row['filter_atype']}, {$rows['user_id']}, $time, {$rows['c_id']})";
                            if (mysql_query($sql3)) {
                                echo "success";
                            }

                        }

                    }


                } else {
                    $str = "error";
                    print_r('链接已存在\n');
                    $flag = 2;
                }
                break;



        }

        $updateSql = "update filter_list set filter_sync={$flag} where filter_id={$row["filter_id"]}";

        mysql_query($updateSql);
        $page++;
        $start = ($page - 1) * $limit;

        $sql = "select * from filter_list where filter_sync=0 limit {$start},{$limit}";

        $res = mysql_query($sql);

    }


//
//
//$type = $_REQUEST['type'];
//
//$page = $_REQUEST['page'];
//
//
//switch($type){
//    case 1://mews*
//
//        $sql = "select * from filter_list where filter_type = 1 order by filter_id limit ".($page*1000).",1000 ";
//        $res = mysql_query($sql);

//        break;
//    case 2://bbs
//        $time=time();
//        $sql = "select * from filter_list where filter_type = 2 order by filter_id limit ".($page*1000).",1000 ";
//
//        $res = mysql_query($sql);
//        //                                echo $sql;
//        while ($row = mysql_fetch_array($res)) {
//
////
////
//        }
////
//        break;
//    case 3://blob
//        $time=time();
//        $sql = "select * from filter_list where filter_type = 3 order by filter_id limit ".($page*1000).",1000 ";
//
//        $res = mysql_query($sql);
//        //                                echo $sql;
//        while ($row = mysql_fetch_array($res)) {
//
//        }
////
//        break;
//    case 4://weibo
//        $time=time();
//        $sql = "select * from filter_list where filter_type = 4 order by filter_id limit ".($page*1000).",1000 ";
//
//        $res = mysql_query($sql);
//        //                                echo $sql;
//        while ($row = mysql_fetch_array($res)) {
//
//        }

//        break;
//    case 5://video
//        $time=time();
//        $sql = "select * from filter_list where filter_type = 5 order by filter_id limit ".($page*1000).",1000 ";
//
//        $res = mysql_query($sql);
//        //                                echo $sql;
//        while ($row = mysql_fetch_array($res)) {
//
//
//
//        }

//        break;
//    case 6://weiixn
//        $time=time();
//        $sql = "select * from filter_list where filter_type = 6 order by filter_id limit ".($page*1000).",1000 ";
//
//        $res = mysql_query($sql);
//        //                                echo $sql;
//        while ($row = mysql_fetch_array($res)) {
//
//
//
//        }

//    break;
//    case 7://zhidao
//        $time=time();
//        $sql = "select * from filter_list where filter_type = 7 order by filter_id limit ".($page*1000).",1000 ";
//
//        $res = mysql_query($sql);
//        //                                echo $sql;
//        while ($row = mysql_fetch_array($res)) {
//
//
//
//        }

//        break;
//    case 8://app
//        $time=time();
//        $sql = "select * from filter_list where filter_type = 8 order by filter_id limit ".($page*1000).",1000 ";
//
//        $res = mysql_query($sql);
//        //                                echo $sql;
//        while ($row = mysql_fetch_array($res)) {
//
//            }


//        break;
//    $page=$page+1;
//        echo "<script>
//    window.location.href='http://47.92.204.34/yuqing/classification_data.php?type={$type}&page={$page}'
//</script>
//";
//
//
//}
//
//
//
//
//
////var_dump($arr);
//
//
//
}?>
