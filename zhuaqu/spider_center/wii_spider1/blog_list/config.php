<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-27  10:25:08
 * @updatetime 2016-8-27  10:25:08
 * @version 1.0
 * @Description
 * blog配置信息
 * 
 */

namespace SPIDER_CENTER\BLOG_LIST;

// 配置类
class Configure {
    const COLLECT_KIND = 'blog';       // 爬取内容种类
    const COLLECT_CONTENT_KIND = 'list';        // 爬取内容排版
    const SLEEP_TIME = 2;      // 爬取间隔
    
    // 抓取地址
    static public $url_hosts = array(
        'sina' => 'http://search.sina.com.cn/?by=all&q=%s&c=blog&range=article&sort=time',       // 新浪博客
        'autohome' => 'http://sou.autohome.com.cn/youchuang?q=%s&class=0&sort=New&pvareaid=100834&entry=116&error=0',//汽车之家说客
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
