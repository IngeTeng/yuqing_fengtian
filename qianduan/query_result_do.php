<?php
include_once('check_user.php');
set_time_limit(300);//设置运行时间，防止数据多时运行时间过长而终止
ini_set('memory_limit', '256M');//修改php的运行内存限制,因为app层面导出数据出现内存不足的问题
  // 获取的html,带模拟登陆
require_once('adminv/inc_dbconn.php');
class Use_class{
    static function get_user_category($user_id){
        $sql = "select * from user_category where user_id = $user_id order by c_id asc";

        $res = mysql_query($sql);
        return  mysql_fetch_array($res);
    }
//    static function get_user_category($user_id){
//        $sql = "select * from user_category where user_id = $user_id order by c_id asc";
//        echo $sql;
//        $res = mysql_query($sql);
//        $row = mysql_fetch_array($res);
//        return $row;
//    }

}
//$row=Use_class::get_user_category(1);
//
//var_dump($row);
//var_dump($row['category_name']);


?>
