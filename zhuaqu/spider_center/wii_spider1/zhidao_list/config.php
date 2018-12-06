<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-4  17:12:47
 * @updatetime 2016-8-4  17:12:47
 * @version 1.0
 * @Description
 * 知道爬虫配置
 * 
 */

namespace SPIDER_CENTER\ZHIDAO_LIST;

// 配置类
class Configure {
    const COLLECT_KIND = 'zhidao';       // 爬取内容种类
    const COLLECT_CONTENT_KIND = 'list';        // 爬取内容排版
    const SLEEP_TIME = 10;      // 爬取间隔
    
    // 抓取地址
    static public $url_hosts = array(
        //'sogou' => 'http://wenwen.sogou.com/s/?w=%s&sti=4&ch=22',      // sogou
        'sogou' => 'http://www.sogou.com/sogou?query=%s&insite=wenwen.sogou.com&page=%d&ie=utf8',      // sogou
        '360' => 'http://wenda.so.com/c/?q=%s&pn=0&filt=10',        // 360问答
        'baidu' => 'http://zhidao.baidu.com/search?word=%s&lm=0&site=-1&sites=0&date=2&ie=gbk',      // 百度知道
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
