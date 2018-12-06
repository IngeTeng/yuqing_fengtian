<?php

/**
 * @filename pcauto_viedo_list.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-17  15:37:14
 * @updatetime 2016-8-17  15:37:14
 * @version 1.0
 * @Description
 * 太平洋汽车网视频搜索解析
 * 
 */

// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/bbs_list/spider_result/b3047079e09f0cf38ce1ed4446cec948";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<ul class="ulPic2 w1000 clearfix ulml20">(.*)<\/ul>/s';

// 最外层匹配
preg_match($main_reg, $html, $main_result);

$li_reg = '/<li>(.*?)<\/li>/s';
// 第四个参数用于所有结果排序
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);

foreach($li_result as $value) {
    $utf8_content = iconv('gbk', 'utf-8', $value[0]);

    $url_reg = '/<a href="([^"]+)"/s';
    $title_reg = '/title="([^"]+)">/s';        // 标题
    $source_reg='/<p class="td"><em>类型:<\/em>(.*)<\/p>/s';
    preg_match($url_reg, $utf8_content, $url_result);
    preg_match($title_reg, $utf8_content, $title_result);
    preg_match($source_reg, $utf8_content, $source);
    if($source=="原创视频"){
        $source=0;
    }else{
        $source=1;
    }
    $final_result[] = array(
        'title' => $title_result[1],
        'url' => $url_result[1],
        'time' => time(),
        'author' => '',
        'source' => $source,
        'media' => '太平洋汽车视频',
    );
    // 删除上一次的记录
    unset($author_result);
    unset($forum_result);
}

$response_result = $final_result;
file_put_contents('pcauto_vldeo-lists', var_export($final_result, true));