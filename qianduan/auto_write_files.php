<?php

// ini_set('display_errors',1);            //错误信息  
// ini_set('display_startup_errors',1);    //php启动错误信息  
// error_reporting(-1);                    //打印出所有的 错误信息  

$uk_id = array(
  '新能源日报' =>  '120,121,122,123,124,125,126,127',//新能源日报，对应产品分类c_id=10,11 
  '集团日报'   =>  '169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,
  189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,
  214,215,216,217,218,219,234', //集团日报，对应公司，c_id=20,21,22
  '乘用车日报' =>  '226,227,228,229,230,231,232,233'  //江淮乘用车日报，暂使用乘用车品牌+乘用车车型,c_id=24,25
  );
$interval_time = 60*60*24;
// $start_date = '2018-08-28 15:00';
// $end_date   = '2018-08-29 15:00';
// $date       = '2018-08-29';
while(true){
    $type = 0;//0 新能源 1 集团  2 乘用车
    foreach ($uk_id as $name => $ukid) {
        $url  = 'http://121.40.40.203/yuqing/write_sheets_new.php';
        $data = array(
            'filename'   => urlencode($name),
            'uk_id'      => $ukid,
            'start_date' => empty($start_date) ? date('Y-m-d 15:00', strtotime("-1 day")) : $start_date, 
            'end_date'   => empty($end_date) ? date('Y-m-d 15:00', time()) : $end_date,
            'date'       => empty($date) ? date('Y-m-d', time()) : $date,
            'type'       => $type,
          );
        $res  = curlRequest($url, $data);
        $type++;
        //print_r($res);
    }

    //break;
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

