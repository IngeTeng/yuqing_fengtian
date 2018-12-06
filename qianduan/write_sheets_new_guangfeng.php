<?php
require_once('adminv/inc_dbconn.php');
require_once('Classes/PHPExcel.php');
set_time_limit(400);//设置运行时间，防止数据多时运行时间过长而终止，2017/02/11
ini_set('memory_limit', '1024M');//修改php的运行内存限制,因为app层面导出数据出现内存不足的问题，2017/02/11

//ini_set('display_errors', 1);            //错误信息
//ini_set('display_startup_errors', 1);    //php启动错误信息
//error_reporting(-1);                    //打印出所有的 错误信息

$json = $_POST['data'];
$data = json_decode($json, true);
var_dump($data);
$start_date = $data['start_date'];
$end_date = $data['end_date'];
$type = $data['type'];
$type1 = $data['type1']; // type1 ==1 日报 type1 == 7 周报

$date = date('Y-m-d', $end_date);


$data_type = array('news', 'video', 'blog', 'weibo', 'weixin', 'zhidao', 'app', 'bbs');
$type_name = array('新闻', '视频', '博客', '微博', '微信', '知道', 'app', '论坛');

$property = array(
    '1' => '正',
    '0' => '中',
    '2' => '负',
    '3' => '不良'
);

$article_is_repost = array(
    '0' => '原创',
    '1' => '转载'
);

$a_type = array(
    '0' => '非经销商发稿',
    '1' => '经销商发稿',
    '2' => '竞品攻击',
    '3' => '非车主投诉',
    '4' => '车主投诉'
);

$count = 1;
$data = array();

switch ($type1) {
    case '1': //日报

        switch ($type) {
            case '40': // 全品牌日报
            case '50': // 凯美瑞日报
            case '51': //凯美瑞日报-迈腾
            case '52'://凯美瑞日报-雅阁
            case '53'://凯美瑞日报-天籁
            case '54'://凯美瑞日报-帕萨特
            case '55'://凯美瑞日报-君威


                $file_name = iconv('utf-8', 'gb2312', date('Y-m-d-H-i', $start_date) . '到' . date('Y-m-d-H-i', $end_date) . 'excel文件');
                echo $file_name;


                /* @实例化 */
                $obpe = new PHPExcel();
                $time = time();
                $time_str = date('Y/m/d H:i', $time);
                /* @func 设置文档基本属性 */
                $obpe_pro = $obpe->getProperties();
                $obpe_pro->setCreator('WiipuXian')//设置创建者
                ->setLastModifiedBy($time_str)//设置时间
                ->setTitle('data')//设置标题
                ->setSubject('beizhu')//设置备注
                ->setDescription('miaoshu')//设置描述
                ->setKeywords('keyword')//设置关键字 | 标记
                ->setCategory('catagory');//设置类别
                $obpe->getDefaultStyle()->getFont()->setName('宋体');
                $obpe->getDefaultStyle()->getFont()->setSize(10);

                $obpe->createSheet();
                $obpe->setactivesheetindex((int)0);
                $objActSheet = $obpe->getActiveSheet();

                switch ($type) {
                    case '40':
                        $objActSheet->setTitle('全品牌日报');
                        break;
                    case '50':
                        $objActSheet->setTitle('凯美瑞日报');
                        break;
                    case '51':
                        $objActSheet->setTitle('凯美瑞日报-迈腾');
                        break;
                    case '52':
                        $objActSheet->setTitle('凯美瑞日报-雅阁');
                        break;
                    case '53':
                        $objActSheet->setTitle('凯美瑞日报-天籁');
                        break;
                    case '54':
                        $objActSheet->setTitle('凯美瑞日报-帕萨特');
                        break;
                    case '55':
                        $objActSheet->setTitle('凯美瑞日报-君威');
                        break;

                }

                set_excel($objActSheet);//设置当前表的属性


                //  查询数据
                for ($y = 0; $y < count($data_type); $y++) {


                    $sql = "select * from
user_keywords, 
user_category,
keyword,
" . $data_type[$y] . "_article,
" . $data_type[$y] . "_key
where
user_category.c_id in (" . $type . ")
and user_keywords.k_id=keyword.k_id
and user_category.c_id= user_keywords.c_id
and " . $data_type[$y] . "_key.article_id=" . $data_type[$y] . "_article.article_id 
and  " . $data_type[$y] . "_article.article_pubtime<" . $end_date . " 
and " . $data_type[$y] . "_article.article_pubtime>" . $start_date . "
and " . $data_type[$y] . "_key.uk_id=user_keywords.uk_id
and " . $data_type[$y] . "_key.status=1
and " . $data_type[$y] . "_key.article_property is not null
 group by " . $data_type[$y] . "_key.article_id," . $data_type[$y] . "_key.uk_id;";

                    echo $sql;
                    $res = mysql_query($sql);


                    while ($row1 = mysql_fetch_array($res)) {
                        $data[$count]['count'] = $count;
                        $data[$count]['keyword'] = $row1['keyword'];
                        if (preg_match('/微博/s', $row1['media'])) {
                            $data[$count]['media_class'] = '微博'; //媒体类型
                        } else {
                            $data[$count]['media_class'] = $type_name[$y]; //媒体类型
                        }

                        $data[$count]['media'] = $row1['media'];
                        $data[$count]['report_date'] = date('Y-m-d H:i', $row1['article_pubtime']);
                        $data[$count]['article_title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_title']);

                        switch ($data_type[$y]) {
                            case 'weibo':
                                $data[$count]['article_summary'] = '';
                                break;
                            case 'video':
                            case 'weixin':
                            case 'zhidao':
                                $data[$count]['article_summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_summary']);
                                break;
                            default:
                                $data[$count]['article_summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_content']);
                        }


                        $data[$count]['article_url'] = $row1['article_url'];
                        $data[$count]['article_property'] = $property[$row1['article_property']];
                        $data[$count]['article_channel'] = $row1['article_channel'];
                        $data[$count]['article_is_repost'] = $article_is_repost[$row1['article_is_repost']];
                        $data[$count]['a_type'] = $a_type[$row1['a_type']];
                        $count++;

                    }

                }


                $line = 2;  //数据起始行
                for ($j = 1; $j < $count; $j++) {
                    write_row($objActSheet, $line, $data[$j]);  // 将数据写入excel
                    $line++;
                }

                switch ($type) {
                    case '40':
                        $filename = iconv('utf-8', 'gb2312', '全品牌日报');
                        break;
                    case '50':
                        $filename = iconv('utf-8', 'gb2312', '凯美瑞日报');
                        break;
                    case '51':
                        $filename = iconv('utf-8', 'gb2312', '凯美瑞日报-迈腾');
                        break;
                    case '52':
                        $filename = iconv('utf-8', 'gb2312', '凯美瑞日报-雅阁');
                        break;
                    case '53':
                        $filename = iconv('utf-8', 'gb2312', '凯美瑞日报-天籁');
                        break;
                    case '54':
                        $filename = iconv('utf-8', 'gb2312', '凯美瑞日报-帕萨特');
                        break;
                    case '55':
                        $filename = iconv('utf-8', 'gb2312', '凯美瑞日报-君威');
                        break;
                }


//写入类容
                $obwrite = PHPExcel_IOFactory::createWriter($obpe, 'Excel5');
//保存文件
                if (!is_dir('auto_files/' . $date)) {
                    mkdir('auto_files/' . $date, 0777, true);
                }
                $filename = 'auto_files/' . $date . "/" . $filename . $file_name . ".xls";
                if (!file_exists($filename)) {
                    $obwrite->save($filename);
                }
                break;

            case '335,412,413': //'335,412,413' CHR日报
            case '336,414':    // 336,414 CHR日报-奕泽
            case '415,416,338': // 415,416,338 CHR日报-XRV
            case '337': // 337 CHR日报-缤智
            case '417,418,419':  // 417,418,419 CHR日报-探歌
            case '420':    // 420 CHR日报-领克

                $file_name = iconv('utf-8', 'gb2312', date('Y-m-d-H-i', $start_date) . '到' . date('Y-m-d-H-i', $end_date) . 'excel文件');
                echo $file_name;


                /* @实例化 */
                $obpe = new PHPExcel();
                $time = time();
                $time_str = date('Y/m/d H:i', $time);
                /* @func 设置文档基本属性 */
                $obpe_pro = $obpe->getProperties();
                $obpe_pro->setCreator('WiipuXian')//设置创建者
                ->setLastModifiedBy($time_str)//设置时间
                ->setTitle('data')//设置标题
                ->setSubject('beizhu')//设置备注
                ->setDescription('miaoshu')//设置描述
                ->setKeywords('keyword')//设置关键字 | 标记
                ->setCategory('catagory');//设置类别
                $obpe->getDefaultStyle()->getFont()->setName('宋体');
                $obpe->getDefaultStyle()->getFont()->setSize(10);

                $obpe->createSheet();
                $obpe->setactivesheetindex((int)0);
                $objActSheet = $obpe->getActiveSheet();

                switch ($type) {
                    case '335,412,413':
                        $objActSheet->setTitle('CHR日报');
                        break;
                    case '336,414':
                        $objActSheet->setTitle('CHR日报-奕泽');
                        break;
                    case '415,416,338':
                        $objActSheet->setTitle('CHR日报-XRV');
                        break;
                    case '337':
                        $objActSheet->setTitle('CHR日报-缤智');
                        break;
                    case '417,418,419':
                        $objActSheet->setTitle('CHR日报-探歌');
                        break;
                    case '420':
                        $objActSheet->setTitle('CHR日报-领克');
                        break;

                }

                set_excel($objActSheet);//设置当前表的属性


                //  查询数据
                for ($y = 0; $y < count($data_type); $y++) {


                    $sql = "select * from
user_keywords, 
user_category,
keyword,
" . $data_type[$y] . "_article,
" . $data_type[$y] . "_key
where
keyword.k_id in (" . $type . ")
and user_keywords.k_id=keyword.k_id
and user_category.c_id= user_keywords.c_id
and " . $data_type[$y] . "_key.article_id=" . $data_type[$y] . "_article.article_id 
and  " . $data_type[$y] . "_article.article_pubtime<" . $end_date . " 
and " . $data_type[$y] . "_article.article_pubtime>" . $start_date . "
and " . $data_type[$y] . "_key.uk_id=user_keywords.uk_id
 and " . $data_type[$y] . "_key.status=1
and " . $data_type[$y] . "_key.article_property is not null

 group by " . $data_type[$y] . "_key.article_id," . $data_type[$y] . "_key.uk_id;;";

                    echo $sql;
                    $res = mysql_query($sql);


                    while ($row1 = mysql_fetch_array($res)) {
                        $data[$count]['count'] = $count;
                        $data[$count]['keyword'] = $row1['keyword'];
                        if (preg_match('/微博/s', $row1['media'])) {
                            $data[$count]['media_class'] = '微博'; //媒体类型
                        } else {
                            $data[$count]['media_class'] = $type_name[$y]; //媒体类型
                        }

                        $data[$count]['media'] = $row1['media'];
                        $data[$count]['report_date'] = date('Y-m-d H:i', $row1['article_pubtime']);
                        $data[$count]['article_title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_title']);
                        switch ($data_type[$y]) {
                            case 'weibo':
                                $data[$count]['article_summary'] = '';
                                break;
                            case 'video':
                            case 'weixin':
                            case 'zhidao':
                                $data[$count]['article_summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_summary']);
                                break;
                            default:
                                $data[$count]['article_summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_content']);
                        }


                        $data[$count]['article_url'] = $row1['article_url'];
                        $data[$count]['article_property'] = $property[$row1['article_property']];
                        $data[$count]['article_channel'] = $row1['article_channel'];
                        $data[$count]['article_is_repost'] = $article_is_repost[$row1['article_is_repost']];
                        $data[$count]['a_type'] = $a_type[$row1['a_type']];
                        $count++;

                    }

                }


                $line = 2;  //数据起始行
                for ($j = 1; $j < $count; $j++) {
                    write_row($objActSheet, $line, $data[$j]);  // 将数据写入excel
                    $line++;
                }

                switch ($type) {
                    case '335,412,413':
                        $filename = iconv('utf-8', 'gb2312', 'CHR日报');
                        break;
                    case '336,414':
                        $filename = iconv('utf-8', 'gb2312', 'CHR日报-奕泽');
                        break;
                    case '415,416,338':
                        $filename = iconv('utf-8', 'gb2312', 'CHR日报-XRV');
                        break;
                    case '337':
                        $filename = iconv('utf-8', 'gb2312', 'CHR日报-缤智');
                        break;
                    case '417,418,419':
                        $filename = iconv('utf-8', 'gb2312', 'CHR日报-探歌');
                        break;
                    case '420':
                        $filename = iconv('utf-8', 'gb2312', 'CHR日报-领克');
                        break;
                }


//写入类容
                $obwrite = PHPExcel_IOFactory::createWriter($obpe, 'Excel5');
//保存文件
                if (!is_dir('auto_files/' . $date)) {
                    mkdir('auto_files/' . $date, 0777, true);
                }
                $filename = 'auto_files/' . $date . "/" . $filename . $file_name . ".xls";
                if (!file_exists($filename)) {
                    $obwrite->save($filename);
                }


                break;


        }
        break;

    case '7': //周报

        switch ($type) {

            case '232': //广汽丰田周报
            case '251':    // 凯美瑞周报
            case '295': // 雷凌周报
            case '311': // 致炫周报
            case '322':  // 致享周报
            case '271':    // 汉兰达周报

            case '232,251,271,335,295,311,322': // 本品月报

//            case '252': // 临时增加雅阁关键字
//            case '': //

//                break;

                $file_name = iconv('utf-8', 'gb2312', date('Y-m-d-H-i', $start_date) . '到' . date('Y-m-d-H-i', $end_date) . 'excel文件');
                echo $file_name;


                /* @实例化 */
                $obpe = new PHPExcel();
                $time = time();
                $time_str = date('Y/m/d H:i', $time);
                /* @func 设置文档基本属性 */
                $obpe_pro = $obpe->getProperties();
                $obpe_pro->setCreator('WiipuXian')//设置创建者
                ->setLastModifiedBy($time_str)//设置时间
                ->setTitle('data')//设置标题
                ->setSubject('beizhu')//设置备注
                ->setDescription('miaoshu')//设置描述
                ->setKeywords('keyword')//设置关键字 | 标记
                ->setCategory('catagory');//设置类别
                $obpe->getDefaultStyle()->getFont()->setName('宋体');
                $obpe->getDefaultStyle()->getFont()->setSize(10);

                $obpe->createSheet();
                $obpe->setactivesheetindex((int)0);
                $objActSheet = $obpe->getActiveSheet();

                switch ($type) {
                    case '232':
                        $objActSheet->setTitle('广汽丰田周报');
                        break;
                    case '251':
                        $objActSheet->setTitle('凯美瑞周报');
                        break;
                    case '295':
                        $objActSheet->setTitle('雷凌周报');
                        break;
                    case '311':
                        $objActSheet->setTitle('致炫周报');
                        break;
                    case '322':
                        $objActSheet->setTitle('致享周报');
                        break;
                    case '271':
                        $objActSheet->setTitle('汉兰达周报');
                        break;

                    case '232,251,271,335,295,311,322': // 本品月报
                        $objActSheet->setTitle('本品月报');
                        break;

//                    case '252':
//                        $objActSheet->setTitle('雅阁关键字');
//                        break;

                }

                set_excel($objActSheet);//设置当前表的属性


                //  查询数据
                for ($y = 0; $y < count($data_type); $y++) {


                    $sql = "select * from
user_keywords, 
user_category,
keyword,
" . $data_type[$y] . "_article,
" . $data_type[$y] . "_key
where
keyword.k_id in (" . $type . ")
and user_keywords.k_id=keyword.k_id
and user_category.c_id= user_keywords.c_id
and " . $data_type[$y] . "_key.article_id=" . $data_type[$y] . "_article.article_id 
and  " . $data_type[$y] . "_article.article_pubtime<" . $end_date . " 
and " . $data_type[$y] . "_article.article_pubtime>" . $start_date . "
and " . $data_type[$y] . "_key.uk_id=user_keywords.uk_id 
and " . $data_type[$y] . "_key.status=1
and " . $data_type[$y] . "_key.article_property is not null
group by " . $data_type[$y] . "_key.article_id," . $data_type[$y] . "_key.uk_id;;";

                    echo $sql;
                    $res = mysql_query($sql);


                    while ($row1 = mysql_fetch_array($res)) {
                        $data[$count]['count'] = $count;
                        $data[$count]['keyword'] = $row1['keyword'];
                        if (preg_match('/微博/s', $row1['media'])) {
                            $data[$count]['media_class'] = '微博'; //媒体类型
                        } else {
                            $data[$count]['media_class'] = $type_name[$y]; //媒体类型
                        }

                        $data[$count]['media'] = $row1['media'];
                        $data[$count]['report_date'] = date('Y-m-d H:i', $row1['article_pubtime']);
                        $data[$count]['article_title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_title']);

                        switch ($data_type[$y]) {
                            case 'weibo':
                                $data[$count]['article_summary'] = '';
                                break;
                            case 'video':
                            case 'weixin':
                            case 'zhidao':
                                $data[$count]['article_summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_summary']);
                                break;
                            default:
                                $data[$count]['article_summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_content']);
                        }

                        $data[$count]['article_url'] = $row1['article_url'];
                        $data[$count]['article_property'] = $property[$row1['article_property']];
                        $data[$count]['article_channel'] = $row1['article_channel'];
                        $data[$count]['article_is_repost'] = $article_is_repost[$row1['article_is_repost']];
                        $data[$count]['a_type'] = $a_type[$row1['a_type']];
                        $count++;

                    }

                }


                $line = 2;  //数据起始行
                for ($j = 1; $j < $count; $j++) {
                    write_row($objActSheet, $line, $data[$j]);  // 将数据写入excel
                    $line++;
                }

                switch ($type) {
                    case '232':
                        $filename = iconv('utf-8', 'gb2312', '广汽丰田周报');
                        break;
                    case '251':
                        $filename = iconv('utf-8', 'gb2312', '凯美瑞周报');
                        break;
                    case '295':
                        $filename = iconv('utf-8', 'gb2312', '雷凌周报');
                        break;
                    case '311':
                        $filename = iconv('utf-8', 'gb2312', '致炫周报');
                        break;
                    case '322':
                        $filename = iconv('utf-8', 'gb2312', '致享周报');
                        break;
                    case '271':
                        $filename = iconv('utf-8', 'gb2312', '汉兰达周报');
                        break;

                    case '252':
                        $filename = iconv('utf-8', 'gb2312', '雅阁关键词');
                        break;
                    case '232,251,271,335,295,311,322': // 本品月报
                        $filename = iconv('utf-8', 'gb2312', '本品月报');
                        break;


                }


//写入类容
                $obwrite = PHPExcel_IOFactory::createWriter($obpe, 'Excel5');
//保存文件
                if (!is_dir('auto_files/' . $date)) {
                    mkdir('auto_files/' . $date, 0777, true);
                }
                $filename = 'auto_files/' . $date . "/" . $filename . $file_name . ".xls";
                if (!file_exists($filename)) {
                    $obwrite->save($filename);
                }
                break;
            case '235,233,234,237,236':  // 竞品数据-广汽丰田  ok
//            case '256,252,253,255,254':  //竞品数据-凯美瑞
            case '256,252,253,255,254,260,259':  //竞品数据-凯美瑞
            case '273,275,279,285,278':  //竞品数据-汉兰达
            case '336,416,337,417,420':  //竞品数据-C-HR
            case '296,297,298,299,300':  //竞品数据-雷凌
            case "312,313,314,317,320":  // 竞品数据-致炫
            case '326,328,323,324,325':  // 竞品数据-致享


                $file_name = iconv('utf-8', 'gb2312', date('Y-m-d-H-i', $start_date) . '到' . date('Y-m-d-H-i', $end_date) . 'excel文件');
                echo $file_name;


                /* @实例化 */
                $obpe = new PHPExcel();
                $time = time();
                $time_str = date('Y/m/d H:i', $time);
                /* @func 设置文档基本属性 */
                $obpe_pro = $obpe->getProperties();
                $obpe_pro->setCreator('WiipuXian')//设置创建者
                ->setLastModifiedBy($time_str)//设置时间
                ->setTitle('data')//设置标题
                ->setSubject('beizhu')//设置备注
                ->setDescription('miaoshu')//设置描述
                ->setKeywords('keyword')//设置关键字 | 标记
                ->setCategory('catagory');//设置类别
                $obpe->getDefaultStyle()->getFont()->setName('宋体');
                $obpe->getDefaultStyle()->getFont()->setSize(10);

                $obpe->createSheet();
                $obpe->setactivesheetindex((int)0);
                $objActSheet = $obpe->getActiveSheet();

                switch ($type) {
                    case '235,233,234,237,236':
                        $objActSheet->setTitle('竞品数据-广汽丰田周报');
                        break;
                    case '256,252,253,255,254,260,259':
                        $objActSheet->setTitle('竞品数据-凯美瑞周报');
                        break;
                    case '273,275,279,285,278':
                        $objActSheet->setTitle('竞品数据-汉兰达周报');
                        break;
                    case '336,416,337,417,420':
                        $objActSheet->setTitle('竞品数据-C-HR周报');
                        break;
                    case '296,297,298,299,300':
                        $objActSheet->setTitle('竞品数据-雷凌周报');
                        break;
                    case '312,313,314,317,320':
                        $objActSheet->setTitle('竞品数据-致炫周报');
                        break;
                    case '326,328,323,324,325':
                        $objActSheet->setTitle('竞品数据-致享周报');
                        break;
//                    case '252':
//                        $objActSheet->setTitle('雅阁关键字');
//                        break;

                }

                set_excel_jingpin($objActSheet);//设置当前表的属性  竞品


                //  查询数据
                for ($y = 0; $y < count($data_type); $y++) {


                    $sql = "select * from
user_keywords, 
user_category,
keyword,
" . $data_type[$y] . "_article,
" . $data_type[$y] . "_key
where
keyword.k_id in (" . $type . ")
and user_keywords.k_id=keyword.k_id
and user_category.c_id= user_keywords.c_id
and " . $data_type[$y] . "_key.article_id=" . $data_type[$y] . "_article.article_id 
and  " . $data_type[$y] . "_article.article_pubtime<" . $end_date . " 
and " . $data_type[$y] . "_article.article_pubtime>" . $start_date . "
and " . $data_type[$y] . "_key.uk_id=user_keywords.uk_id
 and " . $data_type[$y] . "_key.status=1
and " . $data_type[$y] . "_key.article_property is not null
 group by " . $data_type[$y] . "_key.article_id," . $data_type[$y] . "_key.uk_id;;";

                    echo $sql;
                    $res = mysql_query($sql);


                    while ($row1 = mysql_fetch_array($res)) {
                        $data[$count]['count'] = $count;  //序号
                        switch ($type) {
                            case '235,233,234,237,236':
                                $data[$count]['me'] = '广汽丰田';
                                break;
                            case '256,252,253,255,254,260,259': //只有这个地方改了
//                            case '256,252,253,255,254':
                                $data[$count]['me'] = '凯美瑞';
                                break;
                            case '273,275,279,285,278':
                                $data[$count]['me'] = '汉兰达';
                                break;
                            case '336,416,337,417,420':
                                $data[$count]['me'] = 'C-HR';
                                break;
                            case '296,297,298,299,300':
                                $data[$count]['me'] = '雷凌';
                                break;
                            case '312,313,314,317,320':
                                $data[$count]['me'] = '致炫';
                                break;
                            case '326,328,323,324,325':
                                $data[$count]['me'] = '致享';
                                break;
                        }

                        $data[$count]['keyword'] = $row1['keyword']; //竞品品牌
                        if (preg_match('/微博/s', $row1['media'])) {
                            $data[$count]['media_class'] = '微博'; //媒体类型
                        } else {
                            $data[$count]['media_class'] = $type_name[$y]; //媒体类型
                        }

                        $data[$count]['media'] = $row1['media'];  //媒体名称

                        $data[$count]['report_date'] = date('Y-m-d H:i', $row1['article_pubtime']); // 报道日期
                        $data[$count]['article_title'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_title']); //标题

                        switch ($data_type[$y]) {
                            case 'weibo':
                                $data[$count]['article_summary'] = '';
                                break;
                            case 'video':
                            case 'weixin':
                            case 'zhidao':
                                $data[$count]['article_summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_summary']);
                                break;
                            default:
                                $data[$count]['article_summary'] = preg_replace('/^(&nbsp;|\s)*|(\d小时前-)|(\d小时前)|(&nbsp;|\s)*$/', '', $row1['article_content']);
                        }

                        $data[$count]['article_url'] = $row1['article_url'];  // 链接
                        $data[$count]['article_property'] = $property[$row1['article_property']];  //调性
                        $data[$count]['article_author'] = $row1['article_author']; //作者
                        $data[$count]['article_is_repost'] = $article_is_repost[$row1['article_is_repost']];  //原发/转载
                        $data[$count]['a_type'] = $a_type[$row1['a_type']]; //文章类型


                        $count++;

                    }

                }


                $line = 2;  //数据起始行
                for ($j = 1; $j < $count; $j++) {
                    write_row($objActSheet, $line, $data[$j]);  // 将数据写入excel
                    $line++;
                }

                switch ($type) {
                    case '235,233,234,237,236':
                        $filename = iconv('utf-8', 'gb2312', '竞品数据-广汽丰田周报');
                        break;
                    case '256,252,253,255,254,260,259':
//                    case '256,252,253,255,254':

                        $filename = iconv('utf-8', 'gb2312', '竞品数据-凯美瑞周报');
                        break;
                    case '273,275,279,285,278':
                        $filename = iconv('utf-8', 'gb2312', '竞品数据-汉兰达周报');
                        break;
                    case '336,416,337,417,420':
                        $filename = iconv('utf-8', 'gb2312', '竞品数据-C-HR周报');
                        break;
                    case '296,297,298,299,300':
                        $filename = iconv('utf-8', 'gb2312', '竞品数据-雷凌周报');
                        break;
                    case '312,313,314,317,320':
                        $filename = iconv('utf-8', 'gb2312', '竞品数据-致炫周报');
                        break;
                    case '326,328,323,324,325':
                        $filename = iconv('utf-8', 'gb2312', '竞品数据-致享周报');
                        break;

                }


//写入类容
                $obwrite = PHPExcel_IOFactory::createWriter($obpe, 'Excel5');
//保存文件
                if (!is_dir('auto_files/' . $date)) {
                    mkdir('auto_files/' . $date, 0777, true);
                }
                $filename = 'auto_files/' . $date . "/" . $filename . $file_name . ".xls";
                if (!file_exists($filename)) {
                    $obwrite->save($filename);
                }


                break;


        }


        break;

}


function excel_data($str)
{
    while (strncmp($str, "=", 1) == 0) {
        $str = substr($str, 1);
    }
    return $str;
}

//写一行
function write_row($objActSheet, $row, $data)
{
    global $objActSheet;
    $arr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
        'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $len = count($data);
    $i = 0;
    foreach ($data as $key => $value) {
        if ($i == $len)
            break;
        if (empty($value)) {
            $i++;
            continue;
        }
//        $objActSheet->setcellvalue($arr[$i].$row, iconv('gbk','utf-8',trim(excel_data($value)) ));
        $objActSheet->setcellvalue($arr[$i] . $row, trim(excel_data($value)));
        $i++;
    }
//    print_r($data);
}

function set_excel_jingpin($objActSheet)  // 竞品格式标题
{
    $excel_title = array('序号', '本品', '竞品品牌', '媒体类型', '媒体名称', '报道日期', '标题', '摘要', '链接', '调性', '作者', '原发/转载', '文章类型');
    $objActSheet->getDefaultRowDimension()->setRowHeight(15);                                                   //行高15
    $objActSheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_style_Alignment::VERTICAL_CENTER);
    $objActSheet->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objActSheet->getStyle('A1:L1')->getFont()->setBold(true);  //标题用粗体  A1 到 L1
//    $objActSheet->getStyle('E:F')->getAlignment()->setWrapText(true);
//    $objActSheet->getStyle('E:F')->getAlignment()->setWrapText(true);
    //$objActSheet->getStyle('A1:K2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $objActSheet->getStyle('A1:K2')->getFill()->getStartColor()->setARGB('EEEEEEEE');//背景浅灰
    //$objActSheet->getstyle('A1:K2')->getBorders()->getTop()->setBorderstyle(PHPExcel_style_Border::BORDER_THIN);
    $objActSheet->getstyle('A1:M1')->getBorders()->getAllBorders()->setBorderstyle(PHPExcel_style_Border::BORDER_THIN);
    $width_array = array('A' => 7.25, 'B' => 10, 'C' => 12, 'D' => 12, 'E' => 12, 'F' => 12, 'G' => 50, 'H' => 50, 'I' => 40, 'J' => 12, 'K' => 12, 'L' => 13, 'M' => 15);//列宽度
    foreach ($width_array as $k => $v) {
        $objActSheet->getColumnDimension($k)->setWidth($v);
    }
    //输出标题
//    $objActSheet->mergeCells('B1:C1');//合并单元格
//    $objActSheet->mergeCells('G1:H1');
//    $objActSheet->mergeCells('A1:A2');
//    $objActSheet->mergeCells('D1:D2');
//    $objActSheet->mergeCells('E1:E2');
//    $objActSheet->mergeCells('F1:F2');
//    $objActSheet->mergeCells('I1:I2');
//    $objActSheet->mergeCells('J1:J2');
//    $objActSheet->mergeCells('K1:K2');
//    write_row($objActSheet, 1, array('序号', '发布媒体','','发布日期','稿件标题','发布链接','关注度','', '责任单位','舆情处理沟通情况', '媒体影响力评估'));
    write_row($objActSheet, 1, $excel_title);
}


function set_excel($objActSheet)
{
    $excel_title = array('序号', '车型', '媒体类型', '媒体名称', '报道日期', '标题', '摘要', '链接', '调性', '所在频道', '原发/转载', '文章类型');
    $objActSheet->getDefaultRowDimension()->setRowHeight(15);                                                   //行高15
    $objActSheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_style_Alignment::VERTICAL_CENTER);
    $objActSheet->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objActSheet->getStyle('A1:L1')->getFont()->setBold(true);  //标题用粗体  A1 到 L1
//    $objActSheet->getStyle('E:F')->getAlignment()->setWrapText(true);
//    $objActSheet->getStyle('E:F')->getAlignment()->setWrapText(true);
    //$objActSheet->getStyle('A1:K2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $objActSheet->getStyle('A1:K2')->getFill()->getStartColor()->setARGB('EEEEEEEE');//背景浅灰
    //$objActSheet->getstyle('A1:K2')->getBorders()->getTop()->setBorderstyle(PHPExcel_style_Border::BORDER_THIN);
    $objActSheet->getstyle('A1:L1')->getBorders()->getAllBorders()->setBorderstyle(PHPExcel_style_Border::BORDER_THIN);
    $width_array = array('A' => 7.25, 'B' => 14.5, 'C' => 11, 'D' => 12, 'E' => 17, 'F' => 50, 'G' => 50, 'H' => 50, 'I' => 12, 'J' => 12, 'K' => 12, 'L' => 13);//列宽度
    foreach ($width_array as $k => $v) {
        $objActSheet->getColumnDimension($k)->setWidth($v);
    }
    //输出标题
//    $objActSheet->mergeCells('B1:C1');//合并单元格
//    $objActSheet->mergeCells('G1:H1');
//    $objActSheet->mergeCells('A1:A2');
//    $objActSheet->mergeCells('D1:D2');
//    $objActSheet->mergeCells('E1:E2');
//    $objActSheet->mergeCells('F1:F2');
//    $objActSheet->mergeCells('I1:I2');
//    $objActSheet->mergeCells('J1:J2');
//    $objActSheet->mergeCells('K1:K2');
//    write_row($objActSheet, 1, array('序号', '发布媒体','','发布日期','稿件标题','发布链接','关注度','', '责任单位','舆情处理沟通情况', '媒体影响力评估'));
    write_row($objActSheet, 1, $excel_title);
}


class word
{
    function start()
    {
        ob_start();
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
		xmlns:w="urn:schemas-microsoft-com:office:word"
		xmlns="http://www.w3.org/TR/REC-html40">';
    }

    function save($path)
    {

        echo "</html>";
        $data = ob_get_contents();
        ob_end_clean();

        $this->wirtefile($path, $data);
    }

    function wirtefile($fn, $data)
    {
        $fp = fopen($fn, "wb");
        fwrite($fp, $data);
        fclose($fp);
    }
}


?>