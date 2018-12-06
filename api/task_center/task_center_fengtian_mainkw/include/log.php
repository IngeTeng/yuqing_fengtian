<?php

/**
 * @filename log.php 
 * @encoding UTF-8 
 * @author WiiPu unknow 
 * @datetime 2016-6-3  17:23:18
 * @version 1.0
 * @Description
 * 
 */

class Log {
    /*
     * 单例模式log类 
     * 
     */
    private $handler = null;
    private $level = 15;
	
    private static $instance = null;
	
    private function __construct(){}

    private function __clone(){}
	
    public static function Init($handler = null,$level = 15) {
        if(!self::$instance instanceof self) {
            self::$instance = new self();
            self::$instance->__setHandle($handler);
            self::$instance->__setLevel($level);
        }
        return self::$instance;
    }
	
	
    private function __setHandle($handler){
        $this->handler = $handler;
    }
	
    private function __setLevel($level) {
        $this->level = $level;
    }
	
    public static function DEBUG($msg) {
        self::$instance->write(1, $msg);
    }
	
    public static function INFO($msg) {
        self::$instance->write(2, $msg);
    }
    
    public static function WARN($msg) {
        self::$instance->write(4, $msg);
    }
	
    public static function ERROR($msg) {
        $debugInfo = debug_backtrace();
        $stack = "[";
        foreach($debugInfo as $key => $val) {
            if(array_key_exists("file", $val)) {
                $stack .= ",file:" . $val["file"];
            }
            if(array_key_exists("line", $val)) {
                $stack .= ",line:" . $val["line"];
            }
            if(array_key_exists("function", $val)) {
                $stack .= ",function:" . $val["function"];
            }
        }
        $stack .= "]";
        self::$instance->write(8, $stack. $msg);
    }
	
    private function getLevelStr($level) {
        switch ($level) {
            case 1:
                return 'debug';
                break;
            case 2:
                return 'info';	
                break;
            case 4:
                return 'warn';
		break;
            case 8:
                return 'error';
		break;
            default:
        }
    }
	
    protected function write($level,$msg) {
    	if((filesize($this->handler)/pow(1024, 2)) > 10) {		// 大于10M删除
    		unlink($this->handler);
    	}
        if(($level & $this->level) == $level ) {
            $msg = '['.date('Y-m-d H:i:s').']['.$this->getLevelStr($level).'] '.$msg;
            file_put_contents($this->handler, $msg."\r\n", FILE_APPEND);
        }
    }
}