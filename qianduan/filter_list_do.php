<?php
/**
 * Created by PhpStorm.
 * User: 张鑫
 * Date: 2018/10/24
 * Time: 0:39
 */
error_reporting(0);
require_once('adminv/inc_dbconn.php');
require_once('Env.php');
function json_encode_cn($array){
    $str = json_encode($array);
    $os  = Env::getOSType();
    if($os == 'windows')
        $ucs = 'UCS-2';
    else
        $ucs = 'UCS-2BE';

    if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
        $str = preg_replace_callback("/\\\\u([0-9a-f]{4})/i", create_function('$matches', 'return iconv("'.$ucs.'", "UTF-8", pack("H*", $matches[1]));'), $str);
    }else{
        $str = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('".$ucs."', 'UTF-8', pack('H4', '\\1'))", $str);
    }
    return $str;
}

/**
 * 操作响应通知(默认json格式)
 *
 * @param $msg  消息内容
 * @param $code 消息代码
 * @return
 */
function action_msg($msg, $code, $json = true){
    $r = array(
        'code' => $code,
        'msg'  => $msg
    );
    if($json)
        return json_encode_cn($r);
    else
        return $r;
}
$act=$_GET['act'];
switch ($act){
    case 'add': //添加问题
        $type = $_POST['type'];
        $id = $_POST['id'];
        $url=$_POST['url'];
        switch ($type){
            case 1://news
                $sql="select * from news_article where article_url = '{$url}'";

                $res = mysql_query($sql);
                while ($rows = mysql_fetch_array($res)) {
                    $article_id = $rows['article_id'];
                }
                if($article_id){
                    $sql2="delete from news_key where article_id= {$article_id}";
                    if (mysql_query($sql2)) {
                        $sql3="delete from news_article where article_id= {$article_id}";
                        if (mysql_query($sql3)) {
                            $sql4="update filter_list set filter_status=0  WHERE filter_id =$id";
                            if (mysql_query($sql4)) {
                                echo action_msg("success", 1);
                            }else{
                                echo action_msg("error", 0);
                            }
                        }else{
                            echo action_msg("error", 0);
                        }

                    }else{
                        echo action_msg("error", 0);
                    }

                }else{
                    echo action_msg("文章不存在", 0);
                }
                break;
            case 2:
                $sql="select * from bbs_article where article_url = '{$url}'";

                $res = mysql_query($sql);
                while ($rows = mysql_fetch_array($res)) {
                    $article_id = $rows['article_id'];
                }
                if($article_id){
                    $sql2="delete from bbs_key where article_id= {$article_id}";
                    if (mysql_query($sql2)) {
                        $sql3="delete from bbs_article where article_id= {$article_id}";
                        if (mysql_query($sql3)) {
                            $sql4="update filter_list set filter_status=0  WHERE filter_id =$id";
                            if (mysql_query($sql4)) {
                                echo action_msg("success", 1);
                            }else{
                                echo action_msg("error", 0);
                            }
                        }else{
                            echo action_msg("error", 0);
                        }

                    }else{
                        echo action_msg("error", 0);
                    }

                }else{
                    echo action_msg("文章不存在", 0);
                }
                break;
            case 3:
                $sql="select * from blog_article where article_url = '{$url}'";

                $res = mysql_query($sql);
                while ($rows = mysql_fetch_array($res)) {
                    $article_id = $rows['article_id'];
                }
                if($article_id){
                    $sql2="delete from blog_key where article_id= {$article_id}";
                    if (mysql_query($sql2)) {
                        $sql3="delete from blog_article where article_id= {$article_id}";
                        if (mysql_query($sql3)) {
                            $sql4="update filter_list set filter_status=0  WHERE filter_id =$id";
                            if (mysql_query($sql4)) {
                                echo action_msg("success", 1);
                            }else{
                                echo action_msg("error", 0);
                            }
                        }else{
                            echo action_msg("error", 0);
                        }

                    }else{
                        echo action_msg("error", 0);
                    }

                }else{
                    echo action_msg("文章不存在", 0);
                }
                break;
            case 4:
                $sql="select * from weibo_article where article_url = '{$url}'";

                $res = mysql_query($sql);
                while ($rows = mysql_fetch_array($res)) {
                    $article_id = $rows['article_id'];
                }
                if($article_id){
                    $sql2="delete from weibo_key where article_id= {$article_id}";
                    if (mysql_query($sql2)) {
                        $sql3="delete from weibo_article where article_id= {$article_id}";
                        if (mysql_query($sql3)) {
                            $sql4="update filter_list set filter_status=0  WHERE filter_id =$id";
                            if (mysql_query($sql4)) {
                                echo action_msg("success", 1);
                            }else{
                                echo action_msg("error", 0);
                            }
                        }else{
                            echo action_msg("error", 0);
                        }

                    }else{
                        echo action_msg("error", 0);
                    }

                }else{
                    echo action_msg("文章不存在", 0);
                }
                break;
            case 5:
                $sql="select * from video_article where article_url = '{$url}'";

                $res = mysql_query($sql);
                while ($rows = mysql_fetch_array($res)) {
                    $article_id = $rows['article_id'];
                }
                if($article_id){
                    $sql2="delete from video_key where article_id= {$article_id}";
                    if (mysql_query($sql2)) {
                        $sql3="delete from video_article where article_id= {$article_id}";
                        if (mysql_query($sql3)) {
                            $sql4="update filter_list set filter_status=0  WHERE filter_id =$id";
                            if (mysql_query($sql4)) {
                                echo action_msg("success", 1);
                            }else{
                                echo action_msg("error", 0);
                            }
                        }else{
                            echo action_msg("error", 0);
                        }

                    }else{
                        echo action_msg("error", 0);
                    }

                }else{
                    echo action_msg("文章不存在", 0);
                }
                break;
            case 6:
                $sql="select * from weixin_article where article_url = '{$url}'";

                $res = mysql_query($sql);
                while ($rows = mysql_fetch_array($res)) {
                    $article_id = $rows['article_id'];
                }
                if($article_id){
                    $sql2="delete from weixin_key where article_id= {$article_id}";
                    if (mysql_query($sql2)) {
                        $sql3="delete from weixin_article where article_id= {$article_id}";
                        if (mysql_query($sql3)) {
                            $sql4="update filter_list set filter_status=0  WHERE filter_id =$id";
                            if (mysql_query($sql4)) {
                                echo action_msg("success", 1);
                            }else{
                                echo action_msg("error", 0);
                            }
                        }else{
                            echo action_msg("error", 0);
                        }

                    }else{
                        echo action_msg("error", 0);
                    }

                }else{
                    echo action_msg("文章不存在", 0);
                }
                break;
            case 7:
                $sql="select * from zhidao_article where article_url = '{$url}'";

                $res = mysql_query($sql);
                while ($rows = mysql_fetch_array($res)) {
                    $article_id = $rows['article_id'];
                }
                if($article_id){
                    $sql2="delete from zhidao_key where article_id= {$article_id}";
                    if (mysql_query($sql2)) {
                        $sql3="delete from zhidao_article where article_id= {$article_id}";
                        if (mysql_query($sql3)) {
                            $sql4="update filter_list set filter_status=0  WHERE filter_id =$id";
                            if (mysql_query($sql4)) {
                                echo action_msg("success", 1);
                            }else{
                                echo action_msg("error", 0);
                            }
                        }else{
                            echo action_msg("error", 0);
                        }

                    }else{
                        echo action_msg("error", 0);
                    }

                }else{
                    echo action_msg("文章不存在", 0);
                }
                break;
            case 8:
                $sql="select * from app_article where article_url = '{$url}'";

                $res = mysql_query($sql);
                while ($rows = mysql_fetch_array($res)) {
                    $article_id = $rows['article_id'];
                }
                if($article_id){
                    $sql2="delete from app_key where article_id= {$article_id}";
                    if (mysql_query($sql2)) {
                        $sql3="delete from app_article where article_id= {$article_id}";
                        if (mysql_query($sql3)) {
                            $sql4="update filter_list set filter_status=0  WHERE filter_id =$id";
                            if (mysql_query($sql4)) {
                                echo action_msg("success", 1);
                            }else{
                                echo action_msg("error", 0);
                            }
                        }else{
                            echo action_msg("error", 0);
                        }

                    }else{
                        echo action_msg("error", 0);
                    }

                }else{
                    echo action_msg("文章不存在", 0);
                }
                break;

        }
        break;

    case 'del': //删除问题
        $id = $_POST['id'];

        $sql = "update filter_list set filter_status=0  WHERE filter_id =$id";

            if (mysql_query($sql))
            {
                echo action_msg("success", 1);

            }
            else
            {
                echo action_msg("error", 0);

            }

        break;


    }
?>