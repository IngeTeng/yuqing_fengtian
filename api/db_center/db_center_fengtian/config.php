<?php

/**
 * @filename config.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-6-19  20:17:37
 * @updatetime 2016-6-19  20:17:37
 * @version 1.0
 * @Description
 * 数据库网关总配置文件
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

// secret配置
define("SECRET", "secret=3723905834958739587349857938292");

// 错误报告等级
error_reporting(0);
ini_set('display_errors', 1);

// 数据库配置 
define("DB_HOST", "172.26.133.67");

define("DB_NAME", "yuqing");
define("DB_USER_NAME", "root");
define("DB_PASSWORD", "*Wiipuyuqing#");
