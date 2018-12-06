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

namespace SPIDER_CENTER\BBS_LIST;

// 配置类
class Configure {
    const COLLECT_KIND = 'bbs';       // 爬取内容种类
    const COLLECT_CONTENT_KIND = 'list';        // 爬取内容排版
    const SLEEP_TIME = 30;      // 爬取间隔
    
    // 抓取地址
    static public $url_hosts = array(
        'baidu'    => 'http://tieba.baidu.com/f/search/res?isnew=1&kw=&qw=%s&rn=30&un=&only_thread=0&sm=%d&sd=&ed=&pn=%d',                     // 百度贴吧
        'sogou'    => 'http://www.sogou.com/web?query=%s&interation=196648&ie=utf8&sourceid=inttime_day&tsn=1&num=50',                         // 搜狗bbs搜索
        'autohome' => 'http://sou.autohome.com.cn/%s?q=%s&clubSearchType=0&clubSearchTime=1&class=0&sort=New',                          // 汽车之家
        'autohome2'=> 'http://sou2.api.autohome.com.cn/wrap/v3/topic/search?_appid=app&_callback=jsonp_5_116&class=&ignores=content&modify=0&offset=0&pf=h5&q=%s&range=&s=1&size=100&sort=new&tm=app', //汽车之家论坛搜索2
        'pcauto'   => 'http://ks.pcauto.com.cn/auto_bbs.shtml?q=%s&searchTime=week',                                                          // 太平样汽车网
        'xcar'     => 'http://sou.xcar.com.cn/XcarSearch/infobbssearchresult/bbs/%s/none/%s/none/none/1',
        //'baidusearch' => 'https://www.baidu.com/s?ie=utf-8&f=8&rsv_bp=1&rsv_idx=1&tn=baidu&wd=intitle:%s&pn=%d&rn=%d&rsv_enter=1&gpc=stf%%3D%d%%2C%d%%7Cstftype%%3D1&tfflag=1',
        'baidusearch' => 'https://www.baidu.com/s?ie=utf-8&f=8&rsv_bp=1&rsv_idx=1&tn=baidu&wd=%s&pn=%d&rn=%d&rsv_pq=e17f93490000053e&rsv_t=3a792vXa56wkzcEEjfnHfqB42105d4N7M2RS3jgm4bGsQcpe7JPnwFpbLrA&rqlang=cn&rsv_enter=1&inputT=272&gpc=stf%%3D%d%%2C%d%%7Cstftype%%3D1&tfflag=1',
        '360search' => 'https://www.so.com/s?q=%s+club.autohome&pn=%d&src=srp_paging&fr=none', //360搜索
        '12365auto' => 'http://www.12365auto.com/search/Search.aspx?search=%s&page=%d&t=4',       // 凤凰汽车论坛

        //'http://search.xcar.com.cn/search.php?c=5&k=%s&s=dateline_desc&f=_all&p=10&d=0&i=0&fid=0&fctbox=0&brbox=0&special=0'  //爱卡汽车论坛
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
