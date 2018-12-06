<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-3  16:59:38
 * @updatetime 2016-8-3  16:59:38
 * @version 1.0
 * @Description
 * 新闻搜索列表
 * 
 */

namespace SPIDER_CENTER\NEWS_LIST;

// 配置类
class Configure {
    const COLLECT_KIND = 'news';       // 爬取内容种类
    const COLLECT_CONTENT_KIND = 'list';        // 爬取内容排版
    const SLEEP_TIME = 30;      // 爬取间隔
    const PAGE_NUM = 8;     // 翻页次数
    const ONE_PAGE_NUM = 20;        // 每页显示数量,百度无法大于50
    
    // 抓取地址
    static public $url_hosts = array(
        '360' => 'http://news.so.com/ns?rank=pdate&q=%s&pn=%d',
        'baidu' => 'http://news.baidu.com/ns?word=%s&pn=%d&cl=2&ct=0&tn=news&rn=%d&ie=utf-8&bt=0&et=0',
        'baidu1' => 'http://news.baidu.com/ns?word=intitle:%s&sr=0&cl=2&pn=%d&rn=%d&tn=news&ct=0&clk=sortbytime',
        'baidu2' => 'http://news.baidu.com/ns?word=intitle:%s&pn=%d&sr=0&cl=2&rn=%d&tn=news&ct=0&clk=sortbytime',
        'baidutitle' => 'http://news.baidu.com/ns?word=intitle:%s&ct=0&pn=%d&rn=%d&ie=utf-8&rsv_bp=1&sr=0&cl=2&f=8&prevct=no&tn=newstitle',		// 百度新闻标题搜索
        'baidusearch' => 'https://www.baidu.com/s?ie=utf-8&f=8&rsv_bp=1&rsv_idx=1&tn=baidu&wd=intitle:%s&pn=%d&rn=%d&rsv_enter=1&gpc=stf%%3D%d%%2C%d%%7Cstftype%%3D1&tfflag=1',
        'sogou' => 'http://news.sogou.com/news?&clusterId=&p=42230305&query=%s&mode=1&media=&sort=1&num=50&ie=utf8',
        '360search' => 'http://www.so.com/s?q=%s&pn=%d&adv_t=d',
        'google' => 'http://www.google.com.hk/search?q=%s&num=100&newwindow=1&safe=strict&hl=zh-CN&gbv=2&tbs=sbd:1,nsd:1,qdr:d&tbm=nws&filter=0&dpr=1',
        'baidubaijia' => 'https://www.baidu.com/s?ie=utf-8&f=8&rsv_bp=1&rsv_idx=1&tn=baidu&wd=%s&pn=%d&rn=%d&rsv_enter=1&gpc=stf%%3D%d%%2C%d%%7Cstftype%%3D1&tfflag=1',
//        '315che' => 'http://tousu.315che.com/che_v3/struts_tousu/page?page=%s&stat=1',		// 315车投诉
//        'qctsw' => 'http://www.qctsw.com/doTousu_search?%s',		// 汽车投诉网
        '12365auto' => 'http://www.12365auto.com/search/Search.aspx?search=%s&page=%d&t=%d',
//        'sohutousu' => 'http://tousu.auto.sohu.com/view/newUserComplaints.ac?modelId=%d',
        'cheshi' => 'http://search.cheshi.com/default.php?q=%s&page=%d',    // 网上车市
        //'wangtong' => 'http://auto.news18a.com/search/?type=news&keyword=%s',       // 网通社
        'wangtong' => 'http://auto.news18a.com/search/news/%s/',       // 网通社
        'kuaiyatoutiao' => 'http://minivideosearch.dftoutiao.com/search_pc/searchcomplex?jsonpcallback=jQuery18306321030936378016_1511742904600&keywords=%s&stkey_zixun=&lastcol_zixun=&stkey_video=&lastcol_video=&splitwordsarr=&uid=14988936424175451&qid=k002&softtype=toutiao&softname=DFTT&browser_type=chrome62.0.3202.75&pixel=1600*900',       // 快压头条
        'guosou'  => 'http://news.chinaso.com/search?wd=%s&startTime=1w&endTime=now&page=%d&order=time',//中国搜索
        'autohome-dealer' => 'http://dealer.autohome.com.cn/%s/%s',  //汽车之家经销商频道
        'baiduzixun'=> 'https://www.baidu.com/s?rtt=1&bsst=1&cl=2&tn=news&word=%s&pn=%d',

        "chinanews"=>"http://sou.chinanews.com/search.do?field=content&q=%s&ps=100&time_scope=7&sort=pubtime",

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