<?php

/**
 * @filename function.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @datetime 2016-6-6  22:39:57
 * @version 1.0
 * @Description
 * 
 * 常用方法
 * 
 */


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

function getXmlValue($content, $start, $end) {
    $pstart = strpos($content, $start);
    if ($pstart > 0 || $pstart === 0) {
        $pstart += strlen($start);
        $sub_content = substr($content, $pstart);
        $pend = strpos($sub_content, $end);
        if ($pend > 0 || $pend === 0) {
                $a = substr($sub_content, 0, $pend);
                return $a;
        }
    }
    return "";
}

// socket处理函数
function tcpconnect(&$host, &$port) {
    $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($socket < 0) {
        return $socket;
    }
	
    $result = @socket_connect($socket, $host, $port);
    if ($result < 0) {
        @socket_close($socket);
        $socket = -1;
        return -1;
    }
    return $socket;
}

function tcpclose($socket) {
    @socket_close($socket);
}

function eget_set_timeout(&$socket, $seconds, $microseconds=0) {
    return stream_set_timeout($socket, $seconds, $microseconds);
}

function eget_write(&$socket, &$buf, &$buf_len) {
    return @socket_write($socket, $buf, $buf_len);
}

function eget_send(&$socket, &$data, $length) {
    $head = "HTTP/1.0 200 OK\r\nContent-Length: " . $length . "\r\n\r\n";
    $head_len = strlen($head);
    if (eget_write($socket, $head, $head_len) === false) {
        return false;
    }
    return eget_write($socket, $data, $length);
}

function eget_read_head(&$socket, &$head, &$contentLength) {
    $head = '';
    if (($buf = @socket_read($socket, 17, PHP_NORMAL_READ)) !== false) {
        $head .= $buf;
        if(strncmp($buf, "HTTP/1.0 200 OK", 15)) {
            $contentLength = 0;
            return false;
        }
    }
    while(($buf = @socket_read($socket, 512, PHP_NORMAL_READ)) !== false) {
        $head .= $buf;
        if(!strncasecmp($buf, "Content-Length", 14)) {
            $item = explode(":", $buf);
            if (count($item)>1) {
                $contentLength = (int)$item[1];
            } 
            else {
                return false;
            }
        } 
        elseif ($buf == "\r") {
            if (($buf = @socket_read($socket, 1, PHP_NORMAL_READ)) !== false) {
                $head .= $buf;
                if($buf == "\n") {
                    return true;
                }
            }
        }
    }
}

function eget_read(&$socket, &$data, $length) {
    $data = '';
    if (($buf = @socket_read($socket, $length, PHP_BINARY_READ)) !== false) {
        $data = $buf;
        return true;
    } 
    else {
        return false;
    }
}

// 连接语意中心
function article_property($host, $port, $title, $content="") {
    $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);		// 创建套接字
    if (false === socket_connect($socket, $host, $port)) {		// 连接
        echo 'Can not connect to Server [' . $host . ':' . $port . '].';
        return 0;
    }
    
    // 生成xml
    $body = "<title><![CDATA[".$title."]]></title>\r\n";
    $body .= "<content><![CDATA[".$content."]]></content>\r\n";
    if (false === eget_send($socket, $body, strlen($body))) {		// 发送xml
        echo 'Can not write body info to Server [' . $host . ':' . $port . '].';
        return 0;
    }
    
    $contentLength = 0;
    $head = '';
    if (false === eget_read_head($socket, $head, $contentLength)) {		// 接收返回头
        echo 'Can not read body info HEAD of Server.';
        return 0;
    }

    $data = '';
    if (false === eget_read($socket, $data, $contentLength)) {		// 接收返回体
        echo 'Can not read info BODY of Server.';
        return 0;
    }
    
    // 解析返回的xml并判断语意值
    $result = strstr($data, '<title');
    $pstart = strpos($result, '<title');
    $title_array = array();
    $i = 0;
    $property = 0;
    while ($pstart > 0 || $pstart === 0) {
        $pend = strpos($result, '</title>');
        $record = substr($result, $pstart, $pend - $pstart); 
        $property = getXmlValue($record, '<cid>', "</cid>");
        if($property == 2){  
            return $property;
        }
        $result = substr($result, $pend+ strlen('</title>'));
        $pstart = strpos($result, '<title');
        $title_array[$i] = $property;
        $i++;
    }
    if(count($title_array) > 0) { 
        $property = 1;
        return $property;
    }

    $result = strstr($data, '<content');
    $pstart = strpos($result, '<content');
    $content_array = array();
    $i = 0;
    while ($pstart > 0 || $pstart === 0) {
        $pend = strpos($result, '</content>');
        $record = substr($result, $pstart, $pend - $pstart); 
        $property = getXmlValue($record, '<cid>', "</cid>");
        if($property == 2){  
            return $property;
        }
        $result = substr($result, $pend+ strlen('</content>'));
        $pstart = strpos($result, '<content');
        $content_array[$i] = $property;
        $i++;
    }

    if(count($content_array) > 0) { 
        $property = 1;
        return $property;
    }
    return $property;
}

// 关键字查找
function article_keyword($host, $port, $title, $content, $print = 0) {
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if(socket_connect($socket, $host, $port) === false) {
        echo "Can not connect to Server [ .$host:$port ].";
    }
    
    $body = "<title><![CDATA[$title]]></title>\r\n";
    $body .= "<content><![CDATA[$content]]></content>\r\n";
    if (false === eget_send($socket, $body, strlen($body))) {
        echo "Can not write body info to Server [$host:$port].";
        return array();
    }
    if ($print == 1) {
	print_r($body);
    }
    $contentLength = 0;
    $head = '';

    if (false === eget_read_head($socket, $head, $contentLength)) {
        echo 'Can not read body info HEAD of Server.';
        return array();
    }

    $data = '';
    if (false === eget_read($socket, $data, $contentLength)) {
        echo 'Can not read info BODY of Server.';
        return array();
    }

    socket_close($socket);
    preg_match_all('/<word>([^<>]+)</', $data, $keywords);
    preg_match_all('/<kid>([^<>]+)</', $data, $kids);
    return array_unique($kids[1]);
}
