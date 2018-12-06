<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-11-10  20:23:55
 * @updatetime 2016-11-10  20:23:55
 * @version 1.0
 * @Description
 * 
 */


namespace SPIDER_CENTER\APPSEARCH_LIST;

// 配置类
class Configure
{
    const COLLECT_KIND = 'appsearch';       // 爬取内容种类
    const COLLECT_CONTENT_KIND = 'list';        // 爬取内容排版
    const SLEEP_TIME = 2;      // 爬取间隔
    
    // 抓取地址
    static public $url_hosts = array(
        'zaker' => 'http://www.myzaker.com/news/search_new.php?f=myzaker_com&keyword=%s',
        'autohome' => 'https://sou.m.autohome.com.cn/Api/TopicSearchJsPage/search?pageIndex=%d&q=%s&class=0&sort=New&pvareaid=&entry=59&timeType=1&callback=',
        'toutiao' => 'http://www.toutiao.com/search_content/?offset=0&format=json&keyword=%s&autoload=true&count=40',
        'yidianzx' => 'http://www.yidianzixun.com/home/q/news_list_for_keyword?display=%s&cend=50&word_type=token',
        'xcar' => 'http://a.xcar.com.cn/nxcar/index.php/search/search/search_forum/?word=%s&page=%d',
        'yiche' => 'http://searchapi.ycapp.yiche.com/search/newssearch?keyword=%s',  //易车网APP
        'sohu'  => "http://api.k.sohu.com/api/search/v5/search.go?rt=json&pageNo=1&words=%s&p1=NjIwNjA2Mzk0Mjg2ODMxNjIzOA&pageSize=20&type=0&pid=&token=&gid=02ffff110611110d12e2a4664b561d8a497c482ce94232&apiVersion=37&sid=10&u=1&bid=&keyfrom=input&autoCorrection=&refertype=7&versionName=5.8.0&os=android&h=&_=1481962505963", //搜狐新闻app
        'sohuwap' => 'http://s.auto.sohu.com/search/wap/info.at?suggest=%s',
        'dftoutiao' => 'http://sou2.api.autohome.com.cn/wrap/v3/article/search?_appid=app&_callback=jsonp_2_540&ignores=content&modify=0&offset=0&pf=h5&q=%s&s=1&size=30&tm=app',
        'sina' => 'http://sinanews.sina.cn/interface/search.d.html?callback=initFeed&refresh=1&apiEnv=online&keyword=%s&page=1&size=180&newpage=0&chwm=3023_0001&imei=26c312d18f21d5e86a39d1cf798431734fc287d6&did=26c312d18f21d5e86a39d1cf798431734fc287d6&from=6066993012',//新浪app搜索
    );
    
    private function __construct() {}
    private function __clone() {}
    
    public static function get_spider_result_path($base_path)
    {     
        if($base_path[strlen($base_path)-1] !== '/') {
            $base_path .= '/';
        }
        $spider_result = $base_path. self::COLLECT_KIND. '_'. self::COLLECT_CONTENT_KIND. '/spider_result';
        if(!is_dir($spider_result)) {
            mkdir($spider_result);
        }
        return $spider_result;
    }
    
    public static function getLogHandle($base_path, $spider_site)
    {
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
