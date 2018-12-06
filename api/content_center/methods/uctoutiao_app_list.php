<?php

/**
 * @filename uctoutiao_app_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl
 * @createtime 2017-12-20  11:04:11
 * @updatetime
 * @version 1.0
 * @Description
 * uc头条api解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://localhost/yuqing/wii_spider/app_list/spider_result/ccef22a80be2be6cc930f7d5bc216654";
//$collect_download_url = "http://iflow.uczzd.cn/iflow/api/v1/channel/323644874?app=ucnews-iflow&recoid=&ftime=&method=new&count=20&no_op=0&auto=0&content_ratio=0&sc=&puser=1&uc_param_str=dnnivebichfrmintcpgieiwidsudsvadmeprpf&ve=3.6.0.360&bi=997&ch=&fr=iphone&nt=2&gp=&wf=&sv=app&ad=&pr=UCNewsApp&pf=195";

//$list_id = $post_obj['list_id'];
$json_str = file_get_contents($collect_download_url);
$json_array = json_decode($json_str, true);
//print_r($json_array);
$final_result = array();
if(!empty($json_array['data']['items']) ) {

    foreach($json_array['data']['articles'] as $data) {
        //print_r($data);
        if(!isset($data['url'])) {
            continue;
        }

        $final_result[] = array(
            'id' => $data['id'],
            'title' => $data['title'],
            'url' => "http://m.uczzd.cn/ucnews/news?app=ucnews-iflow&aid=".$data['id'],
            'author' => $data['source_name'],
            'source' => 1,
            'time' => substr($data['publish_time'], 0, 10),
            'channel' => $data['source_name'],
            'media' => 'uc头条',
//            'collect_from_site' => $post_obj['collect_from_site'],
        );
    }
}
$response_result = $final_result;
//print_r($final_result);