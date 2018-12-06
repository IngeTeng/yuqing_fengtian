<?php

// 当前路径
define("BASE_PATH", str_replace("\\", "/", realpath(dirname(__FILE__))));

require('./smtp.class.php');


$log_paths = array(

	BASE_PATH. "/content_center_fengtian/log/", // 内容提取中心日志
	BASE_PATH. "/db_center_fengtian/log/",	// 数据库中心
	BASE_PATH. '/task_center_fengtian/log/',	// 任务中心
	BASE_PATH. '/task_center_fengtian/proxy/',	// 代理文件
);	
while(true) {
	$content = '';
	new Mail('test');
	break;
	foreach($log_paths as $log_path) {
		$log_path = $log_path.date('Y_m_d', time());
		if(is_dir($log_path) or is_file($log_path) ) {
	
			$last_time = filemtime($log_path);
			
			if(strstr($log_path, 'pinjin') && (time() - $last_time) > 12*60*60) {	//api日志12小时不更新，说明系统出现问题，品今数据少，用12个小时判断
				//echo date('Y-m-d H:i:s', $last_time)."\r\n";
				$content = $content. date(' Y-m-d H:i:s ', $last_time). $log_path . " 已经12个小时没更新了，请检查系统！</br>";
			}elseif ( (time() - $last_time) > 2*60*60 ) {//舆情2小时
				$content = $content. date(' Y-m-d H:i:s ', $last_time). $log_path . " 已经2个小时没更新了，请检查系统！</br>";
			}
			
		}
	}
	if(!empty($content)){
		new Mail($content);
		echo $content;
	}

	sleep(30*60);
}


class Mail{
	//SMTP相关参数配置。置于WiiPHP中使用时，应当放在config.inc.php中
	//SMTP的配置由邮箱服务商提供。一般设置outlook收邮件填写的那些东西就是了。
	private $mysmtp_server      = 'ssl://smtp.126.com';
	private $mysmtp_port        = 465;
	private $mysmtp_auth        = true;
	private $mysmtp_account     = 'yuqing_tq';//邮箱账号
	private $mysmtp_pass        = 'Yuqing123';
	private $mailfrom    		= 'yuqing_tq@126.com';
	private $mailto         	= 'yuqing_tq@163.com';
	private $mailfrom_name   	= 'API-yuqing';
	//SMTP配置结束*************

	function Mail($mail_content){
		
		
		/*定时发送邮件*/
		$mail_subject    = '系统有情况'.substr(time(), 6);	    //邮件标题
		$content = '请注意：'."<br/>".$mail_content;
		$time = time();

		$mail_body       = $content;   //邮件内容
		$mail_type       = 'HTML';
		$mysmtp = new smtp($this->mysmtp_server, $this->mysmtp_port, $this->mysmtp_auth, 
			                $this->mysmtp_account, $this->mysmtp_pass, $this->mailfrom);
		
		//开始发送
		$sent = $mysmtp->sendmail($this->mailto, $this->mailfrom, $this->mailfrom_name, 
			                       $mail_subject, $mail_body, $mail_type);
		if($sent === TRUE){		//发送成功
			echo 'Sent OK';
		}else{
			echo 'Failed';
			echo $mysmtp->logs;	//主动输出错误
		}
		
	}
}
?>