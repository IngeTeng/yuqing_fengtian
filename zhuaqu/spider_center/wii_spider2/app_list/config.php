<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-9  15:58:38
 * @updatetime 2016-8-9  15:58:38
 * @version 1.0
 * @Description
 * api信息配置
 */


namespace SPIDER_CENTER\APP_LIST;

// 配置类
class Configure {
    const COLLECT_KIND = 'app';       // 爬取内容种类
    const COLLECT_CONTENT_KIND = 'list';        // 爬取内容排版
    const SLEEP_TIME = 2;      // 爬取间隔
    
    // 抓取地址
    static public $url_hosts = array(
        'cheshi' => 'http://api.cheshi.com/services/mobile/api.php?api=mobile.wscs_v3.data&act=newslist&page=1&pagesize=200',       // 车市网
        'yiche' => 'http://api.app.yiche.com/webapi/newslist.ashx?pageindex=1&pagesize=50&categoryid=%d',      // 易车网
        'ifeng' => 'http://api.iclient.ifeng.com/ClientNews?id=%s&page=1',      // 凤凰网   
        'sina' => 'http://api.sina.cn/sinago/list.json?uid=0b3cc567b3eac3fd&platfrom_version=4.4.4&wm=b207&oldchwm=14010_0001&imei=860308025820347&from=6044095012&chwm=14010_0001&AndroidID=9334ff85f934e11e8d3dfe9a28138f8c&v=1&s=20&IMEI=97c982d0025dbcafdd32ad2cd7b72f73&p=1&user_id=2502265814&MAC=d926fe2fdf80d6ad2796e999d6635561&channel=%s',       // 新浪
        'autohome' => 'news.app.autohome.com.cn/%s',       // 汽车之家
        '163' => 'http://c.m.163.com/nc/article/headline/%s/0-20.html',     
        'toutiao' => 'http://ic.snssdk.com/2/article/v21/stream/?category=%s&count=50',     // 今日头条
        'pcauto' => 'http://mrobot.pcauto.com.cn/v2/cms/channels/%s?pageNo=1&pageSize=50&serialIds=&v=4.0.0',       // 太平洋汽车网
        'xcar'  => 'http://a.xcar.com.cn/cms/interface/5.0/getNewsList.php?type=%d&cityId=&offset=0&limit=10&ver=5.3.4',      // 爱卡汽车
        'zaker1' => 'http://iphone.myzaker.com/zaker/news.php?_appid=AndroidPhone&_bsize=720_1280&_version=5.0&app_id=%d',      // ZAKER
        'zaker2' => 'http://iphone.myzaker.com/zaker/blog.php?_appid=AndroidPhone&_bsize=720_1280&_version=5.0&app_id=%d&catalog_appid=7',
        'uctoutiao' => 'http://iflow.uczzd.cn/iflow/api/v1/channel/%s?app=ucnews-iflow&recoid=&ftime=&method=new&count=20&no_op=0&auto=0&content_ratio=0&sc=&puser=1&uc_param_str=dnnivebichfrmintcpgieiwidsudsvadmeprpf&ve=3.6.0.360&bi=997&ch=&fr=iphone&nt=2&gp=&wf=&sv=app&ad=&pr=UCNewsApp&pf=195',//uc头条
        'dftoutiao' => 'http://refreshnews.dftoutiao.com/toutiao_appnew02/newsgzip?type=%s&pgnum=%d', //东方头条
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
