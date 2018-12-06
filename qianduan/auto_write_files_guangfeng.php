<?php
/*
 * write_sheets_new_guangfeng.php定时启动脚本
 *by wangming
 *
 */


// ini_set('display_errors',1);            //错误信息  
// ini_set('display_startup_errors',1);    //php启动错误信息  
// error_reporting(-1);                    //打印出所有的 错误信息  


$interval_time = 60 * 60 * 24;
$start_date = strtotime('2018-11-02 12:00');
$end_date = strtotime('2018-11-03 12:00');
// $date       = '2018-08-29';
while (true) {
//    $arr = array('40','50','51','52','53','54','55','335,412,413','336,414','415,416,338','337','417,418,419','420');
    $arr = array('40');
    //  40是生成全品牌日报
    //  50是生成凯美瑞日报
    //  51是生成凯美瑞日报-迈腾日报
    //  52是凯美瑞日报-雅阁
    //  53是凯美瑞日报-天籁
    //  54是凯美瑞日报-帕萨特
    //  55是凯美瑞日报-君威
    //'335,412,413' CHR日报
    // 336,414 CHR日报-奕泽
    // 415,416,338 CHR日报-XRV
    // 337 CHR日报-缤智
    // 417,418,419 CHR日报-探歌
    // 420 CHR日报-领克
    for ($type = 0; $type < count($arr); $type++) {

        $url = 'http://47.92.204.34/yuqing/write_sheets_new_guangfeng.php';   // 改成广丰的
        $data = array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'type' => $arr[$type],
            'type1' => '1',  // type1 是 1为 日报
        );
        $res = curlRequest($url, $data);
        print_r($res);
    }


    $start_date += $interval_time;
    $end_date += $interval_time;
    sleep($interval_time);
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

