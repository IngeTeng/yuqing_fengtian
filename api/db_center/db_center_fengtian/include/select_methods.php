<?php

/**
 * @filename select_methods.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @createtime 2016-8-9  21:55:25
 * @updatetime 2016-8-9  21:55:25
 * @version 1.0
 * @Description
 * 数据库查找操作
 * 
 */

// PDO连接数据库
class PDOFactory {
    public static function getPDO($log='', $db_host, $db_name, $username, $password, $options=array()) {
        $dsn = "mysql:dbname=". $db_name. ";host=". $db_host;

        $pdo_key = self::getKey($dsn, $username, $password, $options);
        if(!isset($GLOBALS['PDOS']) or !($GLOBALS['PDOS'][$pdo_key] instanceof PDO)) {
            try {
                $GLOBALS['PDOS'][$pdo_key] = new PDO($dsn, $username, $password, $options);
                $GLOBALS['PDOS'][$pdo_key]->query("SET NAMES utf8");
                $GLOBALS['PDOS'][$pdo_key]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                if(!empty($log)) {
                    $log->INFO("新建数据库连接成功, pdo_key = $pdo_key");
                }
            } catch (PDOException $ex) {
                if(!empty($log)) {
                    $log->WARN("数据库连接失败, ". $ex->getMessage());
                }
                return false;
            }
        }
        return $GLOBALS['PDOS'][$pdo_key];
    }
    
    public static function getKey($dsn, $username, $password, $options=array()) {
        return md5(serialize(array($dsn, $username, $password, $options)));
    }
    
    public static function rollBack($_pdo, $insert_stack, $table_name, $id_name) {
        foreach($insert_stack as $insert_id) {
            $query = "DELETE FROM $table_name WHERE $id_name = $insert_id";
            $result = $_pdo->query($query);
        }
    }
    
    public static function unsetPDO($dsn, $username, $password, $options=array()) {
        $pdo_key = self::getKey($dsn, $username, $password, $options);
        if(isset($GLOBALSS['PDOS'][$pdo_key])) {
            unset($GLOBALSS['PDOS'][$pdo_key]);
        }
    }
}

// 过滤app数据
function app_fliter($info, $log, $keyword) {
    $_pdo = PDOFactory::getPDO($log, DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    
    if($_pdo === false) {
        return false;
    }

    // 进行查询
    $query = "SELECT * FROM app_filter_ids WHERE ids = :id and media = :media";
    $staff_statement = $_pdo->prepare($query);
    $staff_statement->bindParam(':id', $info['id'], PDO::PARAM_STR);
    $staff_statement->bindParam(':media', $info['media'], PDO::PARAM_STR);
    $staff_statement->execute();
    $result = $staff_statement->fetch();
    
    $_pdo = null;
    PDOFactory::unsetPDO(DB_HOST, DB_NAME, DB_USER_NAME, DB_PASSWORD);
    if(empty($result)) {
        $log->INFO("ids={$info['id']}, media={$info['media']}的数据可以插入app_filter_ids");
        return true;
    }
    $log->WARN("ids={$info['id']}, media={$info['media']}的数据在app_filter_ids表中已存在");
    return false;    
}