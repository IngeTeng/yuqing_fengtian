<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-3  20:15:41
 * @updatetime 2016-8-3  20:15:41
 * @version 1.0
 * @Description
 * bbs配置文件
 * 
 */

namespace SPIDER_CENTER\BBS_ARTICLE;

// 配置类
class Configure {
    const COLLECT_KIND = 'bbs';       // 爬取内容种类
    const COLLECT_CONTENT_KIND = 'article';        // 爬取内容排版
    const SLEEP_TIME = 10;      // 爬取间隔
    
    // 抓取地址
    static public $url_hosts = array(
        'tianya' => 'http://search.tianya.cn/bbs?s=4&q=%s&pid=',        // 天涯
        'ifeng' => 'http://bbs.auto.ifeng.com',       // 凤凰汽车论坛
    );
    
    private function __construct() {}
    private function __clone() {}
    
    public static function get_ifeng_result_path($base_path) {
        if($base_path[strlen($base_path)-1] !== '/') {
            $base_path .= '/';
        }
        $ifeng_path = $base_path. self::COLLECT_KIND. '_'. self::COLLECT_CONTENT_KIND. '/ifeng_result';
        if(!is_dir($ifeng_path)) {
            mkdir($ifeng_path);
        }
        return $ifeng_path;
    }
    
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
