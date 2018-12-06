<?php

/**
* mail.php 通过SMTP服务器发送邮件
*
* @version    v0.01
* @createtime 2016/11/26
* @updatetime 2016/12/03
* @author     yjl
* @copyright  Copyright (c) 微普科技 WiiPu Tech Inc. ( http://www.wiipu.com)
* 
*/

//header("content-type:text/html;charset=utf-8");//测试时需要输出中文，所以加上这个

require_once('smtp.class.php');
require_once('./config.php');
class Mail{
	//SMTP相关参数配置。置于WiiPHP中使用时，应当放在config.inc.php中
	//SMTP的配置由邮箱服务商提供。一般设置outlook收邮件填写的那些东西就是了。
	private $mysmtp_server      = 'ssl://smtp.126.com';
	private $mysmtp_port        = 465;
	private $mysmtp_auth        = true;
	private $mysmtp_account     = 'yuqing_tq';//邮箱账号
	private $mysmtp_pass        = 'Yuqing123';
	private $mailfrom    		= 'yuqing_tq@126.com';
	private $mailto         	= 'stone_movies@126.com';
	private $mailfrom_name   	= 'fengtian-yuqing_new';
	//SMTP配置结束*************

	function Mail($mail_title, $mail_content){
		
		//echo $mail_content;
		$flag = 0; //发邮件标志，1为需要发
		$content = ""; //备用变量，用于存储邮件内容
		//$mysmtp->debug = true;//打开调试，自动输出错误
		$filename = WARN_PATH.$mail_title.date("-Y_m_d").'.txt';
		if(!file_exists($filename)){//如果文件不存在，直接写
			//echo "不存在";
			file_put_contents($filename, $mail_content."*1*"."\r\n" );//初始次数为1,
		}else{      //文件存在，直接追加
			
			$str = file_get_contents($filename);
			$arr = explode('*', $str);//分割内容和数字
			$arr[1]++;
			$str2 = implode('*', $arr);//黏合字符串
			file_put_contents($filename, $str2);
		}
		//echo $content;
		
		/*定时发送邮件*/
		$mail_subject    = "fengtian".substr(time(), 6);	    //邮件标题
		//$time       	 = date("i");           //获取当前的分钟数
		//$sec             = date("s");
		$time = time();
		if($time % 2000 == 0 ){  //实现2000秒检查一次并发邮件

			$files = glob(WARN_PATH. '*.txt'); //获取所有文件
			foreach($files as $file) {
				$str = file_get_contents($file);
				$arr = explode('*', $str);//分割内容和数字
				//警戒线：
				if( $arr[1] > 600 ) //300以上错误报警
				{
					$flag = 1;
					unlink($file);//发送一次后删除日志，避免重复发送。
					$content = $content."<br/>".$str;
				}
			}
			if($flag == 1){
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
		



	}
}



?>
