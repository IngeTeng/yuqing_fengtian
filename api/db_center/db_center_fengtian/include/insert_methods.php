<?php

/**
 * @filename sql_methods.php
 * @encoding UTF-8
 * @author WiiPu CzRzChao
 * @createtime 2016-6-20  11:17:14
 * @updatetime 2016-6-20  11:17:14
 * @version 1.0
 * @Description
 * 数据库写入操作
 *
 */
include_once("lib.mds.function.php");
include_once("configure.php");

// PDO连接数据库
class PDOFactory
{
    public static function getPDO($log = '', $db_host, $db_name, $username, $password, $options = array())
    {
        $dsn = "mysql:dbname=" . $db_name . ";host=" . $db_host;

        $pdo_key = self::getKey($dsn, $username, $password, $options);
        if (!isset($GLOBALS['PDOS']) or !($GLOBALS['PDOS'][$pdo_key] instanceof PDO)) {
            try {
                $GLOBALS['PDOS'][$pdo_key] = new PDO($dsn, $username, $password, $options);
                $GLOBALS['PDOS'][$pdo_key]->query("SET NAMES utf8");
                $GLOBALS['PDOS'][$pdo_key]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                if (!empty($log)) {
                    $log->INFO("新建数据库连接成功, pdo_key = $pdo_key");
                }
            } catch (PDOException $ex) {
                if (!empty($log)) {
                    $log->WARN("数据库连接失败, " . $ex->getMessage());
                }
                return false;
            }
        }
        return $GLOBALS['PDOS'][$pdo_key];
    }

    public static function getKey($dsn, $username, $password, $options = array())
    {
        return md5(serialize(array($dsn, $username, $password, $options)));
    }

    public static function rollBack($_pdo, $insert_stack)
    {
        foreach ($insert_stack as $insert_row) {
            $query = "DELETE FROM {$insert_row['table_name']} WHERE {$insert_row['id_name']} = {$insert_row['insert_id']}";
            $result = $_pdo->query($query);
        }
    }

    public static function unsetPDO($dsn, $username, $password, $options = array())
    {
        $pdo_key = self::getKey($dsn, $username, $password, $options);
        if (isset($GLOBALSS['PDOS'][$pdo_key])) {
            unset($GLOBALSS['PDOS'][$pdo_key]);
        }
    }
}

function send_post($url, $post_data)
{
    $postdata = http_build_query($post_data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // 设置不输出到屏幕
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    $content = curl_exec($ch);
    return $content;
}

//再次分析网页
function get_content($url)
{
    $request['url'] = $url;
    $response = send_post(CONTENT_ANALYSE, $request);
    $response = json_decode($response, true);
    return $response;
}

// 记录单个关键词索引
function insert_key($_pdo, $log, $key_info)
{
    $keyword = '';
    if (!empty($key_info['keyword'])) {
        $keyword = $key_info['keyword'];
        // 查找当前关键词id
        $query = "SELECT * FROM keyword WHERE keyword = '{$key_info['keyword']}'";
        $row = $_pdo->query($query)->fetch();
        $k_id = $row['k_id'];
    } else {
        $k_id = $key_info['k_id'];
    }

    // 查找当前关键词对应的user_keywords
    $query = "SELECT * FROM user_keywords WHERE k_id = '$k_id'";
    $log->INFO("insert_key中，，k_id: $k_id, keyword:{$key_info['keyword']} ");
    $uk_row = $_pdo->query($query);
    while ($row = $uk_row->fetch()) {
        // 在信息索引表中查找是否已经记录
        $query = "SELECT id FROM {$key_info['table_name']} WHERE user_id = '{$row['user_id']}' and article_id = '{$key_info['article_id']}' and c_id = '{$row['c_id']}'";
        $nk_row = $_pdo->query($query)->fetch();

        // 如果没有记录
        if (empty($nk_row)) {
            $query = "INSERT INTO {$key_info['table_name']} (uk_id, article_id, article_property, article_pubtime, a_type, user_id, article_addtime, c_id) "
                . "VALUES (:uk_id, :article_id, :article_property, :article_pubtime, :a_type, :user_id, :article_addtime, :c_id)";
            $staff_statement = $_pdo->prepare($query);
            // if($row['uk_id'] == 102 && $keyword == 'TNGA'){//丰田用，融合TNGA和凯美瑞
            //    $row['uk_id'] = 2;
            // }
            $staff_statement->bindParam(':uk_id', $row['uk_id'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_id', $key_info['article_id'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_property', $key_info['article_property'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_pubtime', $key_info['article_pubtime'], PDO::PARAM_INT);
            $staff_statement->bindParam(':user_id', $row['user_id'], PDO::PARAM_INT);
            $staff_statement->bindValue(':article_addtime', time(), PDO::PARAM_INT);
            $staff_statement->bindParam(':c_id', $row['c_id'], PDO::PARAM_INT);

            $a_type = isset($key_info['a_type']) ? $key_info['a_type'] : 0;
            $staff_statement->bindParam(':a_type', $a_type, PDO::PARAM_INT);

            $result = $staff_statement->execute();
            if ($result === false) {
                $log->WARN("{$key_info['table_name']}_key 插入失败 article_id: {$key_info['article_id']}");
                return false;
            }
            $log->INFO("{$key_info['table_name']}_key 插入成功, id = " . $_pdo->lastInsertId());
        } else {
            $log->INFO("key表中已有记录，keyword:{$key_info['keyword']} ");
        }
    }
    return true;
}

// 匹配媒体
function getMedia($uri, $media_list)
{
    $media_name = '';

    for ($i = 0; $i < count($media_list); $i++) {
        if (strstr($uri, $media_list[$i]['domain']) != '') {
            $media_name = $media_list[$i]['media_name'];

            break;
        }
    }
    return $media_name;
}

//
function getMedias($uri, $media_list)
{
    $media_name = '';
    $grade = 3;
    for ($i = 0; $i < count($media_list); $i++) {
        if (strstr($uri, $media_list[$i]['domain']) != '') {
            $media_name = $media_list[$i]['media_name'];
            $grade = $media_list[$i]['grade'];
            break;
        }
    }
    return array('name' => $media_name, 'grade' => $grade);
}

// news_list的写入操作
function news_list($infos, $log, $keyword)
{
    $insert_stack = array();
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);

    if ($_pdo === false) {
        return false;
    }


    $infos_count = count($infos);

    //获取媒体列表
    $query = "SELECT domain,media_name,grade  from media_list";
    $media_rows = $_pdo->query($query);
    $i = 0;
    $media_list = array();
    foreach ($media_rows as $row) {
        $media_list[$i]['domain'] = $row['domain'];
        $media_list[$i]['media_name'] = $row['media_name'];
        $media_list[$i]['grade'] = $row['grade'];
        $i++;
    }

    for ($i = 0; $i < $infos_count; $i++) {
        $media = '';
        $media = getMedia($infos[$i]['article_url'], $media_list);
        $grade = getMedias($infos[$i]['article_url'], $media_list)['grade'];
        if (!empty($media)) {
            $infos[$i]['media'] = $media;
        }
        if (empty($grade)) {
            $grade = 3;
        }
        if (empty($infos[$i]['article_is_repost'])) {
            $infos[$i]['article_is_repost'] = 1;
        }
        $a_type = 0;
        if (strstr($infos[$i]['article_channel'], '经销商')
            or strstr($infos[$i]['article_title'], '经销商') or strstr($infos[$i]['article_title'], '优惠促销') or strstr($infos[$i]['article_title'], '抢购优惠')
            or (strstr($infos[$i]['article_title'], '广汽丰田') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')))
            or (strstr($infos[$i]['article_title'], '广丰') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')))
            or strstr($infos[$i]['article_content'], '经销商') or strstr($infos[$i]['article_content'], '优惠促销') or strstr($infos[$i]['article_content'], '抢购优惠')
            or (strstr($infos[$i]['article_content'], '广汽丰田') and (strstr($infos[$i]['article_content'], '有限公司') or strstr($infos[$i]['article_content'], '店')))
            or (strstr($infos[$i]['article_content'], '广丰') and (strstr($infos[$i]['article_content'], '有限公司') or strstr($infos[$i]['article_content'], '店')))
        ) {
            $a_type = 1;
        }
        // 过滤结果
        $query = "SELECT f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) WHERE k.keyword = '$keyword' OR f.k_id = 0";
        $filter_rows = $_pdo->query($query);
        $filter_result = false;
        foreach ($filter_rows as $row) {
            if (stripos($infos[$i]['article_title'], $row['filter_word']) === false and stripos($infos[$i]['article_content'], $row['filter_word']) === false) {
                continue;
            } else {
                $filter_result = true;
                break;
            }
        }
        if (mb_strlen($infos[$i]['media'], 'UTF-8') > 13 or strstr($infos[$i]['article_title'], '棋牌') or strstr($infos[$i]['article_content'], '包夜服务') or strstr($infos[$i]['article_title'], '娱乐')) {
            $log->WARN("过滤赌博, keyword:{$keyword}, {$infos[$i]['media']}：" . mb_strlen($infos[$i]['media'], 'UTF-8'));
            continue;
        }
        // 过滤结果2，将内容和标题里都没有包含关键字的结果过滤掉
        if ($keyword != '广汽丰田' and stripos($infos[$i]['article_title'], $keyword) === false and stripos($infos[$i]['article_content'], $keyword) === false and stripos($infos[$i]['article_summary'], $keyword) === false) {
            $filter_result = true;
            $log->WARN("过滤结果2, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        }


        //过滤掉不属于新闻的论坛信息
        if (stripos($infos[$i]['article_title'], '论坛') !== false or stripos($infos[$i]['article_url'], 'bbs') !== false or stripos($infos[$i]['article_url'], 'tieba') !== false) {
            $log->WARN("过滤结果3-论坛, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            continue;
        }
        //过滤掉金属新闻网
        if (stripos($infos[$i]['media'], '金属新闻网') !== false or stripos($infos[$i]['media'], '华股财经') !== false) {
            $log->WARN("过滤结果3-金属新闻或华股财经, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            continue;
        }

        /*******wm********/

        //过滤掉养猪巴巴网
        if (stripos($infos[$i]['media'], '养猪巴巴网') !== false or stripos($infos[$i]['article_url'], 'www.yz88.cn') !== false) {
            $log->WARN("过滤结果3, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            continue;
        }

        /***************/


        //过滤掉无效数据
        if (stripos($infos[$i]['media'], '印象庆阳网') !== false or stripos($infos[$i]['article_url'], 'cien.com.cn') !== false) {
            $log->WARN("过滤结果3, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            continue;
        }


        //重新过滤一次,过过滤内容没哟，标题也没有的


        if ($filter_result === true) {
            continue;
        }
//        file_put_contents('news_list_value', var_export(get_array($infos[$i]['article_url']), true));

        /****** wm 去除.html 或.htm 或 .shtml 后的参数**********/
        $url_arrs = array('club.autohome.com.cn');

        foreach ($url_arrs as $url_arr) {
            if (stripos($infos[$i]['article_url'], $url_arr)) {
                if (stripos($infos[$i]['article_url'], '.shtml')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.shtml') + 6);
                } elseif (stripos($infos[$i]['article_url'], '.html')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.html') + 5);
                } elseif (stripos($infos[$i]['article_url'], '.htm')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.htm') + 4);
                }
            }
        }


        preg_match('/\/\/.*?\//', $infos[$i]['article_url'], $my_domain);

        $my_domain = $my_domain[0];

        /*******************/
// 进行查询
        $query = "SELECT article_id from news_article WHERE ( article_url = :article_url ) or ( article_title = :article_title and article_url REGEXP :my_domain and article_addtime>" . (time() - 24 * 60 * 60) . "  ) ";
        $staff_statement = $_pdo->prepare($query);
        $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
        $staff_statement->bindParam(':my_domain', $my_domain, PDO::PARAM_STR);

        $staff_statement->execute();
        $result = $staff_statement->fetch();

        if (empty($result)) {
            //再次过滤
            if (!empty($keyword) and stripos(get_content($infos[$i]['article_url'])['content'], $keyword) === false and stripos($infos[$i]['article_title'], $keyword) === false) {

                $query = "INSERT INTO filter_list (filter_title, filter_url, filter_pubtime, "
                    . "filter_content, filter_type, filter_media, filter_property, filter_keyword, filter_atype, filter_author, filter_is_repost,filter_grade) "
                    . "VALUES ( :filter_title, :filter_url, :filter_pubtime, "
                    . ":filter_content, :filter_type, :filter_media, :filter_property, :filter_keyword, :filter_atype, :filter_author, :filter_is_repost, :filter_grade)";
                $staff_statement = $_pdo->prepare($query);
                $staff_statement->bindParam(':filter_title', $infos[$i]['article_title'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_url', $infos[$i]['article_url'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_content', $infos[$i]['article_content'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
                $staff_statement->bindValue(':filter_type', 1, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_property', $infos[$i]['article_property'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_keyword', $keyword, PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_atype', $a_type, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_media', $infos[$i]['media'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_author', $infos[$i]['article_author'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_grade', $grade, PDO::PARAM_INT);
                // 执行预处理语句
                $result = $staff_statement->execute();
                if ($result === false) {
                    $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                    return $result;
                }

                $log->WARN("过滤结果4(插入过滤表中), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            }
            // 如果没有保存,进行保存
            $query = "INSERT INTO news_article (article_title, article_url, article_content, article_pubtime, "
                . "article_addtime, article_summary, article_comment, article_source, article_channel, media, article_author, article_is_repost,article_grade) "
                . "VALUES (:article_title, :article_url, :article_content, :article_pubtime, "
                . ":article_addtime, :article_summary, :article_comment, :article_source, :article_channel, :media, :article_author, :article_is_repost, :article_grade)";
            $staff_statement = $_pdo->prepare($query);

            $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_content', $infos[$i]['article_content'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
            $staff_statement->bindValue(':article_addtime', time(), PDO::PARAM_INT);
            $staff_statement->bindParam(':article_summary', $infos[$i]['article_summary'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_comment', $infos[$i]['article_comment'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_source', $infos[$i]['article_source'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_channel', $infos[$i]['article_channel'], PDO::PARAM_STR);
            $staff_statement->bindParam(':media', $infos[$i]['media'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_author', $infos[$i]['article_author'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_grade', $grade, PDO::PARAM_INT);
            file_put_contents("query", var_export($query, true));
            // 执行预处理语句
            $result = $staff_statement->execute();
            if ($result === false) {
                $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                return $result;
            }
            $article_id = $_pdo->lastInsertId();
        } else {
            $article_id = $result['article_id'];
        }

        // 关键词的记录
        $key_info = array(
            'keyword' => $keyword,
            'table_name' => 'news_key',
            'article_id' => $article_id,
            'article_property' => $infos[$i]['article_property'],
            'article_pubtime' => $infos[$i]['article_pubtime']
        );
        /* if( strstr($infos[$i]['article_channel'], '经销商')  or strstr($infos[$i]['article_title'], '经销商') or strstr($infos[$i]['article_title'], '优惠') or
            ( strstr($infos[$i]['article_title'], '广汽丰田') and  ( strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')  )  ) or
            ( strstr($infos[$i]['article_title'], '广丰') and  ( strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')  )  )  
            ){ 
            $key_info['a_type'] = 1;
        }*/

        $key_result = insert_key($_pdo, $log, $key_info);
        if ($key_result === false) {
            return false;
        }
    }
    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    return true;
}


// weibo_list的入库操作
function weibo_list($infos, $log, $keyword)
{
    $insert_stack = array();
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);

    if ($_pdo === false) {
        return false;
    }


    //获取媒体列表
    $query = "SELECT domain , media_name, grade  from media_list";
    $media_rows = $_pdo->query($query);
    $i = 0;
    $media_list = array();
    foreach ($media_rows as $row) {
        $media_list[$i]['domain'] = $row['domain'];
        $media_list[$i]['media_name'] = $row['media_name'];
        $media_list[$i]['grade'] = $row['grade'];
        $i++;
    }
    $infos_count = count($infos);
    for ($i = 0; $i < $infos_count; $i++) {
        // 过滤结果
        $query = "SELECT f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) WHERE k.keyword = '$keyword' OR f.k_id = 0";
        $filter_rows = $_pdo->query($query);
        $filter_result = false;
        foreach ($filter_rows as $row) {
            if (stripos($infos[$i]['article_title'], $row['filter_word']) === false) {
                continue;
            } else {
                $filter_result = true;
                $log->WARN("过滤结果1, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
                break;
            }
        }
        if ($keyword == 'POLO' and (strstr($infos[$i]['article_title'], '女装') or strstr($infos[$i]['article_title'], '衬衫') or strstr($infos[$i]['article_title'], '衣服') or strstr($infos[$i]['article_title'], '连衣裙'))) {
            $filter_result = true;
            $log->WARN("过滤含有衣服的结果, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        }
        // 过滤结果2，将内容和标题里都没有包含关键字的结果过滤掉
        if (!strstr($infos[$i]['article_title'], $keyword)) {
            $filter_result = true;
            $log->WARN("过滤结果2, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        }
        if ($keyword == '广汽丰田' and stripos($infos[$i]['article_title'], 'C-HR') != false) {
            continue;
        }

        //重新过滤一次,过过滤内容没哟，标题也没有的
        $grade = getMedias($infos[$i]['article_url'], $media_list)['grade'];
        if (empty($infos[$i]['article_is_repost'])) {
            $infos[$i]['article_is_repost'] = 1;
        }
        if (empty($grade)) {
            $grade = 3;
        }
        if ($grade == 1) {  //原来的A级别的媒体有新闻、APP、视频、论坛、博客类型的数据，这些里面新闻和APP的数据属于A级别，视频、论坛、博客属于B级别的
            $grade = 2;
        }

        if ($filter_result === true) {
            continue;
        }

        /****** wm 去除.html 或.htm 或 .shtml 后的参数**********/
        $url_arrs = array('club.autohome.com.cn');

        foreach ($url_arrs as $url_arr) {
            if (stripos($infos[$i]['article_url'], $url_arr)) {
                if (stripos($infos[$i]['article_url'], '.shtml')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.shtml') + 6);
                } elseif (stripos($infos[$i]['article_url'], '.html')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.html') + 5);
                } elseif (stripos($infos[$i]['article_url'], '.htm')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.htm') + 4);
                }
            }
        }


        preg_match('/\/\/.*?\//', $infos[$i]['article_url'], $my_domain);

        $my_domain = $my_domain[0];

        /*******************/
        // 进行查询
        $query = "SELECT article_id from weibo_article WHERE ( article_url = :article_url ) or ( article_title = :article_title and article_url REGEXP :my_domain and article_addtime>" . (time() - 24 * 60 * 60) . "  ) ";
        $staff_statement = $_pdo->prepare($query);
        $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
        $staff_statement->bindParam(':my_domain', $my_domain, PDO::PARAM_STR);

        /*******************/

        $staff_statement->execute();
        $result = $staff_statement->fetch();

        if (empty($result)) {


            if (!empty($keyword) and stripos(get_content($infos[$i]['article_url'])['content'], $keyword) === false and stripos($infos[$i]['article_title'], $keyword) === false) {

                $a_type = 0;
                if (strstr($infos[$i]['article_channel'], '经销商') or strstr($infos[$i]['article_title'], '经销商') or strstr($infos[$i]['article_title'], '优惠') or
                    (strstr($infos[$i]['article_title'], '广汽丰田') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店'))) or
                    (strstr($infos[$i]['article_title'], '广丰') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')))
                ) {
                    $a_type = 1;
                }
                $query = "INSERT INTO filter_list (filter_title, filter_url, filter_pubtime, "
                    . "filter_content, filter_type, filter_media, filter_property, filter_keyword, filter_atype, filter_author, filter_is_repost,filter_grade) "
                    . "VALUES ( :filter_title, :filter_url, :filter_pubtime, "
                    . ":filter_content, :filter_type, :filter_media, :filter_property, :filter_keyword, :filter_atype, :filter_author, :filter_is_repost, :filter_grade)";
                $staff_statement = $_pdo->prepare($query);
                $staff_statement->bindParam(':filter_title', $infos[$i]['article_title'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_url', $infos[$i]['article_url'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_content', $infos[$i]['article_content'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
                $staff_statement->bindValue(':filter_type', 4, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_property', $infos[$i]['article_property'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_keyword', $keyword, PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_atype', $a_type, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_media', $infos[$i]['media'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_author', $infos[$i]['article_author'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_grade', $grade, PDO::PARAM_INT);
                // 执行预处理语句
                $result = $staff_statement->execute();
                if ($result === false) {
                    $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                    return $result;
                }

                $log->WARN("过滤结果4(插入过滤表中), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            }
            // 如果没有保存,进行保存
//            if(empty($infos[$i]['media'])) {    // 如果媒体为空,进行匹配
//                continue;
//            }

            $query = "INSERT INTO weibo_article (article_url, article_title, article_pubtime, article_addtime,"
                . " article_comment, article_repost, author, isV, rz_info, fans, media, mid, article_author, article_is_repost,article_grade) "
                . "VALUES (:article_url, :article_title, :article_pubtime, :article_addtime, "
                . ":article_comment, :article_repost, :author, :isV, :rz_info, :fans, :media, :mid, :article_author, :article_is_repost, :article_grade)";

            $staff_statement = $_pdo->prepare($query);

            $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
            $staff_statement->bindValue(':article_addtime', time(), PDO::PARAM_INT);
            $staff_statement->bindParam(':article_comment', $infos[$i]['article_comment'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_repost', $infos[$i]['article_repost'], PDO::PARAM_INT);
            $staff_statement->bindParam(':author', $infos[$i]['author'], PDO::PARAM_STR);
            $staff_statement->bindParam(':isV', $infos[$i]['isV'], PDO::PARAM_INT);
            $staff_statement->bindParam(':rz_info', $infos[$i]['rz_info'], PDO::PARAM_STR);
            $staff_statement->bindParam(':fans', $infos[$i]['fans'], PDO::PARAM_INT);
            $staff_statement->bindParam(':media', $infos[$i]['media'], PDO::PARAM_STR);
            $staff_statement->bindParam(':mid', $infos[$i]['mid'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_author', $infos[$i]['author'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_grade', $grade, PDO::PARAM_INT);

            // 执行预处理语句
            $result = $staff_statement->execute();
            if ($result === false) {
                $log->WARN("weibo_article 插入失败,keyword: $keyword url:{$infos[$i]['article_title']}");
                return $result;
            }
            $article_id = $_pdo->lastInsertId();
        } else {
            $article_id = $result['article_id'];
        }

        // 关键词的记录
        $key_info = array(
            'keyword' => $keyword,
            'table_name' => 'weibo_key',
            'article_id' => $article_id,
            'article_property' => $infos[$i]['article_property'],
            'article_pubtime' => $infos[$i]['article_pubtime']
        );
        $key_result = insert_key($_pdo, $log, $key_info);
        if ($key_result === false) {
            $log->WARN("weibo_key 插入失败,keyword: $keyword, article_id: $article_id");
            return false;
        }
    }
    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    return true;
}

// weixin_list 的入库操作
function weixin_list($infos, $log, $keyword)
{
    $insert_stack = array();
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);

    if ($_pdo === false) {
        return false;
    }

    $infos_count = count($infos);
    /**************zx**********************/

    //获取媒体列表
    $query = "SELECT domain,media_name,grade  from media_list";
    $media_rows = $_pdo->query($query);
    $i = 0;
    $media_list = array();
    foreach ($media_rows as $row) {
        $media_list[$i]['domain'] = $row['domain'];
        $media_list[$i]['media_name'] = $row['media_name'];
        $media_list[$i]['grade'] = $row['grade'];
        $i++;
    }
    /************************************/
    for ($i = 0; $i < $infos_count; $i++) {
        // 过滤结果
        $query = "SELECT f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) WHERE k.keyword = '$keyword' OR f.k_id = 0";
        $filter_rows = $_pdo->query($query);
        $filter_result = false;
        foreach ($filter_rows as $row) {
            if (stripos($infos[$i]['article_title'], $row['filter_word']) === false and stripos($infos[$i]['article_summary'], $row['filter_word']) === false) {
                continue;
            } else {
                $filter_result = true;
                break;
            }
        }
        // 过滤结果2，将内容和标题里都没有包含关键字的结果过滤掉
        if (!empty($keyword) and stripos($infos[$i]['article_title'], $keyword) === false and stripos($infos[$i]['article_summary'], $keyword) === false) {
            $filter_result = true;
            $log->WARN("过滤结果2, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        }

        if (empty($infos[$i]['article_is_repost'])) {
            $infos[$i]['article_is_repost'] = 1;
        }
        $grade = getMedias($infos[$i]['article_url'], $media_list)['grade'];
        if (empty($grade)) {
            $grade = 3;
        }
        if ($grade == 1) {  //原来的A级别的媒体有新闻、APP、视频、论坛、博客类型的数据，这些里面新闻和APP的数据属于A级别，视频、论坛、博客属于B级别的
            $grade = 2;
        }

        if ($filter_result === true) {
            continue;
        }

        // 检查微信是否是重复内容
        $p = stripos($infos[$i]['article_url'], 'signature=');
        $sign = substr($infos[$i]['article_url'], $p + strlen('signature='), 128);
        $query = "SELECT article_id FROM weixin_article WHERE sign = :sign";
        $staff_statement = $_pdo->prepare($query);
        $staff_statement->bindParam(':sign', $sign, PDO::PARAM_STR);
        $staff_statement->execute();
        $result = $staff_statement->fetch();

        $query2 = "SELECT id FROM weixin_url WHERE url = :sign";
        $staff_statement = $_pdo->prepare($query2);
        $staff_statement->bindParam(':sign', $sign, PDO::PARAM_STR);
        $staff_statement->execute();
        $result2 = $staff_statement->fetch();

        //用于过滤标题和公众号都相等的消息
        $query3 = "SELECT article_id FROM weixin_article WHERE article_title = :title AND author=:author";
        $staff_statement = $_pdo->prepare($query3);
        $staff_statement->bindParam(':title', $infos[$i]['article_title'], PDO::PARAM_STR);
        $staff_statement->bindParam(':author', $infos[$i]['author'], PDO::PARAM_STR);
        $staff_statement->execute();
        $result3 = $staff_statement->fetch();
        //file_put_contents('-'.$result3, var_export($infos[$i],TRUE));
        if (empty($result2)) {
            $query = "INSERT INTO weixin_url (url) VALUES (:sign)";
            $staff_statement = $_pdo->prepare($query);
            $staff_statement->bindParam(':sign', $sign, PDO::PARAM_STR);
            $staff_statement->execute();
        }

        if (empty($result) and empty($result3)) {


            if (!empty($keyword) and stripos(get_content($infos[$i]['article_url'])['content'], $keyword) === false and stripos($infos[$i]['article_title'], $keyword) === false) {
//            $filter_result = true;
                $a_type = 0;
                if (strstr($infos[$i]['article_channel'], '经销商') or strstr($infos[$i]['article_title'], '经销商') or strstr($infos[$i]['article_title'], '优惠') or
                    (strstr($infos[$i]['article_title'], '广汽丰田') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店'))) or
                    (strstr($infos[$i]['article_title'], '广丰') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')))
                ) {
                    $a_type = 1;
                }
                continue;  // 对微信不再进行插入过滤列表

                $query = "INSERT INTO filter_list (filter_title, filter_url, filter_pubtime, "
                    . "filter_content, filter_type, filter_media, filter_property, filter_keyword, filter_atype, filter_author,filter_article_author, filter_is_repost,filter_grade) "
                    . "VALUES ( :filter_title, :filter_url, :filter_pubtime, "
                    . ":filter_content, :filter_type, :filter_media, :filter_property, :filter_keyword, :filter_atype, :filter_author,:filter_article_author, :filter_is_repost, :filter_grade)";
                $staff_statement = $_pdo->prepare($query);
                $staff_statement->bindParam(':filter_title', $infos[$i]['article_title'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_url', $infos[$i]['article_url'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_content', $infos[$i]['article_summary'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
                $staff_statement->bindValue(':filter_type', 6, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_property', $infos[$i]['article_property'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_keyword', $keyword, PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_atype', $a_type, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_media', $infos[$i]['media'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_author', $infos[$i]['author'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_article_author', $infos[$i]['author'], PDO::PARAM_STR);

                $staff_statement->bindParam(':filter_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_grade', $grade, PDO::PARAM_INT);
                // 执行预处理语句
                $result = $staff_statement->execute();
                if ($result === false) {
                    $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                    return $result;
                }

                $log->WARN("过滤结果4(插入过滤表中), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            }
            // 如果没有保存,进行保存

            $query = "INSERT INTO weixin_article (article_url, article_title, article_summary, "
                . "article_pubtime, article_addtime, author, media, sign, read_num, like_num  ,article_author, article_is_repost,article_grade) "
                . "VALUES (:article_url, :article_title, :article_summary, :article_pubtime, "
                . ":article_addtime, :author, :media, :sign, :read_num, :like_num , :article_author, :article_is_repost, :article_grade)";




            $staff_statement = $_pdo->prepare($query);

            $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_summary', $infos[$i]['article_summary'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
            $staff_statement->bindValue(':article_addtime', time(), PDO::PARAM_INT);
            $staff_statement->bindParam(':media', $infos[$i]['media'], PDO::PARAM_STR);
            $staff_statement->bindParam(':author', $infos[$i]['author'], PDO::PARAM_STR);
            $staff_statement->bindParam(':sign', $sign, PDO::PARAM_STR);
            $staff_statement->bindParam(':read_num', $infos[$i]['read_num'], PDO::PARAM_STR);
            $staff_statement->bindParam(':like_num', $infos[$i]['like_num'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_author', $infos[$i]['author'], PDO::PARAM_STR);

            $staff_statement->bindParam(':article_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_grade', $grade, PDO::PARAM_INT);
            // 执行预处理语句
            $result = $staff_statement->execute();
            if ($result === false) {
                $log->WARN("weixin_article 插入失败, url:{$infos[$i]['article_title']}");
                return $result;
            }
            $article_id = $_pdo->lastInsertId();
        } else {
            $article_id = $result['article_id'];
        }

        // 关键词的记录
        $key_info = array(
            'keyword' => $keyword,
            'table_name' => 'weixin_key',
            'article_id' => $article_id,
            'article_property' => $infos[$i]['article_property'],
            'article_pubtime' => $infos[$i]['article_pubtime']
        );
        $key_result = insert_key($_pdo, $log, $key_info);
        if ($key_result === false) {
            return false;
        }
    }
    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    return true;
}

// vedio_list 的入库操作
function video_list($infos, $log, $keyword)
{
    $insert_stack = array();
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);

    if ($_pdo === false) {
        return false;
    }

    $infos_count = count($infos);
    /**************zx**********************/

    //获取媒体列表
    $query = "SELECT domain,media_name,grade  from media_list";
    $media_rows = $_pdo->query($query);
    $i = 0;
    $media_list = array();
    foreach ($media_rows as $row) {
        $media_list[$i]['domain'] = $row['domain'];
        $media_list[$i]['media_name'] = $row['media_name'];
        $media_list[$i]['grade'] = $row['grade'];
        $i++;
    }
    /************************************/
    for ($i = 0; $i < $infos_count; $i++) {
        if (!empty($keyword)) {
            // 过滤结果
            $query = "SELECT f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) WHERE k.keyword = '$keyword' OR f.k_id = 0";
            $filter_rows = $_pdo->query($query);
            $filter_result = false;
            foreach ($filter_rows as $row) {
                if (stripos($infos[$i]['article_title'], $row['filter_word']) === false and stripos($infos[$i]['article_summary'], $row['filter_word']) === false) {
                    continue;
                } else {
                    $filter_result = true;
                    break;
                }
            }
            // 过滤结果2，将内容和标题里都没有包含关键字的结果过滤掉
            if (!empty($keyword) and stripos($infos[$i]['article_title'], $keyword) === false and stripos($infos[$i]['article_summary'], $keyword) === false) {
                $filter_result = true;
                $log->WARN("过滤结果-视频, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            }

            $media = getMedia($infos[$i]['article_url'], $media_list);
            if (!empty($media)) {
                $infos[$i]['media'] = $media;
            }
            $grade = getMedias($infos[$i]['article_url'], $media_list)['grade'];
            if (empty($grade)) {
                $grade = 3;
            }
            if ($grade == 1) {  //原来的A级别的媒体有新闻、APP、视频、论坛、博客类型的数据，这些里面新闻和APP的数据属于A级别，视频、论坛、博客属于B级别的
                $grade = 2;
            }

            if (empty($infos[$i]['article_is_repost'])) {
                $infos[$i]['article_is_repost'] = 1;
            }

            if ($filter_result === true) {
                continue;
            }
        }

        /****** wm 去除.html 或.htm 或 .shtml 后的参数**********/
        $url_arrs = array('club.autohome.com.cn');

        foreach ($url_arrs as $url_arr) {
            if (stripos($infos[$i]['article_url'], $url_arr)) {
                if (stripos($infos[$i]['article_url'], '.shtml')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.shtml') + 6);
                } elseif (stripos($infos[$i]['article_url'], '.html')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.html') + 5);
                } elseif (stripos($infos[$i]['article_url'], '.htm')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.htm') + 4);
                }
            }
        }


        preg_match('/\/\/.*?\//', $infos[$i]['article_url'], $my_domain);

        $my_domain = $my_domain[0];

        /*******************/
// 进行查询
        $query = "SELECT article_id from video_article WHERE ( article_url = :article_url ) or ( article_title = :article_title and article_url REGEXP :my_domain and article_addtime>" . (time() - 24 * 60 * 60) . "  ) ";
        $staff_statement = $_pdo->prepare($query);
        $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
        $staff_statement->bindParam(':my_domain', $my_domain, PDO::PARAM_STR);


        $staff_statement->execute();
        $result = $staff_statement->fetch();

        if (empty($result)) {
            //
            if (!empty($keyword) and stripos(get_content($infos[$i]['article_url'])['content'], $keyword) === false and stripos($infos[$i]['article_title'], $keyword) === false) {
//                $filter_result = true;
                $a_type = 0;
                if (strstr($infos[$i]['article_channel'], '经销商') or strstr($infos[$i]['article_title'], '经销商') or strstr($infos[$i]['article_title'], '优惠') or
                    (strstr($infos[$i]['article_title'], '广汽丰田') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店'))) or
                    (strstr($infos[$i]['article_title'], '广丰') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')))
                ) {
                    $a_type = 1;
                }
                $query = "INSERT INTO filter_list (filter_title, filter_url, filter_pubtime, "
                    . "filter_content, filter_type, filter_media, filter_property, filter_keyword, filter_atype, filter_author, filter_is_repost,filter_grade) "
                    . "VALUES ( :filter_title, :filter_url, :filter_pubtime, "
                    . ":filter_content, :filter_type, :filter_media, :filter_property, :filter_keyword, :filter_atype, :filter_author, :filter_is_repost, :filter_grade)";
                $staff_statement = $_pdo->prepare($query);
                $staff_statement->bindParam(':filter_title', $infos[$i]['article_title'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_url', $infos[$i]['article_url'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_content', $infos[$i]['article_summary'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
                $staff_statement->bindValue(':filter_type', 5, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_property', $infos[$i]['article_property'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_keyword', $keyword, PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_atype', $a_type, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_media', $infos[$i]['media'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_author', $infos[$i]['article_author'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_grade', $grade, PDO::PARAM_INT);
                // 执行预处理语句
                $result = $staff_statement->execute();
                if ($result === false) {
                    $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                    return $result;
                }

                $log->WARN("过滤结果4(插入过滤表中), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            }
            //

            $query = "INSERT INTO video_article(article_url, article_title, article_pubtime,"
                . " article_addtime, article_summary, media, article_author, article_is_repost, article_grade)"
                . "VALUES (:article_url, :article_title, :article_pubtime, :article_addtime, "
                . ":article_summary, :media, :article_author, :article_is_repost, :article_grade)";

            $staff_statement = $_pdo->prepare($query);

            $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
            $staff_statement->bindValue(':article_addtime', time(), PDO::PARAM_INT);
            $staff_statement->bindParam(':article_summary', $infos[$i]['article_summary'], PDO::PARAM_STR);
            $staff_statement->bindParam(':media', $infos[$i]['media'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_author', $infos[$i]['article_author'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_grade', $grade, PDO::PARAM_INT);
            // 执行预处理语句
            $result = $staff_statement->execute();
            if ($result === false) {
                $log->WARN("video_article 插入失败, url:{$infos[$i]['article_title']}");
                return $result;
            }
            $article_id = $_pdo->lastInsertId();
        } else {
            $article_id = $result['article_id'];
        }

        // 关键词的记录
        $key_info = array(
            'keyword' => $keyword,
            'table_name' => 'video_key',
            'article_id' => $article_id,
            'article_property' => $infos[$i]['article_property'],
            'article_pubtime' => $infos[$i]['article_pubtime']
        );
        if (isset($infos[$i]['k_ids'])) {
            $k_ids = $infos[$i]['k_ids'];
            foreach ($k_ids as $k_id) {
                $key_info['k_id'] = $k_id;
                $key_result = insert_key($_pdo, $log, $key_info);
                if ($key_result === false) {
                    return false;
                }
            }
        }
        $key_result = insert_key($_pdo, $log, $key_info);
        if ($key_result === false) {
            return false;
        }
    }
    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    return true;
}

// bbs文章入库
function bbs_article($infos, $log, $keyword)
{
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);

    if ($_pdo === false) {
        return false;
    }
    //获取媒体列表
    $query = "SELECT domain,media_name,grade  from media_list";
    $media_rows = $_pdo->query($query);
    $i = 0;
    $media_list = array();
    foreach ($media_rows as $row) {
        $media_list[$i]['domain'] = $row['domain'];
        $media_list[$i]['media_name'] = $row['media_name'];
        $media_list[$i]['grade'] = $row['grade'];
        $i++;
    }
    // 过滤结果
    if (!empty($keyword)) {
        $query = "SELECT f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) WHERE k.keyword = '$keyword' OR f.k_id = 0";
        $filter_rows = $_pdo->query($query);
        $filter_result = false;
        foreach ($filter_rows as $row) {
            if (stripos($infos['article_title'], $row['filter_word']) === false and stripos($infos['article_summary'], $row['filter_word']) === false) {
                continue;
            } else {
                $filter_result = true;
                break;
            }
        }
        // 过滤结果2，将内容和标题里都没有包含关键字的结果过滤掉
        if (stripos($infos['article_title'], $keyword) === false and stripos($infos['article_summary'], $keyword) === false) {
            $filter_result = true;
            $log->WARN("过滤结果2, keyword:{$keyword}, url:{$infos['article_url']}");
        }
        if (empty($infos['article_is_repost'])) {
            $infos['article_is_repost'] = 1;
        }
        $grade = getMedias($infos['article_url'], $media_list)['grade'];
        if (empty($grade)) {
            $grade = 3;
        }
        if ($grade == 1) {  //原来的A级别的媒体有新闻、APP、视频、论坛、博客类型的数据，这些里面新闻和APP的数据属于A级别，视频、论坛、博客属于B级别的
            $grade = 2;
        }

        //通过内容进行过滤

        if ($filter_result === true) {
            return true;
        }
    }

    /****** wm 去除.html 或.htm 或 .shtml 后的参数**********/
    $url_arrs = array('club.autohome.com.cn');

    foreach ($url_arrs as $url_arr) {
        if (stripos($infos['article_url'], $url_arr)) {
            if (stripos($infos['article_url'], '.shtml')) {
                $infos['article_url'] = substr($infos['article_url'], 0, stripos($infos['article_url'], '.shtml') + 6);
            } elseif (stripos($infos['article_url'], '.html')) {
                $infos['article_url'] = substr($infos['article_url'], 0, stripos($infos['article_url'], '.html') + 5);
            } elseif (stripos($infos['article_url'], '.htm')) {
                $infos['article_url'] = substr($infos['article_url'], 0, stripos($infos['article_url'], '.htm') + 4);
            }
        }
    }


    preg_match('/\/\/.*?\//', $infos['article_url'], $my_domain);

    $my_domain = $my_domain[0];

    /*******************/
// 进行查询
    $query = "SELECT article_id from bbs_article WHERE ( article_url = :article_url ) or ( article_title = :article_title and article_url REGEXP :my_domain and article_addtime>" . (time() - 24 * 60 * 60) . "  ) ";
    $staff_statement = $_pdo->prepare($query);
    $staff_statement->bindParam(':article_url', $infos['article_url'], PDO::PARAM_STR);
    $staff_statement->bindParam(':article_title', $infos['article_title'], PDO::PARAM_STR);
    $staff_statement->bindParam(':my_domain', $my_domain, PDO::PARAM_STR);


    $staff_statement->execute();
    $result = $staff_statement->fetch();

    if (empty($result)) {
        if (!empty($keyword) and stripos(get_content($infos['article_url'])['content'], $keyword) === false and stripos($infos['article_title'], $keyword) === false) {
//            $filter_result = true;
            $a_type = 0;
            if (strstr($infos['article_channel'], '经销商') or strstr($infos['article_title'], '经销商') or strstr($infos['article_title'], '优惠') or
                (strstr($infos['article_title'], '广汽丰田') and (strstr($infos['article_title'], '有限公司') or strstr($infos['article_title'], '店'))) or
                (strstr($infos['article_title'], '广丰') and (strstr($infos['article_title'], '有限公司') or strstr($infos['article_title'], '店')))
            ) {
                $a_type = 1;
            }
            $query = "INSERT INTO filter_list (filter_title, filter_url, filter_pubtime, "
                . "filter_content, filter_type, filter_media, filter_property, filter_keyword, filter_atype, filter_author, filter_is_repost,filter_grade) "
                . "VALUES ( :filter_title, :filter_url, :filter_pubtime, "
                . ":filter_content, :filter_type, :filter_media, :filter_property, :filter_keyword, :filter_atype, :filter_author, :filter_is_repost, :filter_grade)";
            $staff_statement = $_pdo->prepare($query);
            $staff_statement->bindParam(':filter_title', $infos['article_title'], PDO::PARAM_STR);
            $staff_statement->bindParam(':filter_url', $infos['article_url'], PDO::PARAM_STR);
            $staff_statement->bindParam(':filter_content', $infos['article_content'], PDO::PARAM_STR);
            $staff_statement->bindParam(':filter_pubtime', $infos['article_pubtime'], PDO::PARAM_INT);
            $staff_statement->bindValue(':filter_type', 2, PDO::PARAM_INT);
            $staff_statement->bindParam(':filter_property', $infos['article_property'], PDO::PARAM_INT);
            $staff_statement->bindParam(':filter_keyword', $keyword, PDO::PARAM_STR);
            $staff_statement->bindParam(':filter_atype', $a_type, PDO::PARAM_INT);
            $staff_statement->bindParam(':filter_media', $infos['media'], PDO::PARAM_STR);
            $staff_statement->bindParam(':filter_author', $infos['article_author'], PDO::PARAM_STR);
            $staff_statement->bindParam(':filter_is_repost', $infos['article_is_repost'], PDO::PARAM_INT);
            $staff_statement->bindParam(':filter_grade', $grade, PDO::PARAM_INT);
            // 执行预处理语句
            $result = $staff_statement->execute();
            if ($result === false) {
                $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                return $result;
            }

            $log->WARN("过滤结果4(插入过滤表中), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        }
        // 如果没有保存,进行保存

        $query = "INSERT INTO bbs_article(article_url, article_title, article_content, article_pubtime,"
            . " article_addtime, article_summary, article_reply, article_click, media, forum, article_author, article_is_repost,article_grade)"
            . "VALUES (:article_url, :article_title, :article_content, :article_pubtime, :article_addtime, "
            . ":article_summary, :article_reply, :article_click, :media, :forum, :article_author, :article_is_repost, :article_grade)";

        $staff_statement = $_pdo->prepare($query);

        $staff_statement->bindParam(':article_url', $infos['article_url'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_title', $infos['article_title'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_content', $infos['article_content'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_pubtime', $infos['article_pubtime'], PDO::PARAM_INT);
        $staff_statement->bindValue(':article_addtime', time(), PDO::PARAM_INT);
        $staff_statement->bindParam(':article_summary', $infos['article_summary'], PDO::PARAM_STR);
        $staff_statement->bindValue(':article_reply', $infos['article_reply'], PDO::PARAM_INT);
        $staff_statement->bindValue(':article_click', $infos['article_click'], PDO::PARAM_INT);
        $staff_statement->bindParam(':media', $infos['media'], PDO::PARAM_STR);
        $staff_statement->bindParam(':forum', $infos['forum'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_author', $infos['article_author'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_is_repost', $infos['article_is_repost'], PDO::PARAM_INT);
        $staff_statement->bindParam(':article_grade', $grade, PDO::PARAM_INT);

        // 执行预处理语句
        $result = $staff_statement->execute();
        if ($result === false) {
            return $result;
        }
        $article_id = $_pdo->lastInsertId();
    } else {
        $article_id = $result['article_id'];
    }

    // 关键词的记录
    $key_info = array(
        'keyword' => '',
        'table_name' => 'bbs_key',
        'article_id' => $article_id,
        'article_property' => $infos['article_property'],
        'article_pubtime' => $infos['article_pubtime']
    );

    // 如果关键词为空,则为关键词命中内容
    if (empty($keyword)) {
        $k_ids = $infos['k_ids'];
        foreach ($k_ids as $k_id) {
            $key_info['k_id'] = $k_id;
            $key_result = insert_key($_pdo, $log, $key_info);
            if ($key_result === false) {
                $log->WARN("bbs_article 插入失败, url:{$infos['article_title']}");
                return false;
            }
        }
    } else {      // 正常内容
        $key_info['keyword'] = $keyword;
        $key_result = insert_key($_pdo, $log, $key_info);
        if ($key_result === false) {
            return false;
        }
    }

    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    return true;
}

// bbs列表入库
function bbs_list($infos, $log, $keyword)
{
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);

    if ($_pdo === false) {
        return false;
    }
    $infos_count = count($infos);
    $media_list = array();

    //获取媒体列表
    $query = "SELECT domain,media_name,grade  from media_list";
    $media_rows = $_pdo->query($query);
    $i = 0;
    $media_list = array();
    foreach ($media_rows as $row) {
        $media_list[$i]['domain'] = $row['domain'];
        $media_list[$i]['media_name'] = $row['media_name'];
        $media_list[$i]['grade'] = $row['grade'];
        $i++;
    }
    //$media_list  二维数组，存储域名与媒体名
    if (strstr($infos[0]['media'], '百度贴吧')) {
        //file_put_contents('baidubbs', var_export($infos,TRUE));
    }

    for ($i = 0; $i < $infos_count; $i++) {
        // 过滤结果
        $query = "SELECT f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) WHERE k.keyword = '$keyword' OR f.k_id = 0";
        $filter_rows = $_pdo->query($query);
        $filter_result = false;
        foreach ($filter_rows as $row) {
            //查找第一次出现的位置
            if (!strstr($infos[$i]['article_title'], $row['filter_word']) and !strstr($infos[$i]['article_content'], $row['filter_word'])) {
                continue;
            } else {
                $filter_result = true;
                break;
            }
        }
        //过滤条件0：(长度大于10   and  (没出现bbs or autohome)  ) or 出现六合彩  or  出现棋牌 娱乐  麻将
        if ((mb_strlen($infos[$i]['media'], 'UTF-8') > 10 and (!strstr($infos[$i]['media'], 'bbs') or !strstr($infos[$i]['media'], 'autohome'))) or strstr($infos[$i]['article_title'], '六 合 彩') or strstr($infos[$i]['article_title'], '棋牌') or strstr($infos[$i]['article_title'], '娱乐') or strstr($infos[$i]['article_title'], '麻将')) {
            $log->WARN("过滤结果0, 赌博 url:{$infos[$i]['article_url']}");
            continue;
        } //过滤条件1：(关键字为空) or 猫咪有约  or  辽一网
        else if (empty($keyword) or strstr($infos[$i]['media'], '猫咪有约') or strstr($infos[$i]['media'], '辽一网')) {//过滤猫咪有约论坛
            $filter_result = true;
            $log->WARN("过滤结果1, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        } // // 过滤结果2，将内容和标题里都没有包含关键字的结果过滤掉
        else if (!strstr($infos[$i]['article_title'], $keyword) and !strstr($infos[$i]['article_content'], $keyword) and !strstr($infos[$i]['article_summary'], $keyword)) {
            $filter_result = true;
            $log->WARN("过滤结果2, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        } // 过滤结果3，将铁血网的结果过滤掉
        else if (stripos($infos[$i]['media'], '铁血网') !== false or stripos($infos[$i]['article_summary'], '铁血网') !== false or stripos($infos[$i]['forum'], '铁血网') !== false) {
            $filter_result = true;
            $log->WARN("过滤结果3, 铁血网 url:{$infos[$i]['article_url']}");
        } // 过滤结果4，将房天下的结果过滤掉
        else if (stripos($infos[$i]['media'], '搜房网') !== false or stripos($infos[$i]['article_summary'], '搜房网') !== false or stripos($infos[$i]['forum'], '搜房网') !== false) {
            $filter_result = true;
            $log->WARN("过滤结果4, 搜房网 url:{$infos[$i]['article_url']}");
        } // 过滤结果5，将bbs其他的结果过滤掉
        else if (stripos($infos[$i]['media'], 'bbs.ilmusic.cn') !== false or stripos($infos[$i]['article_url'], 'bbs.auto.ifeng.com/forum-1020258-1') !== false or stripos($infos[$i]['article_url'], 'qingdaonews') !== false or stripos($infos[$i]['article_url'], 'xcar.com.cn/bbs/photo') !== false) {
            $filter_result = true;
            $log->WARN("过滤结果5,  url:{$infos[$i]['article_url']}");
        } // 过滤结果6，过滤精选
        else if (stripos($infos[$i]['article_url'] . '', 'pcauto.com.cn/jinxuan') !== false) {//过滤精选页面
            $filter_result = true;
            $log->WARN("过滤结果6, 精选 url:{$infos[$i]['article_url']}");
        } //add_zx
        else if ((stripos($infos[$i]['article_url'] . '', 'autohome') !== false) and (stripos($infos[$i]['article_url'] . '', 'club') === false)) {//过滤不含club页面
            $filter_result = true;
            $log->WARN("过滤结果7, 含有autohome不含club url:{$infos[$i]['article_url']}");
        }
//        if(!empty($keyword) and stripos(get_array($infos[$i]['article_url'])['TITLE'], $keyword) === false and stripos(get_array($infos[$i]['article_url'])['content'], $keyword) === false){
//            $filter_result = true;
//            $log->WARN("过滤结果4(a.php), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
//        }
        if (stripos($infos[$i]['media'], '汽车之家车型论坛') !== false) {
            $filter_result = false;//汽车之家指定车型论坛的数据，放行
            $log->INFO("汽车之家{$keyword}论坛,  url:{$infos[$i]['article_url']}");
        }

        if ((stripos($infos[$i]['article_url'], 'autohome.com.cn') !== false) and (stripos($keyword, '凯美瑞') !== false or stripos($keyword, '汉兰达') !== false or stripos($keyword, 'CHR') !== false or stripos($keyword, 'CH-R') !== false or stripos($keyword, 'C-HR') !== false or stripos($keyword, '雷凌') !== false or stripos($keyword, '致享') !== false or stripos($keyword, '致炫') !== false)) {
            $filter_result = false;//汽车之家论坛的，车型是凯美瑞/汉兰达/C-HR/雷凌/致享/致炫 的 放行
            $log->INFO("汽车之家{$keyword}论坛 汽车之家论坛的，车型是凯美瑞/汉兰达/C-HR/雷凌/致享/致炫 的 放行,  url:{$infos[$i]['article_url']}");
        }


        $media = getMedia($infos[$i]['article_url'], $media_list);
        if (!empty($media) and stripos($infos[$i]['media'], '汽车之家车型论坛') === false) {
            $infos[$i]['media'] = $media;
        }
//            if(empty($infos[$i]['media'])) {    // 如果媒体为空,进行匹配
//                continue;
//            }
        if (empty($infos[$i]['article_is_repost'])) {
            $infos[$i]['article_is_repost'] = 1;
        }
        $grade = getMedias($infos[$i]['article_url'], $media_list)['grade'];
        if (empty($grade)) {
            $grade = 3;
        }
        if ($grade == 1) {  //原来的A级别的媒体有新闻、APP、视频、论坛、博客类型的数据，这些里面新闻和APP的数据属于A级别，视频、论坛、博客属于B级别的
            $grade = 2;
        }


        //file_put_contents('?????', $infos[$i]['article_url']);
        // if($keyword == '广汽丰田' and stripos($infos[$i]['article_title'], 'C-HR') != false){
        //     continue;
        // }
        // //抓取太平洋汽车网的时间，过滤
        // if( stripos($infos[$i]['article_url'], 'bbs.pcauto.com.cn') !== false ){
        //     $html = get_html($infos[$i]['article_url']);
        //     $time_reg = '/<div class="post_time">发表于 (.*?)<\/div>/s';
        //     preg_match($time_reg, $html, $time_result);
        //     $time = strtotime($time_result[1]);
        //     if(!empty($time) && time() - $time > 60*60*24*5 ){//只要5天内的
        //         $filter_result = true;
        //         $log->WARN("过滤过期的结果,  url:{$infos[$i]['article_url']} ， time: $time");
        //     }

        // }

        if ($filter_result === true) {
            continue;
        }

        /****** wm 去除.html 或.htm 或 .shtml 后的参数**********/
        $url_arrs = array('club.autohome.com.cn');

        foreach ($url_arrs as $url_arr) {
            if (stripos($infos[$i]['article_url'], $url_arr)) {
                if (stripos($infos[$i]['article_url'], '.shtml')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.shtml') + 6);
                } elseif (stripos($infos[$i]['article_url'], '.html')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.html') + 5);
                } elseif (stripos($infos[$i]['article_url'], '.htm')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.htm') + 4);
                }
            }
        }


        preg_match('/\/\/.*?\//', $infos[$i]['article_url'], $my_domain);

        $my_domain = $my_domain[0];

        /*******************/
// 进行查询
        $query = "SELECT article_id from bbs_article WHERE ( article_url = :article_url ) or ( article_title = :article_title and article_url REGEXP :my_domain and article_addtime>" . (time() - 24 * 60 * 60) . "  ) ";
        $staff_statement = $_pdo->prepare($query);
        $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
        $staff_statement->bindParam(':my_domain', $my_domain, PDO::PARAM_STR);


        $staff_statement->execute();
        $result = $staff_statement->fetch();
        if (strstr($infos[$i]['media'], '百度贴吧')) {
            //file_put_contents('cx-'.$keyword.'=='.$infos[$i]['article_url'], var_export($result,TRUE));
            //file_put_contents($keyword, $keyword);

        }
        if (empty($result)) {
            //匹配媒体
            if (!empty($keyword) and stripos(get_content($infos[$i]['article_url'])['content'], $keyword) === false and stripos($infos[$i]['article_title'], $keyword) === false) {
//            $filter_result = true;
                $a_type = 0;
                if (strstr($infos[$i]['article_channel'], '经销商') or strstr($infos[$i]['article_title'], '经销商') or strstr($infos[$i]['article_title'], '优惠') or
                    (strstr($infos[$i]['article_title'], '广汽丰田') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店'))) or
                    (strstr($infos[$i]['article_title'], '广丰') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')))
                ) {
                    $a_type = 1;
                }
                $query = "INSERT INTO filter_list (filter_title, filter_url, filter_pubtime, "
                    . "filter_content, filter_type, filter_media, filter_property, filter_keyword, filter_atype, filter_author, filter_is_repost,filter_grade) "
                    . "VALUES ( :filter_title, :filter_url, :filter_pubtime, "
                    . ":filter_content, :filter_type, :filter_media, :filter_property, :filter_keyword, :filter_atype, :filter_author, :filter_is_repost, :filter_grade)";
                $staff_statement = $_pdo->prepare($query);
                $staff_statement->bindParam(':filter_title', $infos[$i]['article_title'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_url', $infos[$i]['article_url'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_content', $infos[$i]['article_content'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
                $staff_statement->bindValue(':filter_type', 2, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_property', $infos[$i]['article_property'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_keyword', $keyword, PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_atype', $a_type, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_media', $infos[$i]['media'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_author', $infos[$i]['article_author'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_grade', $grade, PDO::PARAM_INT);
                // 执行预处理语句
                $result = $staff_statement->execute();
                if ($result === false) {
                    $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                    return $result;
                }

                $log->WARN("过滤结果4(插入过滤表中), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            }
            // 如果没有保存,进行保存
            $query = "INSERT INTO bbs_article(article_url, article_title, article_content, article_pubtime,"
                . " article_addtime, article_summary, article_reply, article_click, media, forum, article_author, article_is_repost,article_grade)"
                . "VALUES (:article_url, :article_title, :article_content, :article_pubtime, :article_addtime, "
                . ":article_summary, :article_reply, :article_click, :media, :forum, :article_author, :article_is_repost, :article_grade)";

            $staff_statement = $_pdo->prepare($query);

            $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_content', $infos[$i]['article_content'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
            $staff_statement->bindValue(':article_addtime', time(), PDO::PARAM_INT);
            $staff_statement->bindParam(':article_summary', $infos[$i]['article_summary'], PDO::PARAM_STR);
            $staff_statement->bindValue(':article_reply', $infos[$i]['article_reply'], PDO::PARAM_INT);
            $staff_statement->bindValue(':article_click', $infos[$i]['article_click'], PDO::PARAM_INT);
            $staff_statement->bindParam(':media', $infos[$i]['media'], PDO::PARAM_STR);
            $staff_statement->bindParam(':forum', $infos[$i]['forum'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_author', $infos['article_author'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_is_repost', $infos['article_is_repost'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_grade', $grade, PDO::PARAM_INT);

            // 执行预处理语句
            $result = $staff_statement->execute();
            if ($result === false) {
                $log->WARN("bbs_list 插入失败, url:{$infos[$i]['article_title']}");
                return $result;
            }
            if (strstr($infos[$i]['media'], '百度贴吧')) {
                //file_put_contents('baiduresult', var_export($result,TRUE));
            }
            $article_id = $_pdo->lastInsertId();
        } else {
            $article_id = $result['article_id'];
        }

        // 关键词的记录
        $key_info = array(
            'keyword' => $keyword,
            'table_name' => 'bbs_key',
            'article_id' => $article_id,
            'article_property' => $infos[$i]['article_property'],
            'article_pubtime' => $infos[$i]['article_pubtime']
        );
        $key_result = insert_key($_pdo, $log, $key_info);
        if (strstr($infos[$i]['media'], '百度贴吧')) {
            //file_put_contents('baidukey', var_export($key_result,TRUE));
        }
        if ($key_result === false) {
            return false;
        }
    }

    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    return true;
}

function zhidao_list($infos, $log, $keyword)
{
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);

    if ($_pdo === false) {
        return false;
    }
    //获取媒体列表
    $query = "SELECT domain,media_name,grade  from media_list";
    $media_rows = $_pdo->query($query);
    $i = 0;
    $media_list = array();
    foreach ($media_rows as $row) {
        $media_list[$i]['domain'] = $row['domain'];
        $media_list[$i]['media_name'] = $row['media_name'];
        $media_list[$i]['grade'] = $row['grade'];
        $i++;
    }
    $infos_count = count($infos);

    for ($i = 0; $i < $infos_count; $i++) {
        // 过滤结果
        $query = "SELECT f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) WHERE k.keyword = '$keyword' OR f.k_id = 0";
        $filter_rows = $_pdo->query($query);
        $filter_result = false;
        foreach ($filter_rows as $row) {
            if (stripos($infos[$i]['article_title'], $row['filter_word']) === false and stripos($infos[$i]['article_summary'], $row['filter_word']) === false) {
                continue;
            } else {
                $filter_result = true;
                break;
            }
        }
        //再次过滤
        if (empty($infos[$i]['article_is_repost'])) {
            $infos[$i]['article_is_repost'] = 1;
        }
        $grade = getMedias($infos[$i]['article_url'], $media_list)['grade'];
        if (empty($grade)) {
            $grade = 3;
        }

        if ($grade == 1) {  //原来的A级别的媒体有新闻、APP、视频、论坛、博客类型的数据，这些里面新闻和APP的数据属于A级别，视频、论坛、博客属于B级别的
            $grade = 2;
        }

        if ($filter_result === true) {
            continue;
        }

        /****** wm 去除.html 或.htm 或 .shtml 后的参数**********/
        $url_arrs = array('club.autohome.com.cn');

        foreach ($url_arrs as $url_arr) {
            if (stripos($infos[$i]['article_url'], $url_arr)) {
                if (stripos($infos[$i]['article_url'], '.shtml')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.shtml') + 6);
                } elseif (stripos($infos[$i]['article_url'], '.html')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.html') + 5);
                } elseif (stripos($infos[$i]['article_url'], '.htm')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.htm') + 4);
                }
            }
        }


        preg_match('/\/\/.*?\//', $infos[$i]['article_url'], $my_domain);

        $my_domain = $my_domain[0];

        /*******************/
// 进行查询
        $query = "SELECT article_id from zhidao_article WHERE ( article_url = :article_url ) or ( article_title = :article_title and article_url REGEXP :my_domain and article_addtime>" . (time() - 24 * 60 * 60) . "  ) ";
        $staff_statement = $_pdo->prepare($query);
        $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
        $staff_statement->bindParam(':my_domain', $my_domain, PDO::PARAM_STR);


        $staff_statement->execute();
        $result = $staff_statement->fetch();

        if (empty($result)) {
            if (!empty($keyword) and stripos(get_content($infos[$i]['article_url'])['content'], $keyword) === false and stripos($infos[$i]['article_title'], $keyword) === false) {
//            $filter_result = true;
                $a_type = 0;
                if (strstr($infos[$i]['article_channel'], '经销商') or strstr($infos[$i]['article_title'], '经销商') or strstr($infos[$i]['article_title'], '优惠') or
                    (strstr($infos[$i]['article_title'], '广汽丰田') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店'))) or
                    (strstr($infos[$i]['article_title'], '广丰') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')))
                ) {
                    $a_type = 1;
                }
                $query = "INSERT INTO filter_list (filter_title, filter_url, filter_pubtime, "
                    . "filter_content, filter_type, filter_media, filter_property, filter_keyword, filter_atype, filter_author, filter_is_repost,filter_grade) "
                    . "VALUES ( :filter_title, :filter_url, :filter_pubtime, "
                    . ":filter_content, :filter_type, :filter_media, :filter_property, :filter_keyword, :filter_atype, :filter_author, :filter_is_repost, :filter_grade)";
                $staff_statement = $_pdo->prepare($query);
                $staff_statement->bindParam(':filter_title', $infos[$i]['article_title'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_url', $infos[$i]['article_url'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_content', $infos[$i]['article_summary'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
                $staff_statement->bindValue(':filter_type', 7, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_property', $infos[$i]['article_property'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_keyword', $keyword, PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_atype', $a_type, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_media', $infos[$i]['media'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_author', $infos[$i]['article_author'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_grade', $grade, PDO::PARAM_INT);
                // 执行预处理语句
                $result = $staff_statement->execute();
                if ($result === false) {
                    $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                    return $result;
                }

                $log->WARN("过滤结果4(插入过滤表中), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            }
            // 如果没有保存,进行保存

            $query = "INSERT INTO zhidao_article(article_url, article_title, article_pubtime,"
                . " article_addtime, article_summary, media, author, mid, article_author, article_is_repost,article_grade)"
                . "VALUES (:article_url, :article_title, :article_pubtime,"
                . " :article_addtime, :article_summary, :media, :author, :mid, :article_author, :article_is_repost,:article_grade)";

            $staff_statement = $_pdo->prepare($query);
            $mid = ' ';
            $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
            $staff_statement->bindValue(':article_addtime', time(), PDO::PARAM_INT);
            $staff_statement->bindParam(':article_summary', $infos[$i]['article_summary'], PDO::PARAM_STR);
            $staff_statement->bindParam(':media', $infos[$i]['media'], PDO::PARAM_STR);
            $staff_statement->bindParam(':author', $infos[$i]['author'], PDO::PARAM_STR);
            $staff_statement->bindParam(':mid', $mid, PDO::PARAM_STR);
            $staff_statement->bindParam(':article_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_author', $infos[$i]['author'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_grade', $grade, PDO::PARAM_INT);
            // 执行预处理语句
            $result = $staff_statement->execute();
            if ($result === false) {
                $log->WARN("zhidao_list 插入失败, url:{$infos[$i]['article_title']}");
                return $result;
            }
            $article_id = $_pdo->lastInsertId();
        } else {
            $article_id = $result['article_id'];
        }

        // 关键词的记录
        $key_info = array(
            'keyword' => $keyword,
            'table_name' => 'zhidao_key',
            'article_id' => $article_id,
            'article_property' => $infos[$i]['article_property'],
            'article_pubtime' => $infos[$i]['article_pubtime']
        );
        $key_result = insert_key($_pdo, $log, $key_info);
        if ($key_result === false) {
            return false;
        }
    }

    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    return true;
}

function app_list($infos, $log, $keyword)
{
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
//    file_put_contents('app_list_data', var_export($infos, true));
    if ($_pdo === false) {
        return false;
    }
    $infos_count = count($infos);
    /**************zx**********************/

    //获取媒体列表
    $query = "SELECT domain,media_name,grade  from media_list";
    $media_rows = $_pdo->query($query);
    $i = 0;
    $media_list = array();
    foreach ($media_rows as $row) {
        $media_list[$i]['domain'] = $row['domain'];
        $media_list[$i]['media_name'] = $row['media_name'];
        $media_list[$i]['grade'] = $row['grade'];
        $i++;
    }
    /************************************/
    for ($i = 0; $i < $infos_count; $i++) {
        file_put_contents('app_list_data[i]', var_export($infos[$i], true));
        // 过滤结果
        $query = "SELECT f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) WHERE k.keyword = '$keyword' OR f.k_id = 0";
        $filter_rows = $_pdo->query($query);
        $filter_result = false;
        foreach ($filter_rows as $row) {
            if (stripos($infos[$i]['article_title'], $row['filter_word']) === false and stripos($infos[$i]['article_content'], $row['filter_word']) === false) {
                continue;
            } else {
                $filter_result = true;
                break;
            }
        }
        if (empty($infos[$i]['article_title'])) {
            continue;

        }
        // 过滤结果，将内容和标题里都没有包含关键字的结果过滤掉
        if ($infos[$i]['media'] == 'Zaker' and strstr($infos[$i]['article_title'], $keyword) === false and strstr($infos[$i]['article_content'], $keyword) === false) {
            $filter_result = true;
            $log->WARN("过滤没有关键字的结果-app, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        }
//        if(!empty($keyword) and stripos(get_array($infos[$i]['article_url'])['TITLE'], $keyword) === false and stripos(get_array($infos[$i]['article_url'])['content'], $keyword) === false){
//            $filter_result = true;
//            $log->WARN("过滤结果4(a.php), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
//        }
        if ($infos[$i]['media'] == 'uc头条APP') {//and !strstr($infos[$i]['article_title'], $keyword) and !strstr($infos[$i]['article_content'], $keyword) ){
            //$filter_result = true;
            $log->WARN("记录-app-{$infos[$i]['media']}, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        }

        if (empty($keyword) or $keyword == ' ') {
            continue;
        }
        /********************zx* 得到媒体名*******************************/
        $media = getMedia($infos[$i]['article_url'], $media_list);
        if (!empty($media)) {
            $infos[$i]['media'] = $media;
        }
        $grade = getMedias($infos[$i]['article_url'], $media_list)['grade'];
        if (empty($grade)) {
            $grade = 3;
        }
        if (empty($infos[$i]['article_is_repost'])) {
            $infos[$i]['article_is_repost'] = 1;
        }

        if ($filter_result === true) {
            continue;
        }

        /****** wm 去除.html 或.htm 或 .shtml 后的参数**********/
        $url_arrs = array('club.autohome.com.cn');

        foreach ($url_arrs as $url_arr) {
            if (stripos($infos[$i]['article_url'], $url_arr)) {
                if (stripos($infos[$i]['article_url'], '.shtml')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.shtml') + 6);
                } elseif (stripos($infos[$i]['article_url'], '.html')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.html') + 5);
                } elseif (stripos($infos[$i]['article_url'], '.htm')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.htm') + 4);
                }
            }
        }


        preg_match('/\/\/.*?\//', $infos[$i]['article_url'], $my_domain);

        $my_domain = $my_domain[0];

        /*******************/
// 进行查询
        $query = "SELECT article_id from app_article WHERE ( article_url = :article_url ) or ( article_title = :article_title and article_url REGEXP :my_domain and article_addtime>" . (time() - 24 * 60 * 60) . "  ) ";
        $staff_statement = $_pdo->prepare($query);
        $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
        $staff_statement->bindParam(':my_domain', $my_domain, PDO::PARAM_STR);


        $staff_statement->execute();
        $result = $staff_statement->fetch();

        if (empty($result)) {
            if (!empty($keyword) and stripos(get_content($infos[$i]['article_url'])['content'], $keyword) === false and stripos($infos[$i]['article_title'], $keyword) === false) {
//            $filter_result = true;
                $a_type = 0;
                if (strstr($infos[$i]['article_channel'], '经销商') or strstr($infos[$i]['article_title'], '经销商') or strstr($infos[$i]['article_title'], '优惠') or
                    (strstr($infos[$i]['article_title'], '广汽丰田') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店'))) or
                    (strstr($infos[$i]['article_title'], '广丰') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')))
                ) {
                    $a_type = 1;
                }
                $query = "INSERT INTO filter_list (filter_title, filter_url, filter_pubtime, "
                    . "filter_content, filter_type, filter_media, filter_property, filter_keyword, filter_atype, filter_author, filter_is_repost,filter_grade) "
                    . "VALUES ( :filter_title, :filter_url, :filter_pubtime, "
                    . ":filter_content, :filter_type, :filter_media, :filter_property, :filter_keyword, :filter_atype, :filter_author, :filter_is_repost, :filter_grade)";
                $staff_statement = $_pdo->prepare($query);
                $staff_statement->bindParam(':filter_title', $infos[$i]['article_title'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_url', $infos[$i]['article_url'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_content', $infos[$i]['article_content'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
                $staff_statement->bindValue(':filter_type', 8, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_property', $infos[$i]['article_property'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_keyword', $keyword, PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_atype', $a_type, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_media', $infos[$i]['media'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_author', $infos[$i]['article_author'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_grade', $grade, PDO::PARAM_INT);
                // 执行预处理语句
                $result = $staff_statement->execute();
                if ($result === false) {
                    $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                    return $result;
                }

                $log->WARN("过滤结果4(插入过滤表中), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            }
            // 如果没有保存,进行保存

            try {
                $query = "INSERT INTO app_article (article_url, article_title, article_content, article_pubtime,"
                    . " article_addtime, media, article_summary, article_channel, article_author, article_is_repost,article_grade) "
                    . "VALUES (:article_url, :article_title, :article_content, :article_pubtime,"
                    . " :article_addtime, :media, :article_summary, :article_channel, :article_author, :article_is_repost, :article_grade)";

                $staff_statement = $_pdo->prepare($query);

                $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_content', $infos[$i]['article_content'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
                $staff_statement->bindValue(':article_addtime', time(), PDO::PARAM_INT);
                $staff_statement->bindParam(':media', $infos[$i]['media'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_summary', $infos[$i]['article_summary'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_channel', $infos[$i]['article_channel'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_author', $infos[$i]['article_author'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
                $staff_statement->bindParam(':article_grade', $grade, PDO::PARAM_INT);
                // 执行预处理语句
                file_put_contents('app_list_sql', var_export($query, true));
                $result = $staff_statement->execute();
            } catch (PDOException $ex) {
                $log->WARN('插入失败: ' . $ex->getMessage());
            }
            if ($result === false) {
                $log->WARN("app_article 插入失败, url:{$infos[$i]['article_title']}");
                return $result;
            }
            $article_id = $_pdo->lastInsertId();

            // 将入库的信息插入到过滤表中
            $query = "INSERT INTO app_filter_ids (ids, media) VALUES (:ids, :media)";
            $staff_statement = $_pdo->prepare($query);
            $staff_statement->bindParam(':ids', $infos[$i]['ids'], PDO::PARAM_INT);
            $staff_statement->bindParam(':media', $infos[$i]['media'], PDO::PARAM_STR);
            $staff_statement->execute();
        } else {
            $article_id = $result['article_id'];
        }

        // 关键词的记录
        $key_info = array(
            'keyword' => '',
            'table_name' => 'app_key',
            'article_id' => $article_id,
            'article_property' => $infos[$i]['article_property'],
            'article_pubtime' => $infos[$i]['article_pubtime']
        );
        $k_ids = $infos[$i]['k_ids'];
        foreach ($k_ids as $k_id) {
            $key_info['k_id'] = $k_id;
            $key_result = insert_key($_pdo, $log, $key_info);
            if ($key_result === false) {
                return false;
            }
        }
    }

    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    return true;
}

// blog_list写入操作
function blog_list($infos, $log, $keyword)
{
    $insert_stack = array();
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);

    if ($_pdo === false) {
        return false;
    }

    $infos_count = count($infos);

    /**************zx**********************/
    //获取媒体列表
    $query = "SELECT domain,media_name,grade  from media_list";
    $media_rows = $_pdo->query($query);
    $i = 0;
    $media_list = array();
    foreach ($media_rows as $row) {
        $media_list[$i]['domain'] = $row['domain'];
        $media_list[$i]['media_name'] = $row['media_name'];
        $media_list[$i]['grade'] = $row['grade'];
        $i++;
    }
    /************************************/
    for ($i = 0; $i < $infos_count; $i++) {
        // 过滤结果
        $query = "SELECT f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) WHERE k.keyword = '$keyword' OR f.k_id = 0";
        $filter_rows = $_pdo->query($query);
        $filter_result = false;
        foreach ($filter_rows as $row) {
            if (stripos($infos[$i]['article_title'], $row['filter_word']) === false and stripos($infos[$i]['article_content'], $row['filter_word']) === false) {
                continue;
            } else {
                $filter_result = true;
                break;
            }
        }
        // 过滤结果2，将内容和标题里都没有包含关键字的结果过滤掉
        if (stripos($infos[$i]['article_title'], $keyword) === false and stripos($infos[$i]['article_content'], $keyword) === false) {
            $filter_result = true;
            $log->WARN("过滤结果2, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        }
        if (empty($infos[$i]['article_is_repost'])) {
            $infos[$i]['article_is_repost'] = 1;
        }
        $grade = getMedias($infos[$i]['article_url'], $media_list)['grade'];
        if (empty($grade)) {
            $grade = 3;
        }
        if ($grade == 1) {  //原来的A级别的媒体有新闻、APP、视频、论坛、博客类型的数据，这些里面新闻和APP的数据属于A级别，视频、论坛、博客属于B级别的
            $grade = 2;
        }
        if ($filter_result === true) {
            continue;
        }

        /****** wm 去除.html 或.htm 或 .shtml 后的参数**********/
        $url_arrs = array('club.autohome.com.cn');

        foreach ($url_arrs as $url_arr) {
            if (stripos($infos[$i]['article_url'], $url_arr)) {
                if (stripos($infos[$i]['article_url'], '.shtml')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.shtml') + 6);
                } elseif (stripos($infos[$i]['article_url'], '.html')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.html') + 5);
                } elseif (stripos($infos[$i]['article_url'], '.htm')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.htm') + 4);
                }
            }
        }


        preg_match('/\/\/.*?\//', $infos[$i]['article_url'], $my_domain);

        $my_domain = $my_domain[0];

        /*******************/
// 进行查询
        $query = "SELECT article_id from blog_article WHERE ( article_url = :article_url ) or ( article_title = :article_title and article_url REGEXP :my_domain and article_addtime>" . (time() - 24 * 60 * 60) . "  ) ";
        $staff_statement = $_pdo->prepare($query);
        $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
        $staff_statement->bindParam(':my_domain', $my_domain, PDO::PARAM_STR);


        $staff_statement->execute();
        $result = $staff_statement->fetch();

        if (empty($result)) {
            if (!empty($keyword) and stripos(get_content($infos[$i]['article_url'])['content'], $keyword) === false and stripos($infos[$i]['article_title'], $keyword) === false) {
//            $filter_result = true;
                $a_type = 0;
                if (strstr($infos[$i]['article_channel'], '经销商') or strstr($infos[$i]['article_title'], '经销商') or strstr($infos[$i]['article_title'], '优惠') or
                    (strstr($infos[$i]['article_title'], '广汽丰田') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店'))) or
                    (strstr($infos[$i]['article_title'], '广丰') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')))
                ) {
                    $a_type = 1;
                }
                $query = "INSERT INTO filter_list (filter_title, filter_url, filter_pubtime, "
                    . "filter_content, filter_type, filter_media, filter_property, filter_keyword, filter_atype, filter_author, filter_is_repost,filter_grade) "
                    . "VALUES ( :filter_title, :filter_url, :filter_pubtime, "
                    . ":filter_content, :filter_type, :filter_media, :filter_property, :filter_keyword, :filter_atype, :filter_author, :filter_is_repost, :filter_grade)";
                $staff_statement = $_pdo->prepare($query);
                $staff_statement->bindParam(':filter_title', $infos[$i]['article_title'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_url', $infos[$i]['article_url'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_content', $infos[$i]['article_content'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
                $staff_statement->bindValue(':filter_type', 3, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_property', $infos[$i]['article_property'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_keyword', $keyword, PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_atype', $a_type, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_media', $infos[$i]['media'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_author', $infos[$i]['article_author'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_grade', $grade, PDO::PARAM_INT);
                // 执行预处理语句
                $result = $staff_statement->execute();
                if ($result === false) {
                    $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                    return $result;
                }

                $log->WARN("过滤结果4(插入过滤表中), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            }
            // 如果没有保存,进行保存

            $query = "INSERT INTO blog_article (article_title, article_url, article_content, article_pubtime, "
                . "article_addtime, article_summary, media, author, article_author, article_is_repost,article_grade) "
                . "VALUES (:article_title, :article_url, :article_content, :article_pubtime, "
                . ":article_addtime, :article_summary, :media, :author, :article_author, :article_is_repost, :article_grade)";
            $staff_statement = $_pdo->prepare($query);

            $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_content', $infos[$i]['article_content'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
            $staff_statement->bindValue(':article_addtime', time(), PDO::PARAM_INT);
            $staff_statement->bindParam(':article_summary', $infos[$i]['article_summary'], PDO::PARAM_STR);
            $staff_statement->bindParam(':media', $infos[$i]['media'], PDO::PARAM_STR);
            $staff_statement->bindParam(':author', $infos[$i]['author'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
            $staff_statement->bindParam(':article_author', $infos[$i]['author'], PDO::PARAM_STR);
            $staff_statement->bindParam(':article_grade', $grade, PDO::PARAM_INT);
            // 执行预处理语句
            $result = $staff_statement->execute();
            if ($result === false) {
                $log->WARN("blog_article 插入失败, url:{$infos[$i]['article_title']}");
                return $result;
            }
            $article_id = $_pdo->lastInsertId();
        } else {
            $article_id = $result['article_id'];
        }

        // 关键词的记录
        $key_info = array(
            'keyword' => $keyword,
            'table_name' => 'blog_key',
            'article_id' => $article_id,
            'article_property' => $infos[$i]['article_property'],
            'article_pubtime' => $infos[$i]['article_pubtime']
        );
        $key_result = insert_key($_pdo, $log, $key_info);
        if ($key_result === false) {
            return false;
        }
    }
    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    return true;
}

// appsearch
function appsearch_list($infos, $log, $keyword)
{
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);

    if ($_pdo === false) {
        return false;
    }
    $infos_count = count($infos);

    //获取媒体列表
    $query = "SELECT domain,media_name,grade  from media_list";
    $media_rows = $_pdo->query($query);
    $i = 0;
    $media_list = array();
    foreach ($media_rows as $row) {
        $media_list[$i]['domain'] = $row['domain'];
        $media_list[$i]['media_name'] = $row['media_name'];
        $media_list[$i]['grade'] = $row['grade'];
        $i++;
    }

    for ($i = 0; $i < $infos_count; $i++) {

        // 过滤结果
        $query = "SELECT f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) WHERE k.keyword = '$keyword' OR f.k_id = 0";
        $filter_rows = $_pdo->query($query);
        $filter_result = false;
        foreach ($filter_rows as $row) {
            if (stripos($infos[$i]['article_title'], $row['filter_word']) === false and stripos($infos[$i]['article_content'], $row['filter_word']) === false) {
                continue;
            } else {
                $filter_result = true;
                break;
            }
        }

        // 过滤结果，将内容和标题里都没有包含关键字的结果过滤掉
        if ($infos[$i]['media'] == 'Zaker' and strstr($infos[$i]['article_title'], $keyword) === false and strstr($infos[$i]['article_content'], $keyword) === false) {
            $filter_result = true;
            $log->WARN("过滤没有关键字的结果-appsearch, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        }
        //防止广汽丰田的爬取结果中出现C-HR
        if ($keyword == '广汽丰田' and stripos($infos[$i]['article_title'], 'C-HR') != false) {
            continue;
        }
        $media = getMedia($infos[$i]['article_url'], $media_list);
        if (!empty($media)) {
            $infos[$i]['media'] = $media;
        }
        if (empty($infos[$i]['article_is_repost'])) {
            $infos[$i]['article_is_repost'] = 1;
        }
        $grade = getMedias($infos[$i]['article_url'], $media_list)['grade'];
        if (empty($grade)) {
            $grade = 3;
        }

        if ($filter_result === true) {
            continue;
        }

        /****** wm 去除.html 或.htm 或 .shtml 后的参数**********/
        $url_arrs = array('club.autohome.com.cn');

        foreach ($url_arrs as $url_arr) {
            if (stripos($infos[$i]['article_url'], $url_arr)) {
                if (stripos($infos[$i]['article_url'], '.shtml')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.shtml') + 6);
                } elseif (stripos($infos[$i]['article_url'], '.html')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.html') + 5);
                } elseif (stripos($infos[$i]['article_url'], '.htm')) {
                    $infos[$i]['article_url'] = substr($infos[$i]['article_url'], 0, stripos($infos[$i]['article_url'], '.htm') + 4);
                }
            }
        }


        preg_match('/\/\/.*?\//', $infos[$i]['article_url'], $my_domain);

        $my_domain = $my_domain[0];

        /*******************/
// 进行查询
        $query = "SELECT article_id from app_article WHERE ( article_url = :article_url ) or ( article_title = :article_title and article_url REGEXP :my_domain and article_addtime>" . (time() - 24 * 60 * 60) . "  ) ";
        $staff_statement = $_pdo->prepare($query);
        $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
        $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
        $staff_statement->bindParam(':my_domain', $my_domain, PDO::PARAM_STR);


        $staff_statement->execute();
        $result = $staff_statement->fetch();

        if (empty($result)) {


            if (!empty($keyword) and stripos(get_content($infos[$i]['article_url'])['content'], $keyword) === false and stripos($infos[$i]['article_title'], $keyword) === false) {
//            $filter_result = true;
                $a_type = 0;
                if (strstr($infos[$i]['article_channel'], '经销商') or strstr($infos[$i]['article_title'], '经销商') or strstr($infos[$i]['article_title'], '优惠') or
                    (strstr($infos[$i]['article_title'], '广汽丰田') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店'))) or
                    (strstr($infos[$i]['article_title'], '广丰') and (strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')))
                ) {
                    $a_type = 1;
                }
                $query = "INSERT INTO filter_list (filter_title, filter_url, filter_pubtime, "
                    . "filter_content, filter_type, filter_media, filter_property, filter_keyword, filter_atype, filter_author, filter_is_repost,filter_grade) "
                    . "VALUES ( :filter_title, :filter_url, :filter_pubtime, "
                    . ":filter_content, :filter_type, :filter_media, :filter_property, :filter_keyword, :filter_atype, :filter_author, :filter_is_repost, :filter_grade)";
                $staff_statement = $_pdo->prepare($query);
                $staff_statement->bindParam(':filter_title', $infos[$i]['article_title'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_url', $infos[$i]['article_url'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_content', $infos[$i]['article_content'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
                $staff_statement->bindValue(':filter_type', 8, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_property', $infos[$i]['article_property'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_keyword', $keyword, PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_atype', $a_type, PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_media', $infos[$i]['media'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_author', $infos[$i]['article_author'], PDO::PARAM_STR);
                $staff_statement->bindParam(':filter_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
                $staff_statement->bindParam(':filter_grade', $grade, PDO::PARAM_INT);
                // 执行预处理语句
                $result = $staff_statement->execute();
                if ($result === false) {
                    $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                    return $result;
                }

                $log->WARN("过滤结果4(插入过滤表中), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            }
            // 如果没有保存,进行保存
            try {

                $query = "INSERT INTO app_article (article_url, article_title, article_content, article_pubtime,"
                    . " article_addtime, media, article_summary, article_channel, article_author, article_is_repost,article_grade) "
                    . "VALUES (:article_url, :article_title, :article_content, :article_pubtime,"
                    . " :article_addtime, :media, :article_summary, :article_channel, :article_author, :article_is_repost, :article_grade)";

                $staff_statement = $_pdo->prepare($query);

                $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_title', $infos[$i]['article_title'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_content', $infos[$i]['article_content'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_pubtime', $infos[$i]['article_pubtime'], PDO::PARAM_INT);
                $staff_statement->bindValue(':article_addtime', time(), PDO::PARAM_INT);
                $staff_statement->bindParam(':media', $infos[$i]['media'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_summary', $infos[$i]['article_summary'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_channel', $infos[$i]['article_channel'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_author', $infos[$i]['article_author'], PDO::PARAM_STR);
                $staff_statement->bindParam(':article_is_repost', $infos[$i]['article_is_repost'], PDO::PARAM_INT);
                $staff_statement->bindParam(':article_grade', $grade, PDO::PARAM_INT);
                // 执行预处理语句
                $result = $staff_statement->execute();
            } catch (PDOException $ex) {
                $log->WARN('插入失败: ' . $ex->getMessage());
            }
            if ($result === false) {
                $log->WARN("app_article 插入失败, url:{$infos[$i]['article_title']}");
                return $result;
            }
            $article_id = $_pdo->lastInsertId();
        } else {
            $article_id = $result['article_id'];
        }

        // 关键词的记录
        $key_info = array(
            'keyword' => $keyword,
            'table_name' => 'app_key',
            'article_id' => $article_id,
            'article_property' => $infos[$i]['article_property'],
            'article_pubtime' => $infos[$i]['article_pubtime']
        );
        $key_result = insert_key($_pdo, $log, $key_info);
        if ($key_result === false) {
            return false;
        }
    }

    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    return true;
}

// 获取的html,带模拟登陆
function get_html($url, $cookie = '', $proxy = '', $proxy_port = '', $referer = '')
{
    $ch = curl_init();
    // 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);      // 60秒超时

    if ($cookie != '') {
        $coo = "Cookie:$cookie";
        $headers[] = $coo;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($referer != '') {
        curl_setopt($ch, CURLOPT_REFERER, $referer);
    }
    if ($proxy != '' and $proxy_port != '') {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }

    // 获取内容
    $output = curl_exec($ch);
    $output = iconv("gb2312", "utf-8//IGNORE", $output);
    curl_close($ch);
    return $output;
}