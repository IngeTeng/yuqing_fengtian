<?php

/**
 * @filename weixin_list.php
 * @encoding UTF-8
 * @author WiiPu CzRzChao
 * @createtime 2016-7-28  15:31:45
 * @updatetime 2016-11-27  19:31:45 yjl
 * @version 1.0
 * @Description
 * 解析微信搜索结果
 *
 */

// ini_set('display_errors',1);            //错误信息  
// ini_set('display_startup_errors',1);    //php启动错误信息  
// error_reporting(-1);                    //打印出所有的 错误信息  
// ini_set('error_log', dirname(__FILE__) . '/weixin_log.txt'); //将出错信息输出到一个文本文件 

// 获取缓存好的html
$collect_download_url = $post_obj['collect_download_url'];
//$getcomment_url = 'http://mp.weixin.qq.com/mp/getcomment?';     // 获取评论信息的url
//$collect_download_url = "http://weixin.sogou.com/weixin?query=adf&fr=sgsearch&sourceid=inttime_day&type=2&ie=utf8&cid=null&tsn=1&page=1";
//$collect_download_url = "http://localhost/htdocs/yuqing2/wii_spider/weixin_list/spider_result/402de9f8419d646b9cd77b00224ee8da";

$html = file_get_contents($collect_download_url);
$final_result = array();

$main_reg = '/<div class="news-box">(.*)<div class="bottom-form">/s';
preg_match($main_reg, $html, $main_result);

$li_reg = '/<li (.*?)<\/li>/s';
preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//unset($li_result[0]);
//print_r($li_result);

foreach($li_result as $li) {
    //print_r($li[0]);
    $box_reg = '/<div class="txt-box">(.*)<div class="moe-box">/s';
    preg_match($box_reg, $li[0], $box_result);
    //print_r($box_result[0]);

    //$url_reg = '/<a target="_blank" href="(.*?)"/s';      // 微信文章地址

    $url_reg = '/data-share="(.*?)"/s';      // 换成抓取data-share地址
    $title_reg = '/<h3>(.*?)<\/h3>/s';        // 标题
    $content_reg = '/<\/h3>(.*?)<div class="s-p"/s';      // 内容
    $author_reg = '/article_account_[\d]*">(.*?)<\/a>/s';       // 作者
    $time_reg = '/<div class="s-p" t="(.*?)"/s';     // 发布时间

    preg_match($url_reg, $box_result[0], $url_result);
    preg_match($title_reg, $box_result[0], $title_result);
    preg_match($content_reg, $box_result[0], $content_result);
    preg_match($author_reg, $box_result[0], $author_result);
    preg_match($time_reg, $box_result[0], $time_result);

    $title = trim(strip_tags($title_result[1]));        // 删除标签
    $content = trim(strip_tags($content_result[1]));     // 删除标签和前后空格

    if (empty($content)) {
        $content = $title;
    }

    $url = str_replace('&amp;', '&', $url_result[1]);
    //$url = "http://mp.weixin.qq.com/s?src=3&timestamp=1493789750&ver=1&signature=b-KJ1zp8GMjH25dhWdVcm0RXPV3Ug6DgXN8O*zSmiVjqc8hpBMN3UmDevY16XGoNsgyM-iLuho6khZjFhQ2uQsxgmZWbbzufb7l**a-NmVhvg4Kh022VRm2IwlKgOQhhPmexvlCZLeRsCO280-o51sip6l7RT1witPb6F7sbcp0=";
//    $p = strpos($url, 's?');
//    if ($p === false) {
//        continue;
//    }
    // $comment_array = json_decode(file_get_contents($getcomment_url. substr($url, $p+2)), true);
    // if ($comment_array['base_resp']['ret'] !== 0) {
    //     continue;
    // }
    // 筛选无关内容
    // if(!strstr($title, $post_obj['keyword']) and !strstr($content, $post_obj['keyword'])) {
    //     continue;
    // }

    //print_r($url_result);
    //print_r($title_result);

    //获取文章的永久链接start  ----2017-02-13 yjl
    // $cookie = 'wxtokenkey=b996adf4182128976b63d9a8c6989a0875d733b43f547806b25a767d3e21016a; wxticket=155431357; wxticketkey=bc7e808929627345b5ee79176082ce6b75d733b43f547806b25a767d3e21016a; wap_sid=CNHBgKkDEkBTV24yQ05RSE5zb1k2S2lXcXN4akYzb2xLOW1FczMwZ2FkbXNyUlRJOGFld2dYY0QyNWZub0U5eU5GU2ZwUlFxGAQg/BEosprc+gsw9Oj/xAU=; wap_sid2=CNHBgKkDElw4cjFORWUxRVJkU2RaZGNiN0llWWJOdk5RekdVS05KM0hkN29hb0tEV29FY180ZU1FVUFBbGJfU1MxS0ZoaDI1MEhHWVZITjF2Uzd4UnN1WFRnZlFqSHNEQUFBfg==';//访问临时链接获取永久链接时需要用的cookie
    //print_r($url);
    // $cookie = "pgv_pvid=6015893869;rewardsn=047603770350eed85c92; wxuin=891298001; wxtokenkey=029d3fcd75fe573931d4a915b91da1fdb708195ef1e43ea95bb724dd758c9298; wxticket=1439915217; wxticketkey=70b183381a16b9b388f1cadab141e017b708195ef1e43ea95bb724dd758c9298; wap_sid=CNHBgKkDEkBhaTU5b1Q5dWY4bFllczZVclJ2QmREM1V1TEF0dFVRVFVCQ3NYdTNNVGNkSzBTMXB4WE1jZ0hFcGhZZFVxODBJGAQg/BEop/GQuAsw/N6lyAU=; wap_sid2=CNHBgKkDElw2S090MGg3NGdfYzN2MDQ1LTZEbTQ2UWxMOGZDekNrMUJBeTVFUFNSRXBMUG0zUFZGX1RRc1hCZFBqbHdHUUNOSVJ5YWRXd0VFS3BWYnkwMWw5VVZPSVlEQUFBfjD83qXIBQ==; ";
    // $agent = "Mozilla/5.0 (Linux; Android 7.0; TRT-AL00A Build/HUAWEITRT-AL00A; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/53.0.2785.49 Mobile MQQBrowser/6.2 TBS/043204 Safari/537.36 MicroMessenger/6.5.7.1041 NetType/WIFI Language/zh_CN";
    // $ch = curl_init($url); //初始化
    // curl_setopt($ch, CURLOPT_HEADER, 1); //返回header部分
    // curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //返回字符串，而非直接输出
    // //curl_setopt($ch, CURLOPT_COOKIE, $cookie); //设置cookie
    // curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    // $res = curl_exec($ch); //执行curl并赋值给$res
    // //echo($res);
    // //$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    // $info = curl_getinfo($ch);
    // // 根据头大小去获取头信息内容
    // $header = substr($res, 0, $headerSize);
    // // $redirect_url = $info['redirect_url'];//取出永久链接
    // // if(!empty($redirect_url) && strstr($redirect_url, 'weixin.qq.com')){//验证一下，防止获取失败
    // //     $url = $redirect_url;
    // // }
    // curl_close($ch); //关闭curl
    //$header = $http_response_header;
    //print_r(get_headers($url));
    //print_r($info);
    //print_r($header);

    //获取永久链接end
    //break;
    $final_result[] = array(
        'url' => $url,     // 微信搜索url处理
        'title' => $title,
        'time' => $time_result[1],
        'content' => $content,
        'author' => $author_result[1],
        'read_num' => 0,//$comment_array['read_num'],
        'like_num' => 0,//$comment_array['like_num'],
    );
}
$response_result = $final_result;
//file_put_contents('weixin-result', var_export($final_result, true));
//print_r($final_result);