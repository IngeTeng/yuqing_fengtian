<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-3  20:11:49
 * @updatetime 2016-8-3  20:11:49
 * @version 1.0
 * @Description
 * 视频列表抓取配置
 */

namespace SPIDER_CENTER\VIDEO_LIST;

// 配置类
class Configure {
    const COLLECT_KIND = 'video';       // 爬取内容种类
    const COLLECT_CONTENT_KIND = 'list';        // 爬取内容排版
    const SLEEP_TIME = 10;      // 爬取间隔
    
    // 抓取地址
    static public $url_hosts = array(
        'tudou' => 'http://www.soku.com/nt/search/q_%s_orderby_2_limitdate_7?site=14&_lg=10&page=%d',     // 土豆视频搜索
        //'youku' => 'http://www.soku.com/search_video/q_%s_orderby_2',       // 优酷视频搜索
        'sohu' => 'http://so.tv.sohu.com/mts?wd=%s&c=0&v=0&length=0&limit=0&o=3',       // 搜狐视频搜索
        '163' => array(
            'http://auto.163.com/special/v_auto_zx/',
            'http://auto.163.com/special/v_auto_xc/',
            'http://auto.163.com/special/v_auto_pc/',
            'http://auto.163.com/special/v_auto_ycwc/',
            //'http://auto.163.com/special/v_auto_xcmn/'
            ),       // 163视频列表
        'ku6' => 'http://so.ku6.com/search?q=%s&sort=uploadtime',      // ku6
        'pcauto' => 'http://ks.pcauto.com.cn/auto_video.shtml?q=%s&searchTime=week',       // 太平样汽车网
        '56lesou' => 'http://so.56.com/mts?wd=%s&c=0&v=0&length=0&limit=0&site=0&o=3&p=1&st=&suged=&filter=0', //56乐搜
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