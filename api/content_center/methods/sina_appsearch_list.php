<?php

/**
 * @filename sina_appsearh_list.php 
 * @encoding UTF-8 
 * @author WiiPu yjl 
 * @createtime 2018-01-31
 * @updatetime 
 * @version 1.0
 * @Description
 * 新浪app解析
 * 
 */

$collect_download_url = $post_obj['collect_download_url'];
//$collect_download_url = "http://sinanews.sina.cn/interface/search.d.html?callback=initFeed&refresh=1&apiEnv=online&keyword=大众&page=1&size=100&newpage=0&chwm=3023_0001&imei=26c312d18f21d5e86a39d1cf798431734fc287d6&did=26c312d18f21d5e86a39d1cf798431734fc287d6&from=6066993012";

//date_default_timezone_set('Asia/Shanghai');
$json_str = file_get_contents($collect_download_url);
$list_reg = '/\(\{(.*?)\}\)/s';
preg_match($list_reg, $json_str, $json_str2);

$json_array = json_decode('{'.$json_str2[1].'}', true);
$final_result = array();
//print_r($json_str2[1]);
$data_list = $json_array['data']['feed3'];
//print_r($data_list);
foreach($data_list as $data) {
    
    if(empty($data['time'])) {
        continue;
    }
    // 处理时间
    $time_str = '';
    $pubtime = 0;
    //print_r(date('y-m-d H:i:s', time()));
    if(!empty($data['time'])) {
        $time_str = $data['time'];
        $p = strpos($time_str, "分");
        $sec = 60;
        if(!$p) {
            $p = strpos($time_str, "小时");
            $sec = 3600;
        }
        if(!$p) {
            $p2 = strpos($time_str, '天');
            if($p2 !== false) {
                $pubtime = strtotime(date('Y-m-d ', time()). substr($time_str, 7) );
            }
        }
        if($p > 0) {
            $dd = substr($time_str, 0, $p);
            $pubtime = time() - $dd * $sec;
            if($sec == 86400) {
                $pubtime = time() - 86400;
            }
        }elseif(strpos($time_str, '月')){
            if(strpos($time_str, '年')){
                $time_str = str_replace("年","-",$time_str);
                $time_str = str_replace("月","-",$time_str);
                $time_str = str_replace("日","",$time_str);
                $pubtime = strtotime($time_str);
            }else {
                $time_str = str_replace("月","-",$time_str);
                $time_str = str_replace("日","",$time_str);
                $pubtime = strtotime('2018-'.$time_str);
            }

        }elseif(empty($pubtime)){
            $pubtime = strtotime($time_str);
        }
    }
    
    $final_result[] = array(
            'url' => $data['url'],
            // 'cal' => date('Y-m-d H:i ', $pubtime),
            // 'real' =>  $data['time'],
            'time' => $pubtime, 
            'title'  => $data['title'],
            'content' => $data['title'],
            'author'=>$data['user']['name'],
            'source'=>1,

            'from'   => '新浪',
            'media' => '新浪',
    );
}

$response_result = $final_result;
//file_put_contents('sinaapps', var_export($final_result, true));
//print_r($final_result);