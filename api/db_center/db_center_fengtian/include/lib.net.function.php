<?php

function tcpconnect(&$host, &$port) {
	$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket < 0) {
		#echo "socket创建失败原因: ".socket_strerror($socket)."\n";
		return $socket;
	}
	
	$result = @socket_connect($socket, $host, $port);
	if ($result < 0) {
		#echo "SOCKET连接失败原因: ($result) " . socket_strerror($result) . "\n";
		@socket_close($socket);
		$socket = -1;
		return -1;
	}
	return $socket;
}

function tcpclose($socket) {
	@socket_close($socket);
}

function eget_set_timeout(&$socket, $seconds, $microseconds=0){
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
			} else {
				return false;
			}
		} elseif ($buf == "\r") {
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
	} else {
		return false;
	}
}

function eget_read_2(&$socket) {
	while(($buf = @socket_read($socket, 512, PHP_NORMAL_READ)) !== false) {
		//print_r($buf);
		//sleep(1);
	}
}

