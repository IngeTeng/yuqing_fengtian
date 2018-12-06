<?php

/**
 * @filename autoGetCookies.php  
 * @encoding UTF-8 
 * @author WiiPu  yjl 
 * @createtime 2017-02-12  11:12:30
 * @updatetime 
 * @version 1.0
 * @Description
 * 自动获取搜狗微信Cookie,添加到数据库,一天获取一次。
 * 
 */


set_time_limit(0);
$word = 'abcdefghijklmnopqrstuvwxyz';//用于随机关键字搜索
while(true){
    require_once('inc_dbconn.php');
    $num  = rand(0,25);
    //获取cookies
    $url = "http://weixin.sogou.com/weixin?query=$word[$num]";
    //echo $url;
    $headers = get_headers($url,1);//获取请求头
    print_r($headers);
    $cookies = $headers['Set-Cookie'];//获取请求头中的set-cookie数组
    $cookie = implode(";", $cookies);//将cookies转成一个字符串
    //echo $cookie;
    preg_match('/ABTEST=(.*);/iU', $cookie, $ABTEST); //匹配ABTEST值
    preg_match('/SNUID=(.*);/iU', $cookie, $SNUID); //匹配SNUID值
    preg_match('/SUID=(.*);/iU', $cookie, $SUID); //匹配ABTEST值
    // print_r($ABTEST);
    // print_r($SNUID);
    // print_r($SUID);
    $SUV = round(microtime(true) * 1000) . rand(000, 999);//生成SUV
    $cookie_res = $ABTEST[0]. $SNUID[0]. $SUID[0]. 'SUV='.$SUV. '; IPLOC=CN;';//合并各参数为最终cookie
    //print_r($cookie_res);
    //将cookie插入数据库中
    $sql="INSERT INTO weixin_cookies (cookie) VALUES('$cookie_res')";
    $f = fopen("getcookie.log", "a");
    if(mysql_query($sql))
    {   
        $str = date('y-m-d H:i:s', time())." Cookie添加成功: \r\n".$cookie."\r\n"
        .$cookie_res."\r\n";
    }else{
        $str = date('y-m-d H:i:s', time())." Cookie添加失败: \r\n".$cookie."\r\n"
        .$cookie_res."\r\n";
    }
    fwrite($f, $str);
    fclose($f);

    //break;
    sleep(60*60*5);//5小时更新一次
}

?>
