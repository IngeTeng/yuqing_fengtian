<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-3  20:04:12
 * @updatetime 2016-8-3  20:04:12
 * @version 1.0
 * @Description
 * 微信配置
 * 
 */

namespace SPIDER_CENTER\WEIXIN_LIST;

// 配置类
class Configure {
    const COLLECT_KIND = 'weixin';       // 爬取内容种类
    const COLLECT_CONTENT_KIND = 'list';        // 爬取内容排版
    const SLEEP_TIME = 90;      // 爬取间隔
    const PAGE_NUM = 10;     // 翻页次数
    
    // 抓取地址
    static public $url_hosts = array(
        'sogou' => 'http://weixin.sogou.com/weixin?type=2&s_from=input&query=%s&ie=utf8&_sug_=n&_sug_type_=&page=%d',
        //'sogou' => 'http://weixin.sogou.com/weixin?type=2&query=%s&ie=utf8&s_from=input&_sug_=n&_sug_type_=1&w=01015002&oq=&ri=13&sourceid=sugg&sut=0&page=%d&p=40040108',

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

