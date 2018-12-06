<?php
/**
 * 爬虫程序 -- 原型
 *
 * 从给定的url获取html内容
 *
 * @param string $url
 * @return string
 */

function _getUrlContent($url) {
    $handle = fopen($url, "r");
    if ($handle) {
        $content = stream_get_contents($handle, 1024 * 1024);
        return $content;
    } else {
        return false;
    }
}
/**
 * 从html内容中筛选链接
 *
 * @param string $web_content
 * @return array
 */
function _filterUrl($web_content) {
    $str=date("Y-m-d H:i:s",time()).","."汽车之家,";
    for($i=1;$i<100;$i++){
        $string=explode('seriesname',explode('brandid',$web_content)[$i]);
        $num=strpos($string[1],",");
        print_r(substr($string[1],2,$num-1));
        $str.=substr($string[1],2,$num-1);
    }

//    $main_reg = '/<ul class="rank-list-ul"(.*?)<\/ul><\/div>/s';
//    preg_match($main_reg, $web_content, $main_result);
//    save_file('txt','file.txt',$main_result);
//    exit;
//    var_dump($main_result);
//    exit;
//    $li_reg = '/<li (.*?)<\/li>/s';

// 第四个参数用于所有结果排序
    // $result=preg_match_all($li_reg, $main_result[0], $li_result,PREG_PATTERN_ORDER);

//    $li_reg = '/<li>(.*?)<\/li>/s';
//    preg_match_all($li_reg, $main_result[0], $li_result, PREG_SET_ORDER);
//
////    $reg_tag_a = '<h4><a href="//www.autohome.com.cn/78/#levelsource=000000000_0&amp;pvareaid=101596">([\s\S]*?)</a></h4>';
////    '/<h3   data-title="(.*?)".*?<span class="sales-count">月售(.*?)份<\/span>/'
//    $reg_tag_a = '([\s\S]*?)';
//    $reg_tag_a ='/<span   class="red">(.*?)张<\/span>/';
//    $result = preg_match_all($reg_tag_a, $web_content, $match_result);
//    if ($str) {
//        return $match_result[1];
//    }
    if ($str) {
        $fp_puts = fopen("url.txt", "ab");
        fputs($fp_puts, $str . "\r\n");
//        save_file('txt',time().'.txt',$str);
    }
}
/**
 * 修正相对路径
 *
 * @param string $base_url
 * @param array $url_list
 * @return array
 */
function _reviseUrl($base_url, $url_list) {
    $url_info = parse_url($base_url);
    $base_url = $url_info["scheme"] . '://';
//    if ($url_info["user"] && $url_info["pass"]) {
//        $base_url .= $url_info["user"] . ":" . $url_info["pass"] . "@";
//    }
//    $base_url .= $url_info["host"];
//    if ($url_info["port"]) {
//        $base_url .= ":" . $url_info["port"];
//    }
    $base_url .= $url_info["path"];
//    print_r($base_url);
    if (is_array($url_list)) {
        foreach ($url_list as $url_item) {
            if (preg_match('/^http/', $url_item)) {
                // 已经是完整的url
                $result[] = $url_item;
            } else {
                // 不完整的url
                $real_url = $base_url . '/' . $url_item;
                $result[] = $real_url;
            }
        }
        return $result;
    } else {
        return;
    }
}
/**
 * 爬虫
 *
 * @param string $url
 * @return array
 */
function crawler($url) {
    $content = _getUrlContent($url);
    if ($content) {
        $url_list = _reviseUrl($url, _filterUrl($content));
        if ($url_list) {
            return $url_list;
        } else {
            return ;
        }
    } else {
        return ;
    }
}




// 保存文件
function save_file($file_path, $file_name, $file_content) {
    // 处理导入字符串
    $path_len = strlen($file_path);
    if($file_path[$path_len-1] !== '/') {
        $file_path .= '/';
    }
    if($file_name[0] === '/') {
        $file_name = substr($file_name, 1);
    }

    // 先保存临时文件
    $temp_file = $file_path. "$file_name.temp";
    $file_result = file_put_contents($temp_file, $file_content, FILE_APPEND);

    // 如果保存成功,修改文件名
    if($file_result === false) {
        return false;
    }
    else {
        rename($temp_file, $file_path. $file_name);
        return true;
    }
}

/**
 * 测试用主程序
 */
function main() {
    $current_url = "https://openapi.autohome.com.cn/autohome/uc-news-quickappservice/msapi/car/Www_LevelFindCar?_callback=jQuery17209236370851914641_".time()."&_appid=car&cityId=110100&access_token=JYEXk1fV6DdEclgbb0veXYHFrvfLYm21&level=b&price=0_0&displacement=0.0_0.0&drive=0&gear=0&structure=0&attribute=0&fuel=0&country=0&seat=0&config=0&_=".time().""; //初始url
//    echo $current_url;
    $fp_puts = fopen("url.txt", "ab"); //记录url列表
    $fp_gets = fopen("url.txt", "r"); //保存url列表
    do {
        $result_url_arr = crawler($current_url);
        if ($result_url_arr) {
            foreach ($result_url_arr as $url) {
                fputs($fp_puts, $url . "\r\n");
            }
        }
    } while ($current_url = fgets($fp_gets, 1024)); //不断获得url
}


$interval_time = 1;
// $start_date = '2018-08-28 15:00';
// $end_date   = '2018-08-29 15:00';
// $date       = '2018-08-29';
while(true){
    main();
    sleep($interval_time);
}



?>