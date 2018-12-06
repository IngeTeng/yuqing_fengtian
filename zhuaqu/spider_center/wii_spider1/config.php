<?php

/**
 * @filename cofig1.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-20  12:11:25
 * @updatetime 2016-8-20  12:11:25
 * @version 1.0
 * @Description
 * 爬取中心配置文件
 * 
 */

// 时区设置
date_default_timezone_set("PRC");

// 当前路径
define("BASE_PATH", str_replace("\\", "/", realpath(dirname(__FILE__))));

// 日志保存路径
define("LOG_HANDLER", BASE_PATH. '/log/');

// 错误保存路径
define("WARN_PATH", BASE_PATH. '/include/warn/');

// log级别配置
define("LOG_LEVEL", 15);

// 解析result保存路径
define("RESULT_PATH", BASE_PATH. '/result');

// 定义当前服务器地址,用于拼接下载地址.....
define("COLLECT_HOST", "172.26.133.68");		// 
define("COLLECT_PORT", "80");
define("COLLECT_NAME", "spider_1");

// 提取中心的地址
define("CONTENT_CENTER", "http://172.26.133.69:8088/yuqing/content_center/content_center.php");

// 任务中心的地址
define('TASK_CENTER', 'http://172.26.133.69:8088/yuqing/task_center_fengtian/task_center.php');

// secret配置
define("SECRET", "secret=3723905834958739587349857938292");

// 错误报告等级
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 状态码宏定义
define("REQUEST_OK", 200);

// 抓取文件处理方式, 1为保留,2为删除, 3为转移
define("SPIDER_FILE_AFTER_TREATING", 2);

if(SPIDER_FILE_AFTER_TREATING == 3) {
    define("SPIDER_PATH_AFTER_TREATING", BASE_PATH.'/old_spider_result');
}

// 通用的爬取间隔时间
define('COMMON_SLEEP',5000);//7200
// app爬取时间间隔
define('APP_SLEEP', 1000);

// php执行文件地址
define('PHP_PATH', ' php ');

// 爬取间隔
define('SPIDER_SLEEP', json_encode(array(
    //微博
    'sina_weibo_list' => 6000,
    'sina2_weibo_list' => 400,          
    'tencent_weibo_list' => 2*COMMON_SLEEP,       
    
    // 新闻
    '360_news_list' => 2*COMMON_SLEEP,      
    'baidu_news_list' => 2*COMMON_SLEEP,
    'baidu1_news_list' => 2*COMMON_SLEEP,
    'baidu2_news_list' => 2*COMMON_SLEEP,
    'baidutitle_news_list' => 2*COMMON_SLEEP,	// 百度新闻标题搜索
    'baidusearch_news_list' => 2*COMMON_SLEEP,	// 百度网页搜索一天之内

    'sogou_news_list' => COMMON_SLEEP,         
    '360search_news_list' => 2*COMMON_SLEEP,      // 360搜索
//    'google_news_list' => COMMON_SLEEP,     // 谷歌搜索 先不管
    'baidubaijia_news_list' => COMMON_SLEEP,        // 百度百家
    '315che_news_list' => COMMON_SLEEP,		// 315车投诉
    'qctsw_news_list' => COMMON_SLEEP,		// 汽车投诉网
    '12365auto_news_list' => COMMON_SLEEP,
    'sohutousu_news_list' => COMMON_SLEEP,
    'cheshi_news_list' => COMMON_SLEEP*2,	// 网上车市
    'wangtong_news_list' => COMMON_SLEEP,		// 网通社
    'kuaiyatoutiao_news_list' => COMMON_SLEEP,       // 快压头条
    'guosou_news_list' => 2*COMMON_SLEEP,       // 国搜
    'baiduzixun_news_list'=>2*COMMON_SLEEP,
    'ifengsearch_news_list'=>2*COMMON_SLEEP,
        
    // 微信
    'sogou_weixin_list' => 14800*2,      
    
    // bbs
    'tianya_bbs_article' => 2*COMMON_SLEEP,     
    'ifeng_bbs_article' => 18000,              // 凤凰汽车论坛
    'baidu_bbs_list' => COMMON_SLEEP,          // 贴吧
    //'sogou_bbs_list' => COMMON_SLEEP,          // 搜狗bbs搜索
    'autohome_bbs_list' => COMMON_SLEEP,       // 汽车之家bbs搜索
    'autohome2_bbs_list' => COMMON_SLEEP,       // 汽车之家bbs搜索2
    'pcauto_bbs_list' => COMMON_SLEEP,         // 太平洋汽车网
    'xcar_bbs_list'   => COMMON_SLEEP,         //爱卡汽车论坛
    'baidusearch_bbs_list' => COMMON_SLEEP,    //百度搜索论坛
    '360search_bbs_list' => COMMON_SLEEP,   //360搜索论坛

    
    // 视频搜索    
    'tudou_video_list' => COMMON_SLEEP,       
    //'youku_video_list' => COMMON_SLEEP,     
    'sohu_video_list' => COMMON_SLEEP,      
    '163_video_list' => COMMON_SLEEP,     
    '56lesou_video_list' => COMMON_SLEEP,     
    'pcauto_video_list' => COMMON_SLEEP,       // 太平洋汽车网
    
    // 知道
    'sogou_zhidao_list' => COMMON_SLEEP,      
    '360_zhidao_list' => COMMON_SLEEP,  
    'baidu_zhidao_list' => 2*COMMON_SLEEP,        
    
    // app
    'cheshi_app_list' => APP_SLEEP,        // 车市网
    'yiche_app_list' => APP_SLEEP,     // 易车网
    'ifeng_app_list' => APP_SLEEP,       // 凤凰网
    'sohu_app_list' => APP_SLEEP,        // 搜狐
    'sina_app_list' => APP_SLEEP,     // 新浪新闻
    'autohome_app_list' => APP_SLEEP,        // 汽车之家
    '163_app_list' => APP_SLEEP,     // 网易客户端
    'toutiao_app_list' => APP_SLEEP,     // 今日头条
    'tencent_app_list' => APP_SLEEP,     // 腾讯手机客户端
    'pcauto_app_list' => APP_SLEEP,      // 太平洋汽车网
    'xcar_app_list' => APP_SLEEP,        // 爱卡汽车
    'zaker_app_list' => APP_SLEEP,       // ZAKER
    'uctoutiao_app_list' => APP_SLEEP,        // uc头条
    'dftoutiao_app_list' => APP_SLEEP,        // 东方头条
    // 博客
    'sina_blog_list' => COMMON_SLEEP,       // 新浪博客
    'autohome_blog_list' => COMMON_SLEEP,   //汽车之家说客
    
    //appsearch
    'zaker_appsearch_list'    => APP_SLEEP*3,		// ZAKER搜索
    'toutiao_appsearch_list'  => APP_SLEEP*3,	    // 今日头条搜索
    'autohome_appsearch_list' => APP_SLEEP*3, 		// 汽车之家搜索
    'yidianzx_appsearch_list' => APP_SLEEP*3,      // 一点咨询
    'xcar_appsearch_list'     => APP_SLEEP*3,      // 爱卡汽车
    'yiche_appsearch_list'    => APP_SLEEP*3,      // 易车网APP
    'sohuwap_appsearch_list'  => APP_SLEEP*3,      // 搜狐新闻wap
    'dftoutiao_appsearch_list'=> APP_SLEEP*3,      // 东方头条appsearchsin
    'sina_appsearch_list'     => APP_SLEEP*3,      // 东方头条appsearchsin

)));



