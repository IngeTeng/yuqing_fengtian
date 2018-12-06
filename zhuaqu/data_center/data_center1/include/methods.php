<?php

/**
 * @filename methods.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-6-19  17:35:21
 * @updatetime 2016-11-27  19:35:21 yjl
 * @version 1.0
 * @Description
 * 处理不同的数据类型的函数库
 * 
 */


// 解析json文件,处理新闻列表内容
function news_list($result_array, $REPLACE_STRS) {
    $receive_time = 60*60*24*30;//接收的数据日期范围，此时为30天
    $requests = array(
        'collect_kind' => $result_array['collect_kind'],
        'collect_content_kind' => $result_array['collect_content_kind'],
        'result' => array(),
    );
    $request = array();
    $results = $result_array['result']; 

    foreach($results as &$result) {
    	if((time() - $result['time']) > $receive_time) {
    		continue;
    	}
    	
    	$nums = 0;
    	if($result_array['keyword'] == '广汽丰田') {
    		$key_word = array('凯美瑞','雅力士','逸致','致炫','汉兰达','威飒','杰路驰','埃尔法','FJ酷路泽','雷凌', '飞度','卡罗拉','威驰','科鲁兹','福克斯','明锐','昂克赛拉','POLO','骊威','嘉年华','瑞纳','CHR','C-HR');
			
			foreach ($key_word as $word) {
    			if(strpos($result['title'], $word) !== false) {
        			$nums++;
    			}
			}	
    	}
  //   	if($nums == 1) {
		// 	continue;
		// }
    	if(is_array($result['url'])){
            $result['url'] = $result['url'][1];
        }
        $request['article_url'] = $result['url'];
        $request['article_title'] = str_replace($REPLACE_STRS, '', $result['title']);
        if(!empty($result['abstract']))
            $request['article_content'] = str_replace($REPLACE_STRS, '', $result['abstract']);
        else
            $request['article_content'] = str_replace($REPLACE_STRS, '', $result['title']);
        $request['article_pubtime'] = $result['time'];
        $request['article_summary'] = $request['article_content'];
        $request['article_comment'] = 0;
        $request['article_source'] = '';
        $request['article_author'] = $result['author'];
      //  $request['article_is_repost'] = $result['source'];
        //$request['media'] = $result['from'];
        $request['media'] = $result['from']?$result['from']:'';
        $request['article_channel'] = $result['channel'];
        file_put_contents('new_result_log', var_export($request,true));
        // 进行语意分析, 语意中心只支持gbk编码
        $title = iconv("utf-8","GB18030", $request['article_title']);
        $content = iconv("utf-8", "GB18030", $request['article_content']);
        $article_property = article_property(PROPERTY_HOST, PROPERTY_PORT, $title, $content);
        $request['article_property'] = $article_property;
        
        $requests['result'][] = $request;
    }
    unset($result);
//    var_dump($results);
    return $requests;
}

// 微博列表
function weibo_list($result_array, $REPLACE_STRS) {
    $receive_time = 60*60*24*30;//接收的数据日期范围，此时为30天
    $requests = array(
        'collect_kind' => $result_array['collect_kind'],
        'collect_content_kind' => $result_array['collect_content_kind'],
        'result' => array(),
    );
    
    $request = array();
    $results = $result_array['result'];
    foreach($results as &$result) {
    	if((time() - $result['time']) > $receive_time) {
    		continue;
    	}
    
        $request['article_url'] = $result['url'];
        
        $request['article_title'] = str_replace($REPLACE_STRS, '', $result["content"]);
        $request['article_pubtime'] = $result['time'];
        $request['article_comment'] = 0;
        $request['article_repost'] = 0;
        $request['author'] = $result['uname'];
        $request['isV'] = empty($result['isv'])?0:1;
        $request['rz_info'] = $result['isv'];
        $request['fans'] = 0;
        $request['article_author'] = $result['author'];
        //$request['article_is_repost'] = $result['source'];
        $request['media'] = $result['media']?$result['media']:'';
        $request['mid'] = $result['mid']?$result['mid']:'';
        // 进行语意分析
        $title = iconv('utf-8', 'GB18030', $request['article_title']);
        $article_property = article_property(PROPERTY_HOST, PROPERTY_PORT, $title);
        $request['article_property'] = $article_property;
        file_put_contents('weibo_result_log', var_export($request,true));
        $requests['result'][] = $request;
    }
    unset($result);
    return $requests;
}

// 微信文章列表
function weixin_list($result_array, $REPLACE_STRS) {
    $receive_time = 60*60*24*30;//接收的数据日期范围，此时为30天
    $requests = array(
        'collect_kind' => $result_array['collect_kind'],
        'collect_content_kind' => $result_array['collect_content_kind'],
        'result' => array(),
    );
    
    $request = array();
    $results = $result_array['result'];
    foreach($results as &$result) {
    	if((time() - $result['time']) > $receive_time) {
    		continue;
    	}
    	
        $request['article_url'] = $result['url'];
        
        $request['article_title'] = str_replace($REPLACE_STRS, '', $result['title']);
        $request['article_pubtime'] = $result['time'];
        $request['article_summary'] = str_replace($REPLACE_STRS, '', $result["content"]);
        $request['author'] = $result['author']?$result['author']:'';
        $request['media'] = $result['author']?$result['author']:'';
        $request['read_num'] = $result['read_num'];
        $request['like_num'] = $result['like_num'];
        $request['article_author'] = $result['author'];
       // $request['article_is_repost'] = $result['article_is_repost'];

        // 进行语意分析
        $title = iconv('utf-8', 'GB18030', $request['article_title']);
        $content = iconv('utf-8', 'GB18030', $request['article_summary']);
        $article_property = article_property(PROPERTY_HOST, PROPERTY_PORT, $title, $content);
        $request['article_property'] = $article_property;
        file_put_contents('weiixn_result_log', var_export($request,true));
        $requests['result'][] = $request;
    }
    unset($result);
    return $requests;
}

// 视频列表
function video_list($result_array, $REPLACE_STRS) {
    $receive_time = 60*60*24*30;//接收的数据日期范围，此时为30天
    $requests = array(
        'collect_kind' => $result_array['collect_kind'],
        'collect_content_kind' => $result_array['collect_content_kind'],
        'result' => array(),
    );
    
    $request = array();
    $results = $result_array['result'];
    //file_put_contents('video-log', date('Y-m-d H:i:s ', time()). ' start!!'."\n\r", FILE_APPEND);
    foreach($results as $result) {
        if((time() - $result['time']) > $receive_time) {
    		continue;
    	}
        //file_put_contents('video-log', date('Y-m-d H:i:s ', time()). ' ----------------'."\n\r", FILE_APPEND);
        //file_put_contents('video-log', var_export($result, true), FILE_APPEND);
        $request['article_url'] = $result['url'];
        $request['article_title'] = str_replace($REPLACE_STRS, '', $result['title']);
        $request['article_pubtime'] = $result['time'];
        $request['article_summary'] = $request['article_title'];
        $request['media'] = $result['media'];
        $request['article_author'] = $result['author'][1];
        //$request['article_is_repost'] = $result['source'];
        //$title = $request['article_title'];
        $title = iconv('utf-8', 'GB18030', $request['article_title']);
        if(strcmp($result['media'], '网易视频') === 0) {
        	// 网易视频查找命中关键字
            
        	$k_ids = article_keyword(KEYWORD_HOST, KEYWORD_PORT, $title, $title);
        	if(count($k_ids) == 0) {
            	continue;
        	}
        	$request['k_ids'] = $k_ids;
    
        }   
        
        // 进行语意分析
        //file_put_contents('video-log', date('Y-m-d H:i:s ', time()).' before fenxi'."\n\r", FILE_APPEND);
        $article_property = article_property(PROPERTY_HOST, PROPERTY_PORT, $title, $request['article_title']);

        $request['article_property'] = $article_property;
        file_put_contents('video_result_log', var_export($request,true));
        $requests['result'][] = $request;
        
    }
    
    return $requests;
}

// 论坛帖子
function bbs_article($result_array, $REPLACE_STRS) {
    $requests = array(
        'collect_kind' => $result_array['collect_kind'],
        'collect_content_kind' => $result_array['collect_content_kind'],
        'result' => array(),
    );
    
    $request = array();
    $results = $result_array['result'];
    
    $request['article_url'] = $results['url'];
    $request['article_title'] = str_replace($REPLACE_STRS, '', $results['title']);
    $request['article_pubtime'] = $results['time'];
    $request['article_reply'] = $results['reply'];
    $request['article_click'] = $results['click'];
    $request['author'] = $results['author'];
    $request['forum'] = $results['forum'];
    $request['media'] = $results['media'];
    $request['article_author'] = $results['author'];
   // $request['article_is_repost'] = $results['source'];
    // 如果是凤凰论坛的帖子
    if(strcmp($result_array['collect_from_site'], 'ifeng') === 0) {
        if(isset($results['content'])) {        // 如果存在解析好的内容
            $request['article_content'] = str_replace($REPLACE_STRS, '', $results['content']);
            $request['article_summary'] = str_replace($REPLACE_STRS, '', $results['summary']);
        }
        else {      // 如果被反爬虫了, 重新解析内容
            $content_request = array(
                "collect_name" => '',
                "collect_ip" => CONTENT_CENTER,
                "collect_kind" => 'bbs',
                "collect_from_site" => 'ifeng',
                "collect_content_kind" => 'content',
                "collect_download_url" => "",
                "result" => array(),
                "collect_url" => "",
                "keyword" => "",
            );

            $content_request['collect_url'] = $results['url'];
            $content_request['collect_download_url'] = $results['url'];
            $content_request['result'] = $results;
            unset($content_request['sign']);
            $content_request['sign'] = Post2Sign::getSign($content_request, SECRET);
            $content_result = send_post(CONTENT_CENTER, $content_request);
            $content_array = json_decode($content_result, true);
            if($content_array['status'] != REQUEST_OK or empty($content_array['result']['content'])) {
                return '';
            }

            $request['article_content'] = str_replace($REPLACE_STRS, '', $content_array['result']['content']);
            $request['article_summary'] = str_replace($REPLACE_STRS, '', $content_array['result']['summary']);
        }
        // 查找命中关键字
        $title = iconv('utf-8', 'GB18030', $request['article_title']);
        $summary = iconv('utf-8', 'GB18030', $request['article_content']);
        $k_ids = article_keyword(KEYWORD_HOST, KEYWORD_PORT, $title, $summary);
        if(count($k_ids) < 1) {
            return '';
        }
        $request['k_ids'] = $k_ids;
    }
    else {
        $request['article_content'] = str_replace($REPLACE_STRS, '', $results['content']);
        $request['article_summary'] = str_replace($REPLACE_STRS, '', $results['summary']);
    }
    
    // 进行语意分析
    $title = iconv('utf-8', 'GB18030', $request['article_title']);
    $summary = iconv('utf-8', 'GB18030', $request['article_content']);
    $article_property = article_property(PROPERTY_HOST, PROPERTY_PORT, $title, $summary);
    $request['article_property'] = $article_property;
    file_put_contents('bbs_result_log', var_export($request,true));
    $requests['result'] = $request;

    return $requests;
}

// bbslist
function bbs_list($result_array, $REPLACE_STRS) {
    $receive_time = 60*60*24*30;//接收的数据日期范围，此时为30天
    $requests = array(
        'collect_kind' => $result_array['collect_kind'],
        'collect_content_kind' => $result_array['collect_content_kind'],
        'result' => array(),
    );
    
    $request = array();
    $results = $result_array['result'];
    
    foreach($results as $result) {
        //将过期的过滤掉
    	if((time() - $result['time']) > $receive_time ) {
    		continue;
    	}
        if(empty($result['content'])){
            $result['content'] = $result['title'];
        }
        if (empty($result['summary'])) {
            $result['summary'] = $result['title'];
        }
        if(is_array($result['url'])){
            $result['url'] = $result['url'][1];
        }
    
        $request['article_url'] = $result['url'];
        $request['article_title'] = $result['title'];
        $request['article_content'] = str_replace($REPLACE_STRS, '', $result['content']);
        $request['article_summary'] = str_replace($REPLACE_STRS, '', $result['summary']);       // 去除商标等特殊字符
        $request['article_pubtime'] = $result['time'];
        $request['article_reply'] = !empty($result['reply'])?$result['reply']:0;
        $request['article_click'] = !empty($result['click'])?$result['click']:0;
        $request['author'] = !empty($result['author'])?$result['author']:'';
        $request['forum'] = !empty($result['forum'])?$result['forum']:'';
        $request['media'] = !empty($result['media'])?$result['media']:'';
        $request['article_author'] = $result['author'];
       // $request['article_is_repost'] = $result['source'];
        // 进行语意分析
        $title = iconv('utf-8', 'GB18030', $request['article_title']);
        $content = iconv('utf-8', 'GB18030', $request['article_content']);
        $article_property = article_property(PROPERTY_HOST, PROPERTY_PORT, $title, $content);
        $request['article_property'] = $article_property;
        file_put_contents('bbs_list_result_log', var_export($request,true));
        $requests['result'][] = $request;
    }
    return $requests;
}

// 提问
function zhidao_list($result_array, $REPLACE_STRS) {
    $receive_time = 60*60*24*30;//接收的数据日期范围，此时为30天
    $requests = array(
        'collect_kind' => $result_array['collect_kind'],
        'collect_content_kind' => $result_array['collect_content_kind'],
        'result' => array(),
    );
    
    $request = array();
    $results = $result_array['result'];
    
    foreach($results as $result) {
    	if((time() - $result['time']) > $receive_time) {
    		continue;
    	}
    	
        $request['article_url'] = $result['url'];
        $request['article_title'] = str_replace($REPLACE_STRS, '', $result['title']);
        $request['article_summary'] = str_replace($REPLACE_STRS, '', $result['summary']);       // 去除商标等特殊字符
        $request['article_pubtime'] = $result['time'];
        $request['author'] = $result['author']?$result['author']:'';
        $request['media'] = $result['media']?$result['media']:'';
        $request['article_author'] = $result['author'];
       // $request['article_is_repost'] = $result['source'];
        // 进行语意分析
        $title = iconv('utf-8', 'GB18030', $request['article_title']);
        $summary = iconv('utf-8', 'GB18030', $request['article_summary']);
        $article_property = article_property(PROPERTY_HOST, PROPERTY_PORT, $title, $summary);
        $request['article_property'] = $article_property;
        file_put_contents('zhidao_list_result_log', var_export($request,true));
        $requests['result'][] = $request;
    }
    return $requests;    
}

// api
function app_list($result_array, $REPLACE_STRS) {

    file_put_contents('app_result_array', var_export($result_array,true));
    $requests = array(
        'collect_kind' => $result_array['collect_kind'],
        'collect_content_kind' => $result_array['collect_content_kind'],
        'result' => array(),
    );
    $request = array();
    $results = $result_array['result'];
    file_put_contents('$results', var_export($results,true));
    // 查找是否已经入库
    $select_request = array(
        'collect_kind' => 'app',
        'collect_content_kind' => 'fliter',
        'result' => array(),
        'sql' => 'select',
        'keyword' => '',
    );
    
    // content_center
    $content_request = array(
        "timestamp" => '',
        "collect_name" => '',
        "collect_ip" => CONTENT_CENTER,
        "collect_kind" => 'app',
        "collect_from_site" => $result_array['collect_from_site'],
        "collect_content_kind" => 'content',
        "collect_download_url" => "",
        "result" => array(),
        "collect_url" => "",
        "keyword" => "",
    );

    foreach($results as $result) {
        
        // // 像db_center请求查找数据是否在数据库中存在
        // $select_request['result']['id'] = strval($result['id']);
        // $select_request['result']['media'] = $result['media'];
        // unset($select_request['sign']);
        // $select_request['sign'] = Post2Sign::getSign($select_request, SECRET);
        // $select_result = send_post(DATABASE_CENTER, $select_request);
        // $select_array = json_decode($select_result, true);
        // if($select_array['status'] != REQUEST_OK) {
        //     continue;
        // }
        
        if(!isset($result['content'])) {
            // 如果不在数据库中,请求content_center进行解析
            $content_request['collect_url'] = $result['url'];
            $content_request['collect_download_url'] = $result['url'];
            $content_request['result'] = $result;
            unset($content_request['sign']);
            $content_request['sign'] = Post2Sign::getSign($content_request, SECRET);
            $content_result = send_post(CONTENT_CENTER, $content_request);
            $content_array = json_decode($content_result, true);
            if($content_array['status'] != REQUEST_OK or empty($content_array['result']['content'])) {
                continue;
            }

            $request['article_content'] = str_replace($REPLACE_STRS, '', $content_array['result']['content']);
        }
        else {
            $request['article_content'] = str_replace($REPLACE_STRS, '', $result['content']);
        }
        
        // 查找命中关键字
        $request['ids'] = strval($result['id']);
        $request['article_url'] = empty($content_array['result']['url']) ? $result['url']:$content_array['result']['url'];
        $request['article_title'] = str_replace($REPLACE_STRS, '', $result['title']);
        $request['article_pubtime'] = isset($content_array['result']['time']) ? $content_array['result']['time']:$result['time'];
        $request['media'] = $result['media'];
        $request['article_author'] = $result['author'];
        //$request['article_is_repost'] = $result['source'];
        $request['article_channel'] = isset($result['channel']) ? $result['channel']:'';
        
        $title = iconv('utf-8', 'GB18030', $request['article_title']);
        $summary = iconv('utf-8', 'GB18030', $request['article_content']);
        file_put_contents('app_lsit', var_export($request, true));

        $k_ids = article_keyword(KEYWORD_HOST, KEYWORD_PORT, $title, $summary);
        if($result['media'] == 'Zaker'){
        	file_put_contents('app-', $result['media'].'--'.$k_ids."\n\r".$title."\n\r".$summary."\n\r");
        }
        if(count($k_ids) < 1) {
            continue;
        }
            
        // 检测结果中是否存在summary
        if(isset($result['summary'])) {
            $request['article_summary'] = str_replace($REPLACE_STRS, '', $result['summary']);
        }
        else {
            if(mb_strlen($request['article_content'], "utf8") >= 1000) {
                 $request['article_summary'] = mb_substr($request['article_content'], 0, 1000, "utf8");
            }
            else{
                 $request['article_summary'] = $request['article_content'];
            }
        }
        $request['k_ids'] = $k_ids;
        
        // 进行语意分析
        $article_property = article_property(PROPERTY_HOST, PROPERTY_PORT, $title, $summary);
        $request['article_property'] = $article_property;
        $requests['result'][] = $request;
        
    }
    
    return $requests;        
}

function blog_list($result_array, $REPLACE_STRS) {
    $receive_time = 60*60*24*30;//接收的数据日期范围，此时为30天
    $requests = array(
        'collect_kind' => $result_array['collect_kind'],
        'collect_content_kind' => $result_array['collect_content_kind'],
        'result' => array(),
    );
    $request = array();
    $results = $result_array['result']; 
    
    foreach($results as &$result) {
    	if((time() - $result['time']) > $receive_time) {
    		continue;
    	}
    
        $request['article_url'] = $result['url'];
        $request['article_title'] = str_replace($REPLACE_STRS, '', $result['title']);
        $request['article_content'] = str_replace($REPLACE_STRS, '', $result['content']);
        $request['article_pubtime'] = $result['time'];
        $request['article_summary'] = str_replace($REPLACE_STRS, '', $result['summary']);
        $request['author'] = $result['author']?$result['author']:'';
        $request['media'] = $result['media']?$result['media']:'';
        $request['article_author'] = $result['author'];
       // $request['article_is_repost'] = $result['source'];
        // 进行语意分析, 语意中心只支持gbk编码
        $title = iconv("utf-8","GB18030", $request['article_title']);
        $content = iconv("utf-8", "GB18030", $request['article_content']);
        $article_property = article_property(PROPERTY_HOST, PROPERTY_PORT, $title, $content);
        $request['article_property'] = $article_property;
        file_put_contents('blob_list_result_log', var_export($request,true));
        $requests['result'][] = $request;
    }
    unset($result);
    return $requests;    
}

// app搜索
function appsearch_list($result_array, $REPLACE_STRS) {
    $receive_time = 60*60*24*30;//接收的数据日期范围，此时为30天
    $requests = array(
        'collect_kind' => $result_array['collect_kind'],
        'collect_content_kind' => $result_array['collect_content_kind'],
        'result' => array(),
    );
    $request = array();
    $results = $result_array['result'];
    
    // content_center
    $content_request = array(
        "timestamp" => '',
        "collect_name" => '',
        "collect_ip" => CONTENT_CENTER,
        "collect_kind" => 'appsearch',
        "collect_from_site" => $result_array['collect_from_site'],
        "collect_content_kind" => 'content',
        "collect_download_url" => "",
        "result" => array(),
        "collect_url" => "",
        "keyword" => "",
    );
        
    foreach($results as &$result) {
    	if((time() - $result['time']) > $receive_time) {
            continue;
    	}
    	
        $request['article_url'] = $result['url'];
        $request['article_title'] = str_replace($REPLACE_STRS, '', $result['title']);
        $request['article_pubtime'] = $result['time'];
        $request['article_author'] = $result['author'];
        //$request['article_is_repost'] = $result['source'];
        if(!empty($result['from'])){
            $request['media'] = $result['from'];
        }else{
            $request['media'] = $result['media'];
        }
        
        if(!empty($result['channel']))
            $request['article_channel'] = $result['channel'];
        else
            $request['article_channel'] = '';
        
        if(!isset($result['content'])) {
            $content_request['collect_url'] = $result['url'];
            $content_request['collect_download_url'] = $result['url'];
            $content_request['result'] = $result;
            unset($content_request['sign']);
            $content_request['sign'] = Post2Sign::getSign($content_request, SECRET);
            $content_result = send_post(CONTENT_CENTER, $content_request);
            $content_array = json_decode($content_result, true);
            if($content_array['status'] != REQUEST_OK or empty($content_array['result']['content'])) {
                continue;
            }

            $request['article_content'] = str_replace($REPLACE_STRS, '', $content_array['result']['content']);
        }
        else {
            $request['article_content'] = str_replace($REPLACE_STRS, '', $result['content']);
        }
        
        // 进行语意分析, 语意中心只支持gbk编码
        //$title = iconv("utf-8","gbk", $request['article_title']);
        //$content = iconv("utf-8", "gbk", $request['article_content']);
        //$article_property = article_property(PROPERTY_HOST, PROPERTY_PORT, $title, $content);
        //$request['article_property'] = $article_property;
        $request['article_property'] = 1;
        $request['article_author'] = $result['author'];
       // $request['article_is_repost'] = $result['source'];
        file_put_contents('appsearch_result_log', var_export($request,true));
        $requests['result'][] = $request;
    }
    unset($result);
//    var_dump($results);
    return $requests;
}
