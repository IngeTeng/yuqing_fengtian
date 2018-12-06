<?php

/**
 * @filename task_center1.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-20  15:53:02
 * @updatetime 2016-8-20  15:53:02
 * @version 1.0
 * @Description
 * 任务中心,负责分配关键字和cookie
 * 
 */

require('./config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/log.php');
require(BASE_PATH. '/include/function.php');
require(BASE_PATH. '/include/observer.php');

ini_set('display_errors',1);            //错误信息  
ini_set('display_startup_errors',1);    //php启动错误信息  
error_reporting(-1);                    //打印出所有的 错误信息  
ini_set('error_log', dirname(__FILE__) . '/task_log.txt'); //将出错信息输出到一个文本文件 

$log = LOG::Init(LOG_HANDLER, LOG_LEVEL);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_array = $_POST;
    $post_str = json_encode($post_array);
}
else {
    exit('nothing');
}

// 初始化response 
$response = array(
    'status' => 200,
    'timestamp' => time(),
    'cookies' => array(),
    'keywords' => array(),
    'proxy' => array(),
);

// 创建观察者
$subject = new CInterfaceSubject();
$observer = new CInterfaceObserver();
// 将初始化的返回付给观察者
$observer->setResponse($response);
$subject->attach($observer);

// 连接数据库
$dsn = 'mysql:dbname='. DB_NAME. ';host='. DB_HOST;

try {
    $_pdo = new PDO($dsn, DB_USER_NAME, DB_PASSWORD);
    $_pdo->query('SET NAMES utf8');
    $log->INFO('新建数据库连接成功');
} catch (PDOException $ex) {
    // 500 为数据库错误
    $log->WARN('数据库连接失败 '. $ex->getMessage());
    $subject->setStatus(500);
}

$sign = $post_array['sign'];
unset($post_array['sign']);

if(strcmp($sign, Post2Sign::getSign($post_array, SECRET))) {
    // 403为sign错误
    $log->WARN("403 sign错误 $post_str");
    $subject->setStatus(403);
}

if(isset($post_array['useless_cookies'])) {     // 删除失效cookies
    if(count($post_array['useless_cookies']) > 1) {
        $log->WARN("{$post_array['spider_name']} 部分cookies失效, spider_name:{$post_array['spider_name']},  cookies id: ". implode(',', $post_array['useless_cookies']));
        if(strcmp($post_array['spider_name'], 'tencent_weibo_list') === 0) {
            $query = "UPDATE qq_cookies SET status = 0 WHERE qc_id = '";
        }
        elseif(strcmp($post_array['spider_name'], 'sogou_weixin_list') === 0) {
            $query = "UPDATE weixin_cookies SET status = 0 WHERE id = '";
        }
        foreach($post_array['useless_cookies'] as $cookie_id) {
        	if(strcmp($cookie_id, "empty") == 0) {
        		continue;
        	}
        	$the_query = $query. $cookie_id. "'";
        	
        	// 删除无效的cookies
	        $useless_result = $_pdo->exec($the_query);    
    		if($useless_result !== 0) {
    			$log->INFO("失效cookies删除成功, id=$cookie_id");
    		}	
    		else {
            	$log->WARN("受影响的行数为0 id=$cookie_id");
        	}      	
        }
    }
}
elseif(isset($post_array['useless_proxy'])) {       // 删除无效代理
    if(count($post_array['useless_proxy']) > 1) {
        array_shift($post_array['useless_proxy']);      // 删除empty
        $log->WARN("部分代理失效,, spider_name:{$post_array['spider_name']},  proxy id: ". implode(',', $post_array['useless_proxy']));
        $proxy_infos = json_decode(file_get_contents(PROXY_HANDLER), true);
        // 删除无效代理
        foreach($post_array['useless_proxy'] as $proxy_id) {
            unset($proxy_infos[$proxy_id]);
        }
        file_put_contents(PROXY_HANDLER, json_encode(array_values($proxy_infos)));        
    }
}
else {
    $spider_name = $post_array['spider_name'];       // 具体爬虫
    $collect_name = $post_array['collect_name'];
    if(isset($SPIDER_KEYWORD_NUM[$spider_name]) and isset($COLLECT_NUMBER[$collect_name])) {      // 如果存在具体配置
        // 分配关键字
        $keyword_query = 'SELECT keyword FROM keyword WHERE weight = 1 ORDER BY k_id asc';
        if($SPIDER_KEYWORD_NUM[$spider_name] !== 'ALL') {
            $begin = ($COLLECT_NUMBER[$collect_name]-1) * $SPIDER_KEYWORD_NUM[$spider_name] + 1;
            $keyword_query .= " LIMIT $begin, {$SPIDER_KEYWORD_NUM[$spider_name]}";
        }
        $keyword_statement = $_pdo->query($keyword_query);
        $keywords = $keyword_statement->fetchAll();
        
        $response['keywords'] = $keywords;

        // 腾讯微博需要登录, 要查询cookie
        if(strcmp($spider_name, 'tencent_weibo_list') === 0) {
            $cookie_query = "SELECT qc_id, qq_cookie FROM qq_cookies WHERE status = 1";
            $cookie_statement = $_pdo->query($cookie_query);

            while($cookie = $cookie_statement->fetch()) {
                $response['cookies'][] = array(
                    'id' => $cookie['qc_id'],
                    'cookie' => $cookie['qq_cookie']
                );
            }
        }
        elseif(strcmp($spider_name, 'sogou_weixin_list') === 0) {
            $cookie_query = "SELECT id, cookie FROM weixin_cookies WHERE status = 1";
            $cookie_statement = $_pdo->query($cookie_query);

            while($cookie = $cookie_statement->fetch()) {
                $response['cookies'][] = array(
                    'id' => $cookie['id'],
                    'cookie' => $cookie['cookie']
                );
            }
            //加代理
            if(is_file(PROXY_HANDLER) and filesize(PROXY_HANDLER) < 500) {
                unlink(PROXY_HANDLER)   ;
            }
            //clearstatcache();
            if(!is_file(PROXY_HANDLER)) {
                $proxy_infos = get_proxy();
                
                $proxy_str = json_encode($proxy_infos);
                file_put_contents(PROXY_HANDLER, $proxy_str);
            }
            $proxy_str = file_get_contents(PROXY_HANDLER);
            $response['proxy'] = json_decode($proxy_str, true);
        }
        // 新浪微博带代理
        elseif(strcmp($spider_name, 'sina_weibo_list') === 0) {
        	if(is_file(PROXY_HANDLER) and filesize(PROXY_HANDLER) < 500) {
        		unlink(PROXY_HANDLER)	;
        	}
        	//clearstatcache();
            if(!is_file(PROXY_HANDLER)) {
                $proxy_infos = get_proxy();
                
                $proxy_str = json_encode($proxy_infos);
                file_put_contents(PROXY_HANDLER, $proxy_str);
            }
            $proxy_str = file_get_contents(PROXY_HANDLER);
            $response['proxy'] = json_decode($proxy_str, true);
        }
         // autohome论坛搜索带代理
        elseif(strcmp($spider_name, 'autohome_bbs_list') === 0 or strcmp($spider_name, 'autohome2_bbs_list') === 0) {
            if(is_file(PROXY_HANDLER) and filesize(PROXY_HANDLER) < 500) {
                unlink(PROXY_HANDLER)   ;
            }
            //clearstatcache();
            if(!is_file(PROXY_HANDLER)) {
                $proxy_infos = get_proxy();
                
                $proxy_str = json_encode($proxy_infos);
                file_put_contents(PROXY_HANDLER, $proxy_str);
            }
            $proxy_str = file_get_contents(PROXY_HANDLER);
            $response['proxy'] = json_decode($proxy_str, true);
        }
        // 国搜搜索带代理
        elseif(strcmp($spider_name, 'guosou_news_list') === 0) {
            if(is_file(PROXY_HANDLER) and filesize(PROXY_HANDLER) < 500) {
                unlink(PROXY_HANDLER)   ;
            }
            //clearstatcache();
            if(!is_file(PROXY_HANDLER)) {
                $proxy_infos = get_proxy();
                
                $proxy_str = json_encode($proxy_infos);
                file_put_contents(PROXY_HANDLER, $proxy_str);
            }
            $proxy_str = file_get_contents(PROXY_HANDLER);
            $response['proxy'] = json_decode($proxy_str, true);
        }
        //360知道和360search获取代理
        elseif(strcmp($spider_name, '360_zhidao_list') === 0 or strcmp($spider_name, '360search_news_list') === 0 or strcmp($spider_name, '360_news_list') === 0) {
            if(is_file(PROXY_HANDLER) and filesize(PROXY_HANDLER) < 500) {
                unlink(PROXY_HANDLER)   ;
            }
            //clearstatcache();
            if(!is_file(PROXY_HANDLER)) {
                $proxy_infos = get_proxy();
                
                $proxy_str = json_encode($proxy_infos);
                file_put_contents(PROXY_HANDLER, $proxy_str);
            }
            $proxy_str = file_get_contents(PROXY_HANDLER);
            $response['proxy'] = json_decode($proxy_str, true);
        }
        // 头条appsearch代理
        elseif(strcmp($spider_name, 'toutiao_appsearch_list') === 0) {
            if(is_file(PROXY_HANDLER) and filesize(PROXY_HANDLER) < 500) {
                unlink(PROXY_HANDLER)   ;
            }
            //clearstatcache();
            if(!is_file(PROXY_HANDLER)) {
                $proxy_infos = get_proxy();
               
                $proxy_str = json_encode($proxy_infos);
                file_put_contents(PROXY_HANDLER, $proxy_str);
            }
            $proxy_str = file_get_contents(PROXY_HANDLER);
            $response['proxy'] = json_decode($proxy_str, true);
        }
        // 一点资讯appsearch代理
        elseif(strcmp($spider_name, 'yidianzx_appsearch_list') === 0) {
            if(is_file(PROXY_HANDLER) and filesize(PROXY_HANDLER) < 500) {
                unlink(PROXY_HANDLER)   ;
            }
            //clearstatcache();
            if(!is_file(PROXY_HANDLER)) {
                $proxy_infos = get_proxy();
               
                $proxy_str = json_encode($proxy_infos);
                file_put_contents(PROXY_HANDLER, $proxy_str);
            }
            $proxy_str = file_get_contents(PROXY_HANDLER);
            $response['proxy'] = json_decode($proxy_str, true);
        }
    }
    else {
        // 找不到具体的爬虫配置
        $log->WARN("404 config not found, spider=$spider_name, collect=$collect_name");
        $subject->setStatus(404);
    }

    $json_result = json_encode($response);
    $log->INFO("200 成功返回关键字 $json_result");

    echo $json_result;
}

$_pdo = null;