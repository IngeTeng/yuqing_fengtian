<?php

/**
 * env.class.php 得到服务器端或客户端的环境参数
 *
 * @version       $Id$
 * @createtime    2016/7/27
 * @updatetime
 * @author        jt
 * @copyright     Copyright (c) 芝麻开发 (http://www.zhimawork.com)
 */

class Env {

    /**
     * 获得当前页面的URL地址
     */
    static public function getPageUrl(){
        $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
        $url .= $_SERVER['HTTP_HOST'];
        $url .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : urlencode($_SERVER['PHP_SELF']) . '?' . urlencode($_SERVER['QUERY_STRING']);
        return $url;
    }

    /**
     * 获取当前的操作系统是Linux还是Windows
     */
    static public function getOSType(){
        $ostype = (DIRECTORY_SEPARATOR == '\\') ? "windows" : 'linux';
        return $ostype;
    }

    /**
     * 获得访问者IP
     */
    static public function getIP(){
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "unknown";
        return $ip;
    }



}
?>