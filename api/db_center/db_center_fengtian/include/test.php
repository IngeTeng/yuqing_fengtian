<?php

/**
 * @filename test.php 
 * @encoding UTF-8 
 * @author CzRzChao 
 * @createtime 2016-6-21  16:09:34
 * @updatetime 2016-6-21  16:09:34
 * @version 1.0
 * @Description
 * 
 */

require('../config.php');
require(BASE_PATH. '/include/post2sign.php');
require(BASE_PATH. '/include/log.php');
require(BASE_PATH. '/include/observer.php');
require(BASE_PATH. '/include/sql_methods.php');


$log = Log::Init(LOG_HANDLER);

$requests = array();

$request['article_url'] = 'http://news.16888.com/a/2016/0625/4345793.html';
$request['article_title'] = 'title';
$request['article_content'] = 'abstract';
$request['article_pubtime'] = 2131212;
$request['article_summary'] = 'abstract';
$request['article_comment'] = 'abstract';
$request['article_source'] = '';
$request['article_channel'] = '';
$request['media'] = 'from';
$requests[] = $request;

news_list($requests, $log);