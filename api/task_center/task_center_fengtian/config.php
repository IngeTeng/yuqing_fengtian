<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao yjl 
 * @createtime 2016-7-21   15:12:30
 * @updatetime 2016-11-18  22:27:30
 * @version 1.0
 * @Description
 * 任务中心的总配置文件
 * 
 */

// log级别配置
define('LOG_LEVEL', 15);

// 时间
date_default_timezone_set('PRC');

// 当前路径
define("BASE_PATH", str_replace("\\", "/", realpath(dirname(__FILE__))));
// 日志保存路径
define("LOG_HANDLER", BASE_PATH. '/log/'. date('Y_m_d'));
// 代理保存路径
define('PROXY_HANDLER', BASE_PATH. '/proxy/'. date('Y_m_d'));

// secret配置
define("SECRET", "secret=3723905834958739587349857938292");

// 错误报告等级
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 数据库配置 
define("DB_HOST", "172.26.133.67");
define("DB_NAME", "yuqing");
define("DB_USER_NAME", "root");
define("DB_PASSWORD", "*Wiipuyuqing#");

// 爬取数量
$SPIDER_KEYWORD_NUM = array(
    //微博
    'sina_weibo_list'    => 'ALL', 
    'sina2_weibo_list'   => 'ALL', 
    'tencent_weibo_list' => 'ALL',
    
    // 新闻
    '360_news_list'         => 'ALL',
    'baidu_news_list'       => 'ALL',
    'baidutitle_news_list'  => 'ALL',
    'baidusearch_news_list' => 'ALL',
    'sogou_news_list'       => 'ALL',        
    '360search_news_list'   => 'ALL',   // 360搜索
    'google_news_list'      => 1,
    'baidubaijia_news_list' => 'ALL',
    '315che_news_list'      => 'ALL', 	// 315che投诉
    'qctsw_news_list'       => 'ALL', 	// 汽车投诉网
    '12365auto_news_list'   => 'ALL',   // 车质网
    '12365auto_bbs_list'   => 'ALL',   // 车质网论坛

    'sohutousu_news_list'   => 'ALL', 	// 搜狐汽车投诉
    'cheshi_news_list'      => 'ALL', 	// 网上车市
    'wangtong_news_list'    => 'ALL',	// 网通社
    'kuaiyatoutiao_news_list' => 'ALL',   // 快呀头条
    'guosou_news_list'      => 'ALL',   // 中国搜索
    'baiduzixun_news_list'  => 'ALL',   // 百度资讯

    'chinanews_news_list'=>'ALL',
    'ifengsearch_news_list'=>'ALL',


    // 微信
    'sogou_weixin_list'     => 'ALL',
    
    // bbs
    'tianya_bbs_article'    => 'ALL',
    'baidu_bbs_list'        => 'ALL',   // 贴吧
    'sogou_bbs_list'        => 'ALL',   // 搜狗bbs搜索
    'autohome_bbs_list'     => 'ALL',   // 汽车之家bbs搜索
    'autohome2_bbs_list'    => 'ALL',
    'pcauto_bbs_list'       => 'ALL',   // 太平洋汽车网
    'xcar_bbs_list'         => 'ALL',   // 爱卡汽车论坛
    'baidusearch_bbs_list'  => 'ALL',   // 百度网页搜索-论坛
    '360search_bbs_list'    => 'ALL',   //使用360搜索爬取汽车之家
    // 视频搜索    
    'tudou_video_list'      => 'ALL',
    'youku_video_list'      => 'ALL',
    'sohu_video_list'       => 'ALL',
    '56lesou_video_list'    => 'ALL',
    'pcauto_video_list'     => 'ALL',   // 太平洋汽车网
    
    // 知道
    'sogou_zhidao_list'     => 'ALL',
    '360_zhidao_list'       => 'ALL',
    'baidu_zhidao_list'     => 'ALL',
    
    // 博客
    'sina_blog_list'        => 'ALL',
    'autohome_blog_list'    => 'ALL',
    
    // appsearch
    'zaker_appsearch_list'    => 'ALL',
    'autohome_appsearch_list' => 'ALL',
    'toutiao_appsearch_list'  => 'ALL',
    'yidianzx_appsearch_list' => 'ALL',  //一点资讯
    'xcar_appsearch_list'     => 'ALL',  //爱卡汽车
    'yiche_appsearch_list'    => 'ALL',  //易车网
    'sohuwap_appsearch_list'  => 'ALL',  //搜狐新闻wap
    'dftoutiao_appsearch_list' => 'ALL', //东方头条
    'sina_appsearch_list'     => 'ALL', //sinaapp客户端搜索

);

// 爬取中心关键词分配
$COLLECT_NUMBER = array(
    'spider_1' => 1,
);
