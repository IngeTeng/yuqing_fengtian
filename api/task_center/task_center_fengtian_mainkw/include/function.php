<?php

/**
 * @filename function.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-7-21  15:52:22
 * @updatetime 2016-7-21  15:52:22
 * @version 1.0
 * @Description
 * task_center的常用函数库
 * 
 */

// 获取的html,带模拟登陆
function get_html($url,  $proxy='', $proxy_port='') {
    $ch = curl_init();
    // 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//允许页面跳转，获取重定向
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);      // 60秒超时

    if($proxy != '' and $proxy_port != '') {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }

    // 获取内容
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}


// 发送post请求
function send_post($url, $post_data) {  
    $postdata = http_build_query($post_data);
    $ch = curl_init(); 
    curl_setopt ($ch, CURLOPT_URL, $url); 
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);   // 设置不输出到屏幕 
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,20); 
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    $content = curl_exec($ch); 
    return $content;  
} 

// 删除无效的cookies
function useless_cookies($_pdo, $query) {
    try {
        $result = $_pdo->exec($query);
    } catch (PDOException $ex) {
        return $ex->getMessage();
    }
    if($result == 0) {
    	return false;
    }
    return true;
}

//获取代理
function get_proxy() {
    $proxy_infos = get_proxy3();
   // $proxy_infos = array();
    for ($i=1; $i < 10; $i++) { 
       
    
        $html = get_html('www.xicidaili.com/nn');
        file_put_contents(1, $html);
        //print_r($html);
        $proxy_reg = '/<table id="ip_list">(.*?)<\/table>/s';
        preg_match($proxy_reg, $html, $proxy_list);
        $proxy_list_reg = '/<tr(.*?)<\/tr>/s';
        //print_r($proxy_list);
        preg_match_all($proxy_list_reg, $proxy_list[0], $proxy_li, PREG_SET_ORDER);
        //print_r($proxy_li);
        
        foreach ($proxy_li as $li ) {
            
            $proxy_li_reg = '/<td>(.*?)<\/td>/s';
            preg_match_all($proxy_li_reg, $li[1], $proxy_info, PREG_SET_ORDER);
            //print_r($proxy_info);
            
            if(empty($proxy_info) ) {//or strcmp($proxy_info[3][1], 'HTTP')
                echo '1 ';
                continue;
            }
            $proxy_info[2][1]=strip_tags($proxy_info[2][1]);
            $proxy_infos[] = array(
                'proxy' => $proxy_info[0][1],
                'port' => $proxy_info[1][1],
                'type' => $proxy_info[3][1],
                'detail' => $proxy_info[2][1].$proxy_info[5][1],
            );
        }
    }
    //print_r($proxy_infos);
    return $proxy_infos;
}
function get_proxy3() {
    $proxy_infos = get_proxy4();
    $page = 1;
    for($page=1; $page<=50;$page++){
        $url = 'http://www.66ip.cn/'.$page.'.html';//拼接爬取地址
        $html = get_html($url);
        $html = iconv("gb2312", "utf-8//IGNORE",$html);
        file_put_contents(3, $html);
        //print_r($html);
        $proxy_reg = '/border="2px" cellspacing="0px" bordercolor="#6699ff">(.*?)<\/table>/s';
        preg_match($proxy_reg, $html, $proxy_list);
        $proxy_list_reg = "/<tr(.*?)<\/tr>/s";
        //print_r($proxy_list);
        preg_match_all($proxy_list_reg, $proxy_list[1], $proxy_li, PREG_SET_ORDER);
       //s print_r($proxy_li);
        
        foreach ($proxy_li as $li ) {
            if(strstr($li[1], '端口号') != false){
                continue;
            }
            $proxy_li_reg = '/<td(.*?)<\/td>/s';
            preg_match_all($proxy_li_reg, $li[1], $proxy_info, PREG_SET_ORDER);
            //print_r($proxy_info);
            
            if(empty($proxy_info) ) {
                echo '1 ';
                continue;
            }
            //print_r($proxy_info);
            $proxy_info[0][1]=strip_tags($proxy_info[0][0]);
            $proxy_info[1][1]=strip_tags($proxy_info[1][0]);
            $proxy_info[3][1]=strip_tags($proxy_info[3][0]);
            $proxy_info[4][1]=strip_tags($proxy_info[4][0]);

            $proxy_infos[] = array(
                'proxy' => $proxy_info[0][1],
                'port' => $proxy_info[1][1],
                'type' => $proxy_info[3][1],
                'detail' => $proxy_info[2][1].$proxy_info[4][1],
            );
        }
    }

    //print_r($proxy_infos);
    return $proxy_infos;
}

//获取代理4
function get_proxy4() {
    $proxy_infos = get_proxy5();
    //$proxy_infos = array();
    $html = get_html('ip.baizhongsou.com/');
    file_put_contents(4, $html);
    $html = iconv("GBK", "UTF-8", $html);
    //print_r($html);
    $proxy_reg = '/<table(.*?)<\/table/s';
    preg_match($proxy_reg, $html, $proxy_list);
    $proxy_list_reg = '/<tr>(.*?)<\/tr>/s';
    //print_r($proxy_list);
    preg_match_all($proxy_list_reg, $proxy_list[0], $proxy_li, PREG_SET_ORDER);
    //print_r($proxy_li);
    
    foreach ($proxy_li as $li ) {
        //print_r($li);
        if(strstr($li[1], '代理') != false){
            continue;
        }
        $proxy_li_reg = '/<td *>(.*?)<\/td>/s';
        preg_match_all($proxy_li_reg, $li[1], $proxy_info, PREG_SET_ORDER);
        //$proxy_info[0][0] = trim(strip_tags($proxy_info[0][0]));
        //print_r($proxy_info);

        
        // if(empty($proxy_info) or strcmp($proxy_info[3][1], 'HTTP')) {
        //     echo '1 ';
        //     continue;
        // }
        //$proxy_info[2][1]=strip_tags($proxy_info[2][1]);
        //if($proxy_info[])
        $p = explode(':', $proxy_info[0][1]);
        //print_r($p);
        $proxy_infos[] = array(
            'proxy' => $p[0],
            'port' => $p[1],
            'type' => '',
            'detail' => $proxy_info[1][1].$proxy_info[2][1],
        );
    }
    //print_r($proxy_infos);
    return $proxy_infos;
}
//获取代理5
function get_proxy5() {
    $proxy_infos =  get_proxy6();
    //$proxy_infos = array();
    for($i=1;$i<=7;$i++){
        $html = get_html('http://www.ip3366.net/free/?page='.$i);
        file_put_contents(5, $html);
        $html = iconv("GBK", "UTF-8", $html);
        //print_r($html);
        $proxy_reg = '/<tbody>(.*?)<\/tbody>/s';
        preg_match($proxy_reg, $html, $proxy_list);
        $proxy_list_reg = '/<tr>(.*?)<\/tr>/s';
        //print_r($proxy_list);
        preg_match_all($proxy_list_reg, $proxy_list[0], $proxy_li, PREG_SET_ORDER);
        //print_r($proxy_li);
        
        foreach ($proxy_li as $li ) {
            //print_r($li);
            $proxy_li_reg = '/<td *>(.*?)<\/td>/s';
            preg_match_all($proxy_li_reg, $li[1], $proxy_info, PREG_SET_ORDER);

            if(empty($proxy_info) or strcmp($proxy_info[3][1], 'HTTP')) {
                echo '1 ';
                continue;
            }

            $proxy_infos[] = array(
                'proxy' => $proxy_info[0][1],
                'port' => $proxy_info[1][1],
                'type' => $proxy_info[3][1],
                'detail' => $proxy_info[5][1],
            );
        }
    }
    
    //print_r($proxy_infos);
    return $proxy_infos;
}
//获取代理6

function get_proxy6() {
    $proxy_infos = get_proxy7();
    //$proxy_infos = array();
    $html = get_html('http://www.89ip.cn/apijk/?&tqsl=1200&sxa=&sxb=&tta=&ports=&ktip=&cf=1');
    file_put_contents(6, $html);
    //$html = iconv("GBK", "UTF-8", $html);
   // print_r($html);
    $proxy_reg = '/<br\/>(.*?)<br>/s';
    preg_match($proxy_reg, $html, $proxy_list);
    //print_r($proxy_list);
    $p = explode('<BR>', $proxy_list[1]);
    //print_r($p);
    
    foreach ($p as $li ) {
        $proxy_info = explode(':', $li);
        $proxy_infos[] = array(
            'proxy' => trim( strip_tags($proxy_info[0]) ),
            'port' =>  trim( strip_tags($proxy_info[1]) ),
        );
    }
    //print_r($proxy_infos);
    return $proxy_infos;
}
// function get_proxy6() {
//     $proxy_infos = get_proxy7();
//     //$proxy_infos = array();
//     for($i=1;$i<=10;$i++){
//         $html = get_html('www.httpsdaili.com/?page='.$i);
//         file_put_contents(6, $html);
//         $html = iconv("GBK", "UTF-8", $html);
//         //print_r($html);
//         $proxy_reg = '/<div id="list"(.*?)<\/div>/s';
//         preg_match($proxy_reg, $html, $proxy_list);
//         $main_reg = '/<tbody>(.*?)<\/tbody>/s';
//         preg_match($main_reg, $proxy_list[0], $main);
//         $proxy_list_reg = '/<tr class="odd">(.*?)<\/tr>/s';
//         //print_r($proxy_list);
//         preg_match_all($proxy_list_reg, $main[0], $proxy_li, PREG_SET_ORDER);
//         //print_r($proxy_li);
        
//         foreach ($proxy_li as $li ) {
//             //print_r($li);
//             $proxy_li_reg = '/<td *(.*?)<\/td>/s';
//             preg_match_all($proxy_li_reg, $li[1], $proxy_info, PREG_SET_ORDER);
//             //print_r($proxy_info);
//             $proxy_infos[] = array(
//                 'proxy' => trim(strip_tags($proxy_info[0][0])),
//                 'port' =>trim(strip_tags($proxy_info[1][0])),
//                 'type' => trim(strip_tags($proxy_info[3][0])),
//                 'detail' => trim(strip_tags($proxy_info[4][0])),
//             );
//         }
//     }
    
//    // print_r($proxy_infos);
//     return $proxy_infos;
// }
//获取代理7
function get_proxy7() {
    //$proxy_infos = array();
    $proxy_infos = get_proxy8();
    $html = get_html('http://www.xdaili.cn/ipagent//freeip/getFreeIps?page=1&rows=10');
    file_put_contents(7, $html);
    $html = json_decode($html, true);
    //print_r($html);
    
    foreach ($html['RESULT']['rows'] as $li ) {

        //print_r($li);
        $proxy_infos[] = array(
            'proxy'  => $li['ip'],
            'port'   => $li['port'],
            'type'   => $li['type'],
            'detail' => $li['position'],
        );
    }
   //print_r($proxy_infos);
    return $proxy_infos;
}

//获取代理8
function get_proxy8() {
    $proxy_infos = array();
    //$proxy_infos = get_proxy5();
    $html = get_html('http://api.pcdaili.com/?num=500&area=&addres=&port=&port_ex=&ipstart=&ipstart_ex=&carrier=0&an_ha=1&sp1=1&sp2=1&protocol=1&method=1&sort=2&format=json&sep=1&custom_sep=');
    file_put_contents(8, $html);
    $html = json_decode($html, true);
    //print_r($html);
    
    foreach ($html['data']['proxy_list'] as $li ) {

        //print_r($li);
        $proxy = explode(':', $li);
        //print_r($proxy);
        $proxy_infos[] = array(
            'proxy'  => $proxy[0],
            'port'   => $proxy[1],
            'type'   => 'http',
            'detail' => '',
        );
    }
    //print_r($proxy_infos);
    return $proxy_infos;
}