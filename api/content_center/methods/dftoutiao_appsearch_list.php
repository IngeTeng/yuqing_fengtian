<?php

/**
 * @filename dftoutiao_appsearch_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl 
 * @createtime 2018-01-29
 * @updatetime 
 * @version 1.0
 * @Description
 * 东方头条app搜索解析
 * 
 */


$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://sou2.api.autohome.com.cn/wrap/v3/article/search?_appid=app&_callback=jsonp_2_540&ignores=content&modify=0&offset=0&pf=h5&q=%E4%B8%B0%E7%94%B0&s=1&size=30&tm=app";

$json_str = file_get_contents("compress.zlib://".$collect_download_url);
$final_result = array();
$list_reg = '/\(\{(.*?),"returncode":/s';
preg_match($list_reg, $json_str, $json_str2);
//print_r($json_str);
//print_r($json_str2[1]);
if(!empty($json_str)) {
    $json_array = json_decode('{'.$json_str2[1]. '}', true);
    //print_r($json_array);
    $data_list = $json_array['result']['hitlist'];

    foreach($data_list as $data) {
        
        if(empty($data['data']['url'])) {
            continue;
        }

        $final_result[] = array(
            'id' => $data['id'],
            'title' => $data['data']['title'],
            'url' => $data['data']['url'],
            'time' => strtotime($data['data']['date']),
            'author'=>$data['data']['author'],
            'source'=>1,
            'content' => $data['light']['content'],
            'media' => '东方头条',
            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }    
}

$response_result = $final_result;
//file_put_contents('df-appsearch', var_export($final_result, true));
//print_r($final_result);