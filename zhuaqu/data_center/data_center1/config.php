<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-6-15  15:57:36
 * @updatetime 2016-6-15  15:57:36
 * @version 1.0
 * @Description
 * 总配置文件
 */

// log级别设置
define('LOG_LEVEL', 15);

// 返回状态码定义
define('REQUEST_OK', 200);

// 时区设置
date_default_timezone_set("PRC");

// 错误报告等级
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 当前目录
define("BASE_PATH", str_replace('\\', '/', realpath(dirname(__FILE__))). '/');

// 扫描间隔
define("SELEEP_TIME", 3);

// 扫描目录
const TARGET_PATH = '/home/wwwroot/default/wii_spider/result/';
const TARGET_FAIL_PATH = '/home/wwwroot/default/data_center/fail/';

// 扫描后的文件的处理方式, 1为转移,2为删除
define("FILE_AFTER_TREATING", 2);

// 转移路径
if(FILE_AFTER_TREATING == 1) {
    define("PAHT_AFTER_TREATING", BASE_PATH. 'old_result/');
}

// 日志保存目录
define("LOG_HANDLER", BASE_PATH. 'log/');

// secret配置
define("SECRET", "secret=3723905834958739587349857938292");

// 数据网关地址
define('DATABASE_CENTER', 'http://172.26.133.69:8088/yuqing/db_center_fengtian/db_center.php');//121.199.28.140  10.132.58.171

// 语意分析中心
define('PROPERTY_HOST', '172.26.133.69');
define('PROPERTY_PORT', '5040');

// 关键字命中中心
define('KEYWORD_HOST', '172.26.133.69');
define('KEYWORD_PORT', '5010');

// 提取中心的地址
define("CONTENT_CENTER", "http://172.26.133.69:8088/yuqing/content_center/content_center.php");

define('SLEEP_TIME', 1200);

// 需要替换的特殊字符
$REPLACE_STRS = array('®', '©', '�', '✎﹏﹏', '』', '『', '&nbsp;', '•', '&gt;', '&ldquo;', '&rdquo;', '&middot;', '&mdash;', '\s', '↓', '①', '●', '■', '&lt', '&emsp;', 'の');