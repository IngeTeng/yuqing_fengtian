<?php
include_once("configure.php");
include_once("lib.mds.function.php");
include_once("web_functions.php");

require('../config.php');
require('post2sign.php');
require('log.php');
require('observer.php');


ini_set('display_errors',1);            //错误信息
ini_set('display_startup_errors',1);    //php启动错误信息
error_reporting(-1);                    //打印出所有的 错误信息
ini_set('error_log', dirname(__FILE__) . '/error_log.txt'); //将出错信息输出到一个文本文件

$log = Log::Init(LOG_HANDLER, LOG_LEVEL);

class PDOFactory {
    public static function getPDO($log = '', $db_host, $db_name, $username, $password, $options=array()) {
        $dsn = "mysql:dbname=". $db_name. ";host=". $db_host;

        $pdo_key = self::getKey($dsn, $username, $password, $options);
        if(!isset($GLOBALS['PDOS']) or !($GLOBALS['PDOS'][$pdo_key] instanceof PDO)) {
            try {
                $GLOBALS['PDOS'][$pdo_key] = new PDO($dsn, $username, $password, $options);
                $GLOBALS['PDOS'][$pdo_key]->query("SET NAMES utf8");
                $GLOBALS['PDOS'][$pdo_key]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                if(!empty($log)) {
                    $log->INFO("新建数据库连接成功, pdo_key = $pdo_key");
                }
            } catch (PDOException $ex) {
                if(!empty($log)) {
                    $log->WARN("数据库连接失败, ". $ex->getMessage());
                }
                return false;
            }
        }
        return $GLOBALS['PDOS'][$pdo_key];
    }

    public static function getKey($dsn, $username, $password, $options=array()) {
        return md5(serialize(array($dsn, $username, $password, $options)));
    }

    public static function rollBack($_pdo, $insert_stack) {
        foreach($insert_stack as $insert_row) {
            $query = "DELETE FROM {$insert_row['table_name']} WHERE {$insert_row['id_name']} = {$insert_row['insert_id']}";
            $result = $_pdo->query($query);
        }
    }

    public static function unsetPDO($dsn, $username, $password, $options=array()) {
        $pdo_key = self::getKey($dsn, $username, $password, $options);
        if(isset($GLOBALSS['PDOS'][$pdo_key])) {
            unset($GLOBALSS['PDOS'][$pdo_key]);
        }
    }
}
function html_get($url, $cookie="", $referer="")
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3); 
    if ($cookie != "")
    {
    	$coo = "Cookie: " . $cookie;
    	$headers[] = $coo;
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($referer != "")
    {
        curl_setopt($ch,CURLOPT_REFERER,$referer);
    }
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function getXmlValue($content, $start, $end)
{
	$pstart = strpos($content, $start);
        if ($pstart > 0 || $pstart === 0)
        {
                $pstart += strlen($start);
                $sub_content = substr($content, $pstart);
                $pend = strpos($sub_content, $end);
                if ($pend > 0 || $pend === 0)
                {
                        $a = substr($sub_content, 0, $pend);
                        return $a;
                }
        }
        return "";
	
}

function article_property($host, $port, $title, $content)
{
        $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        if (false === socket_connect($socket, $host, $port)) {
                echo 'Can not connect to Server [' . $host . ':' . $port . '].';
                return 0;
        }
        $body = "<title><![CDATA[".$title."]]></title>\r\n";
        $body .= "<content><![CDATA[".$content."]]></content>\r\n";
        if (false === eget_send($socket, $body, strlen($body))) {
                echo 'Can not write body info to Server [' . $host . ':' . $port . '].';
                return 0;
        }
        $contentLength = 0;
	$head = '';
        if (false === eget_read_head($socket, $head, $contentLength)) {
                echo 'Can not read body info HEAD of Server.';
                return 0;
        }

        $data = '';
        if (false === eget_read($socket, $data, $contentLength)) {
                echo 'Can not read info BODY of Server.';
                return 0;
        }
		$result = strstr($data, '<title');
        $pstart = strpos($result, '<title');
        $title_array = array();
        $i=0;
		$property=0;
        while ($pstart > 0 || $pstart === 0){
			$pend = strpos($result, '</title>');
			$record = substr($result, $pstart, $pend - $pstart); 
			$property = getXmlValue($record, '<cid>', "</cid>");
			if($property==2){  
			    return $property;
			}
			$result = substr($result, $pend+ strlen('</title>'));
			$pstart = strpos($result, '<title');
			$title_array[$i]=$property;
			$i++;
        }
		if(count($title_array)>0){ 
		    $property=1;
		    return $property;
	    }

		$result = strstr($data, '<content');
        $pstart = strpos($result, '<content');
        $content_array = array();
        $i=0;
        while ($pstart > 0 || $pstart === 0){
			$pend = strpos($result, '</content>');
			$record = substr($result, $pstart, $pend - $pstart); 
			$property = getXmlValue($record, '<cid>', "</cid>");
			if($property==2){  
			    return $property;
			}
			$result = substr($result, $pend+ strlen('</content>'));
			$pstart = strpos($result, '<content');
			$content_array[$i]=$property;
			$i++;
        }

		if(count($content_array)>0){ 
		    $property=1;
		    return $property;

	    }		
		return $property;
}
function get_media($uri,&$media_list)
{
	$media_name="";
	for($i=0;$i<count($media_list);$i++){
	    if(strstr($uri,$media_list[$i]['domain']) != ""){
		     $media_name=$media_list[$i]['media_name'];
		     return $media_name;
		} 
	}
    return $media_name;
}

function time_str_to_int($time_str)
{
	$p = strpos($time_str,"分钟前");
	if ($p > 0)
	{
		$s = substr($time_str, 0, $p);
		$t = time() - $p * 60;
		return $t;
	}
	
	$p = strpos($time_str,"小时");
	if ($p > 0)
	{
		$s = substr($time_str, 0, $p);
		$t = time() - $p * 3600;
		return $t;
	}
	
	$p = strpos($time_str,"年");
	if ($p > 0)
	{
		$time_str = str_replace($time_str,"年","-");
		$time_str = str_replace($time_str,"月","-");
		$time_str = str_replace($time_str,"日","");
		return strtotime($time_str);
	}
	return 0;
}
function insert_key($_pdo, $log, $key_info) {
    $keyword = '';
    if(!empty($key_info['keyword'])) {
        $keyword = $key_info['keyword'];
        // 查找当前关键词id
        $query = "SELECT * FROM keyword WHERE keyword = '{$key_info['keyword']}'";
        $row = $_pdo->query($query)->fetch();
        $k_id = $row['k_id'];
    }
    else {
        $k_id = $key_info['k_id'];
    }

    // 查找当前关键词对应的user_keywords
    $query = "SELECT * FROM user_keywords WHERE k_id = '$k_id'";
    $log->INFO("insert_key中，，k_id: $k_id, keyword:{$key_info['keyword']} ");
    $uk_row = $_pdo->query($query);
    while($row = $uk_row->fetch()) {
        // 在信息索引表中查找是否已经记录
        $query = "SELECT id FROM {$key_info['table_name']} WHERE user_id = '{$row['user_id']}' and article_id = '{$key_info['article_id']}' and c_id = '{$row['c_id']}'";
        $nk_row = $_pdo->query($query)->fetch();

        // 如果没有记录
        if(empty($nk_row)) {
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

            $a_type = isset($key_info['a_type'])?$key_info['a_type']:0;
            $staff_statement->bindParam(':a_type', $a_type, PDO::PARAM_INT);

            $result = $staff_statement->execute();
            if($result === false) {
                $log->WARN("{$key_info['table_name']}_key 插入失败 article_id: {$key_info['article_id']}");
                return false;
            }
            $log->INFO("{$key_info['table_name']}_key 插入成功, id = ". $_pdo->lastInsertId());
        }else{
            $log->INFO("key表中已有记录，keyword:{$key_info['keyword']} ");
        }
    }
    return true;
}

function getMedia($uri, $media_list)
{
    $media_name = '';

    for($i=0; $i<count($media_list); $i++){
        if(strstr($uri, $media_list[$i]['domain']) != ''){
            $media_name=$media_list[$i]['media_name'];

            break;
        }
    }
    return $media_name;
}
function news_list($infos, $log, $keyword) {
    $insert_stack = array();
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);

    if($_pdo === false) {
        return false;
    }
    file_put_contents('news_list_data', var_export($infos, true));

    $infos_count = count($infos);

    //获取媒体列表
    $query="SELECT domain,media_name,grade  from media_list";
    $media_rows = $_pdo->query($query);
    $i = 0;
    $media_list=array();
    foreach($media_rows as $row) {
        $media_list[$i]['domain']=$row['domain'];
        $media_list[$i]['media_name']=$row['media_name'];
        $media_list[$i]['grade']=$row['grade'];
        $i++;
    }

    for($i = 0; $i < $infos_count; $i++) {
        $media = '';

        // 过滤结果
        $query = "SELECT f.filter_word FROM keyword_filter AS f LEFT JOIN keyword AS k USING(k_id) WHERE k.keyword = '$keyword' OR f.k_id = 0";
        $filter_rows = $_pdo->query($query);
        $filter_result = false;
        foreach($filter_rows as $row) {
            if(strpos($infos[$i]['article_title'], $row['filter_word']) === false and strpos($infos[$i]['article_content'], $row['filter_word']) === false) {
                continue;
            }
            else {
                $filter_result = true;
                break;
            }
        }
        if(mb_strlen($infos[$i]['media'], 'UTF-8') > 13 or strstr($infos[$i]['article_title'], '棋牌') or strstr($infos[$i]['article_content'], '包夜服务') or strstr($infos[$i]['article_title'], '娱乐') ){
            $log->WARN("过滤赌博, keyword:{$keyword}, {$infos[$i]['media']}：".mb_strlen($infos[$i]['media'], 'UTF-8'));
            continue;
        }
        // 过滤结果2，将内容和标题里都没有包含关键字的结果过滤掉
        if($keyword != '广汽丰田' and strpos($infos[$i]['article_title'], $keyword) === false and strpos($infos[$i]['article_content'], $keyword) === false and strpos($infos[$i]['article_summary'], $keyword) === false){
            $filter_result = true;
            $log->WARN("过滤结果2, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
        }
        //重新过滤一次
//        if(!empty($keyword) and strpos(get_array($infos[$i]['article_url'])['TITLE'], $keyword) === false and strpos(get_array($infos[$i]['article_url'])['content'], $keyword) === false){
//            $filter_result = true;
//            $log->WARN("过滤结果4(a.php), keyword:{$keyword}, url:{$infos[$i]['article_url']}");
//        }

        //过滤掉不属于新闻的论坛信息
        if(strpos($infos[$i]['article_title'], '论坛') !== false or strpos($infos[$i]['article_url'], 'bbs') !== false or strpos($infos[$i]['article_url'], 'tieba') !== false){
            $log->WARN("过滤结果3-论坛, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            continue;
        }
        //过滤掉金属新闻网
        if( strpos($infos[$i]['media'], '金属新闻网') !== false or strpos($infos[$i]['media'], '华股财经') !== false){
            $log->WARN("过滤结果3-金属新闻或华股财经, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            continue;
        }
        //过滤掉无效数据
        if( strpos($infos[$i]['media'], '印象庆阳网') !== false or strpos($infos[$i]['article_url'], 'cien.com.cn') !== false){
            $log->WARN("过滤结果3, keyword:{$keyword}, url:{$infos[$i]['article_url']}");
            continue;
        }
        if($filter_result === true) {
            continue;
        }
//        file_put_contents('news_list_url', var_export($infos[$i]['article_url'], true));
//        file_put_contents('news_list_value', var_export(get_array($infos[$i]['article_url']), true));
        // 进行查询
        $query = "SELECT article_id from news_article WHERE article_url = :article_url";
        $staff_statement = $_pdo->prepare($query);
        $staff_statement->bindParam(':article_url', $infos[$i]['article_url'], PDO::PARAM_STR);
        $staff_statement->execute();
        $result = $staff_statement->fetch();

        if(empty($result)) {
            $media = getMedia($infos[$i]['article_url'], $media_list);
//            file_put_contents('media_name', var_export(getMedias($infos[$i]['article_url'], $media_list)['name'], true));
//            file_put_contents('media_grade', var_export(getMedias($infos[$i]['article_url'], $media_list)['grade'], true));
            if(!empty($media)) {
                $infos[$i]['media'] = $media;
            }
//            $grades=getMedias($infos[$i]['article_url'], $media_list)['grade'];
//            file_put_contents('media', var_export($media, true));
//            file_put_contents('grade', var_export($grade, true));
//            if(empty($infos[$i]['media'])) {    // 如果媒体为空,进行匹配
//                continue;
//            }
            if(empty($infos[$i]['article_is_repost'])){
                $infos[$i]['article_is_repost']=1;
            }

            // 如果没有保存,进行保存
            $query = "INSERT INTO news_article (article_title, article_url, article_content, article_pubtime, "
                . "article_addtime, article_summary, article_comment, article_source, article_channel, media, article_author, article_is_repost) "
                . "VALUES (:article_title, :article_url, :article_content, :article_pubtime, "
                . ":article_addtime, :article_summary, :article_comment, :article_source, :article_channel, :media, :article_author, :article_is_repost)";
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

            // 执行预处理语句
            $result = $staff_statement->execute();
            if($result === false) {
                $log->WARN("news_article 插入失败, url:{$infos[$i]['article_title']}");
                return $result;
            }
            $article_id = $_pdo->lastInsertId();
        }
        else {
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
        if( strstr($infos[$i]['article_channel'], '经销商')  or strstr($infos[$i]['article_title'], '经销商') or strstr($infos[$i]['article_title'], '优惠') or
            ( strstr($infos[$i]['article_title'], '广汽丰田') and  ( strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')  )  ) or
            ( strstr($infos[$i]['article_title'], '广丰') and  ( strstr($infos[$i]['article_title'], '有限公司') or strstr($infos[$i]['article_title'], '店')  )  )
        ){
            $key_info['a_type'] = 1;
        }
        $key_result = insert_key($_pdo, $log, $key_info);
        if($key_result === false) {
            return false;
        }
    }
    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    return true;
}



function get_same_news_page($suburl)
{
	$url = "http://news.baidu.com".$suburl;
print_r($url."\n");
	$content = html_get($url);
//print_r($content);
	$content = str_replace("\n","", $content);

	$start_str = '<div class="result"';
	$pstart = strpos($content, $start_str);
	$i = 0;
	while ($pstart > 0 || $pstart === 0)
	{
		$content = substr($content, $pstart + strlen($start_str));
		$pstart = strpos($content, $start_str);
		if ($pstart > 0)
		{
			$div = substr($content, 0, $pstart);
		}
		else
		{
			$div = $content;
		}
		$url  = getXmlValue($div, 'href="','"');
		$infos[$i]["article_url"]= str_replace("&amp;", "&", $url);
		preg_match('/<h3 class=\"c-title\">(.*?)<\/h3>/',$div,$data);//截取标题
		$infos[$i]["article_title"]=strip_tags($data[1]);
		$pc = getXmlValue($div, '</p>', '<a href=');
		$infos[$i]["article_content"]=strip_tags($pc);
		
		$newsinfo = getXmlValue($div, '<p class="c-author"', 'p>');
		if ($newsinfo == "")
		{
			continue;
		}
		$reply_num = 0; 
		$infos[$i]["media"] = getXmlValue($newsinfo, '>', '&nbsp;');
		$time_str = getXmlValue($newsinfo, $infos[$i]["media"].'&nbsp;&nbsp;', '</');
		
		$infos[$i]["article_summary"] = $newsinfo;
		$infos[$i]["article_pubtime"] = $time_str;
		$infos[$i]["article_pubtime"] = time_str_to_int($infos[$i]["article_pubtime"]);
        if(empty($infos[$i]["article_pubtime"])){

            $time_str=explode('&nbsp;&nbsp;',$infos[$i]["article_summary"])[1];
            $ok_str=substr($time_str,0,-2);
            $ok_str=str_replace("年","-",$ok_str);
            $ok_str=str_replace("月","-",$ok_str);
            $ok_str=str_replace("日","",$ok_str);
            $infos[$i]["article_pubtime"]=strtotime($ok_str);
        }
		$i++;
	}
	return $infos;	
}

function get_infos($keyword)
{
	$url = "http://news.baidu.com/ns?word=".urlencode($keyword)."&sr=0&cl=2&rn=50&tn=news&ct=0&clk=sortbytime";
print_r($url."\n");
	$content = html_get($url);
	$content_org = $content;
//print_r($content);
	$content = str_replace("\n","", $content);

	$start_str = '<div class="result"';
	$pstart = strpos($content, $start_str);
	$i = 0;
	while ($pstart > 0 || $pstart === 0)
	{
		$content = substr($content, $pstart + strlen($start_str));
		$pstart = strpos($content, $start_str);
		if ($pstart > 0)
		{
			$div = substr($content, 0, $pstart);
		}
		else
		{
			$div = $content;
		}
		$url  = getXmlValue($div, 'href="','"');
		$infos[$i]["article_url"]= str_replace("&amp;", "&", $url);
		preg_match('/<h3 class=\"c-title\">(.*?)<\/h3>/',$div,$data);//截取标题
		$infos[$i]["article_title"]=strip_tags($data[1]);
		$pc = getXmlValue($div, '</p>', '<a href=');
		$infos[$i]["article_content"]=strip_tags($pc);
		
		$newsinfo = getXmlValue($div, '<p class="c-author"', 'p>');
		if ($newsinfo == "")
		{
			continue;
		}
		$reply_num = 0; 
		$infos[$i]["media"] = getXmlValue($newsinfo, '>', '&nbsp;');
		$time_str = getXmlValue($newsinfo, $infos[$i]["media"].'&nbsp;&nbsp;', '</');

		$infos[$i]["article_summary"] = $newsinfo;
		$infos[$i]["article_pubtime"] = $time_str;
		$infos[$i]["article_pubtime"] = time_str_to_int($infos[$i]["article_pubtime"]);
        if(empty($infos[$i]["article_pubtime"])){

            $time_str=explode('&nbsp;&nbsp;',$infos[$i]["article_summary"])[1];
            $ok_str=substr($time_str,0,-2);
            $ok_str=str_replace("年","-",$ok_str);
            $ok_str=str_replace("月","-",$ok_str);
            $ok_str=str_replace("日","",$ok_str);
            $infos[$i]["article_pubtime"]=strtotime($ok_str);
        }
		$i++;
	}
	
	preg_match_all('/href=\"([^\"]+)\"/iU',$content_org,$out);
	for ($k = 0; $k < count($out[1]); $k++)
	{
		if (!strstr($out[1][$k], "+cont:"))
		{
			continue;
		}
//echo($out[1][$k]);
		$infos_2 = get_same_news_page($out[1][$k]);
//		print_r($infos_2);
//		return;
		for ($m = 0; $m < count($infos_2); $m++)
		{
			$infos[$i] = $infos_2[$m];
			$i++;
		}
		sleep(rand(1,10));
	}

	return $infos;	

}
$interval_time = 60*60;
while(true) {

    $rows=array("凯美瑞","广汽丰田","汉兰达","雷凌","致炫","致享","IX4",'埃尔法',"凯美瑞","广汽丰田","汉兰达","雷凌","致炫","致享","C-HR","上汽大众","迈腾","途观","奕泽","卡罗拉","飞度","锋范","东风日产","雅阁","Q5","X-RV","科鲁兹","POLO","悦纳","一汽大众","天籁","昂科威","缤智","福克斯","骊威","威驰","广汽本田","帕萨特","锐界","探歌","明锐","嘉年华","瑞纳","一汽丰田","君威","新胜达","领克02","昂克塞拉","威驰FS","赛欧","迈锐宝","索纳塔");
//$rows=array("凯美瑞","广汽丰田","汉兰达","雷凌","致炫","致享","IX4",'埃尔法');
    foreach ($rows as $row){
        $infos = get_infos($row);
        news_list($infos,$log,$row);
        print_r("111");
        print_r($infos);

    }
    sleep($interval_time);
}
//$infos = get_infos("凯美瑞");
//news_list($infos,$log,"凯美瑞");
//print_r("111");
//print_r($infos);

//print_r($infos);


?>
