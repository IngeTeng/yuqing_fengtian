<?php


/**
 * @filename ifeng_result.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-19  14:49:14
 * @updatetime 2016-8-19  14:49:14
 * @version 1.0
 * @Description
 * 凤凰论坛结果数量
 * 
 */

require_once('../config.php');
require_once(BASE_PATH. '/bbs_article/config.php');

use SPIDER_CENTER\BBS_ARTICLE\Configure;

$ifeng_result_path = Configure::get_ifeng_result_path(BASE_PATH);
$files = glob($ifeng_result_path. '/*.json');
echo count($files);