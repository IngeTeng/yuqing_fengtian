<?php
/**
 * Created by PhpStorm.
 * User: 张鑫
 * Date: 2018/10/22
 * Time: 19:57
 */
function html_get($url, $cookie="", $referer="")
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    if ($cookie != "")
    {
        $coo = "Cookie: " . $cookie;
        $headers[] = $coo;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($referer != "")
    {
        curl_setopt($ch,CURLOPT_REFERER,$referer);
    }
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
function get_array($url)
{
    $page = html_get($url);
    if ($page) {
        $encoding = mb_detect_encoding($page);
        if ($encoding != "GBK") {
            $page = iconv($encoding, "gbk//TRANSLIT", $page);
            file_put_contents('result_url2_log', var_export($page, true));
        }
        $sock = mdsLogin("127.0.0.1", 1708);
        if ($sock > 0) {
            $a = getMdsContent($sock, $url, $page, 29);
            file_put_contents('result_log', var_export($a, true));

        } else {
            file_put_contents('result_log', var_export(array(), true));
        }
        socket_close($sock);
        return $a;
    } else {
        return false;
    }

}


// 发送post请求
function send_post($url, $post_data) {
    $postdata = http_build_query($post_data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // 设置不输出到屏幕
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,20);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

// 获取的html,带模拟登陆
function get_html($url, $cookie='', $proxy='', $proxy_port='', $referer='', $gzip=false) {
    $ch = curl_init();
    // 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//允许页面跳转，获取重定向
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);      // 60秒超时
    if($gzip) curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // 编码格式

    if($cookie != '') {
        $coo = "Cookie:$cookie";
        $headers[] = $coo;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($referer != '') {
        curl_setopt($ch, CURLOPT_REFERER, $referer);
    }
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


// 获取keywords
function get_keywords($url) {
    // 任务中心请求初始化
    $task_request = array(
        '$url' => $url
    );

    // 向任务中心请求关键字
    $task_request['timestamp'] = time();
    $task_request['sign'] = Post2Sign::getSign($task_request, SECRET);
    $task_response = send_post(TASK_CENTER, $task_request);
    return $task_response;
}


