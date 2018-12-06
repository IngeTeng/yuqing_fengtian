<?php
/*
 * write_sheets_new_guangfeng.php定时启动脚本 周报
 *by wangming
 *
 */


// ini_set('display_errors',1);            //错误信息  
// ini_set('display_startup_errors',1);    //php启动错误信息  
// error_reporting(-1);                    //打印出所有的 错误信息  


$interval_time = 60 * 60 * 24 * 7;
$start_date = strtotime('2018-10-29 00:00'); //周一
//$end_date = strtotime('2018-10-16 23:59');   //周天
$end_date = strtotime('2018-11-04 23:59');   //周天

while (true) {
//    $arr = array('232,251,271,335,295,311,322');
//    $arr = array('235,233,234,237,236', '256,252,253,255,254',
//        '273,275,279,285,278', '336,416,337,417,420', '296,297,298,299,300', '312,313,314,317,320', '326,328,323,324,325');
    $arr = array('232', '251', '295', '311', '322', '271', '235,233,234,237,236', '256,252,253,255,254,260,259',
        '273,275,279,285,278', '336,416,337,417,420', '296,297,298,299,300', '312,313,314,317,320', '326,328,323,324,325');

//    $arr = array('256,252,253,255,254');
    // 232 广汽丰田
    // 251凯美瑞
    // 295 雷凌
    // 311 致炫
    // 322 致享
    // 271 汉兰达
    for ($type = 0; $type < count($arr); $type++) {

        $url = 'http://47.92.204.34/yuqing/write_sheets_new_guangfeng.php';   // 改成广丰的
        $data = array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'type' => $arr[$type],
            'type1' => '7',  // type1 是 7为 周报
        );
        $res = curlRequest($url, $data);
        print_r($res);
    }


    $start_date += $interval_time;
    $end_date += $interval_time;
    echo "okkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk";
    sleep($interval_time);
//    break;
}

function curlRequest($url, $data = '', $cookieFile = '', $connectTimeout = 30, $readTimeout = 30)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 1);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);
    //设置post数据
    $post_data['data'] = json_encode($data);
    //$post_data = $data;
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    //执行命令
    $response = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    return $response;
}

?>

