<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-3  19:24:35
 * @updatetime 2016-8-3  19:24:35
 * @version 1.0
 * @Description
 * 微博配置文件
 * 
 */

namespace SPIDER_CENTER\WEIBO_LIST;

// 配置类
class Configure {
    const COLLECT_KIND = 'weibo';       // 爬取内容种类
    const COLLECT_CONTENT_KIND = 'list';        // 爬取内容排版
    const SLEEP_TIME = 15;      // 爬取间隔
    
    // 抓取地址
    static public $url_hosts = array(
        'sina' => 'http://s.weibo.com/weibo/%s&nodup=1&xsort=time',
        'sina2' => 'http://sinanews.sina.cn/interface/type_of_search.d.html?callback=initFeed&refresh=1&apiEnv=online&keyword=%s&page=1&type=siftWb&size=180&newpage=0&chwm=3023_0001&imei=26c312d18f21d5e86a39d1cf798431734fc287d6&did=26c312d18f21d5e86a39d1cf798431734fc287d6&from=6066993012',
        'tencent' => 'http://search.t.qq.com/index.php?k=%s&pos=174&s_dup=1&s_source=&s_m_type=1',
    );
    
    private function __construct() {}
    private function __clone() {}
    
    public static function get_spider_result_path($base_path) {     
        if($base_path[strlen($base_path)-1] !== '/') {
            $base_path .= '/';
        }
        $spider_result = $base_path. self::COLLECT_KIND. '_'. self::COLLECT_CONTENT_KIND. '/spider_result';
        if(!is_dir($spider_result)) {
            mkdir($spider_result);
        }
        return $spider_result;
    }
    
    public static function getLogHandle($base_path, $spider_site) {
        if($base_path[strlen($base_path)-1] !== '/') {
            $base_path .= '/';
        }
        $log_path = $base_path. self::COLLECT_KIND. '_'. self::COLLECT_CONTENT_KIND. '/log';
        if(!is_dir($log_path)) {
            mkdir($log_path);
        }
        return $log_path. '/'. date('Y_m_d'). '_'. $spider_site;
    }
}

