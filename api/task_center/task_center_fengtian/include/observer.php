<?php

/**
 * @filename observer.php 
 * @encoding UTF-8 
 * @author WiiPu CzRzChao 
 * @datetime 2016-6-3  14:38:14
 * @version 1.0
 * @Description
 * 观察者模式,监控返回状态变化
 */


class CInterfaceSubject implements SplSubject{
    protected $observers;
    protected $status;
    
    public function __construct() {
        $this->observers = array();
    }
    
    public function attach(SplObserver $observer) {
        $this->observers[] = $observer;
    }
    
    public function detach(SplObserver $observer) {
        if($index = array_search($observer, $this->observers, true)) {
            unset($this->observers[$index]);
        }
    }
    
    public function notify() {
        foreach($this->observers as $obserever) {
            $obserever->update($this);
        }
    }
    
    public function setStatus($status) {
        $this->status = $status;
        $this->notify();
    }
    
    public function getStatus() {
        return $this->status;
    }
}

class CInterfaceObserver implements SplObserver {
    protected $response;
    
    public function update(SplSubject $subject) {
        $this->response['status'] =  $subject->getStatus();
        echo json_encode($this->response);
        exit("");
    }
    
    public function setResponse($response) {
        $this->response = $response;
    }
}

