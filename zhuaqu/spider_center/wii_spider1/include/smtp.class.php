<?php

/**
* smtp.class.php 通过SMTP服务器发送邮件
*
* @version 0.01
* @createtime 
* @updatetime 
* @author 未知
* @copyright 来源于互联网
* @info 微普科技http://www.wiipu.com 2016-5-20重新整理
* 
*/

set_time_limit(120);

class smtp
{
	/* Public Variables */
	public $relay_host;
	public $smtp_port;
	public $time_out;
	public $host_name;
	public $debug;
	public $auth;
	public $user;
	public $pass;
	public $sender;
    public $sent   = TRUE;
	public $logs   = '';

	/* Private Variables */
	private $sock;

	/* Constractor 构造函数*/
	/**
	 * $relay_host  SMTP服务器地址，例如：smtp.163.com
	 * $smtp_port   SMTP服务端口
	 * $auth        SMTP是否需要授权
	 * $user        邮箱账号
	 * $pass        邮箱密码
	 * $sender      发送邮箱（$user经常和$sender一样，或是$sender中@的前面部分）
     * 
	**/
	function smtp($relay_host = "", $smtp_port = 465, $auth = false, $user, $pass, $sender)
	{   
		$this->debug        = FALSE;
		$this->smtp_port    = $smtp_port;
		$this->relay_host   = $relay_host;
		$this->time_out     = 30; //is used in fsockopen()
		$this->auth         = $auth;//auth
		$this->user         = $user;
		$this->pass         = $pass;
		$this->sender       = $sender;
		$this->host_name    = "localhost"; //is used in HELO command
		$this->logs         = ""; //记录跟服务器的交互过程
		$this->sock         = FALSE;
	}

	/* Main Function 发送邮件主函数*/
	/**
	 * $to          收件箱（多个Email用英文逗号隔开）
	 * $from        发件箱
	 * $fromName    发件人，比如：微普科技
	 * $subject     邮件主题
	 * $body        邮件内容
	 * $mailtype    是否HTML邮件。如果是，则值为HTML
	 * $cc          抄送人（多个Email用英文逗号隔开）
	 * $bcc         密送人（多个Email用英文逗号隔开）
	 * $additional_headers 
     * 
	**/
	function sendmail($to, $from, $fromName, $subject = "", $body = "", $mailtype = "HTML", $cc = "", $bcc = "", $additional_headers = ""){
		$mail_from = $this->get_address($this->strip_comment($from));

		$body = preg_replace("/(^|(\r\n))(\.)/", "\1.\3", $body);

		$header= "MIME-Version:1.0\r\n";
		if($mailtype=="HTML"){
			$header .= "Content-Type:text/html;charset=utf-8\r\n";
		}
		$header .= "To: ".$to."\r\n";
		if ($cc != "") {
			$header .= "Cc: ".$cc."\r\n";
		}

		$subject="=?UTF-8?B?".base64_encode($subject)."?=";

		$header .= "From: $fromName<".$from.">\r\n";
		$header .= "Subject: ".$subject."\r\n";
		$header .= $additional_headers;
		$header .= "Date: ".date("r")."\r\n";
		$header .= "X-Mailer:WiiPHPSMTP(PHP/".phpversion().")\r\n";

		list($msec, $sec) = explode(" ", microtime());
		$header .= "Message-ID: <".date("YmdHis", $sec).".".($msec*1000000).".".$mail_from.">\r\n";
		
		$TO = explode(",", $this->strip_comment($to));
		if ($cc != "") {
			$TO = array_merge($TO, explode(",", $this->strip_comment($cc)));
		}
		if ($bcc != "") {
			$TO = array_merge($TO, explode(",", $this->strip_comment($bcc)));
		}

		foreach ($TO as $rcpt_to) {
			$rcpt_to = $this->get_address($rcpt_to);
			if (!$this->smtp_sockopen($rcpt_to)) {
			   $this->setlog("Error: Cannot send email to ".$rcpt_to."\n");
               $this->sent = FALSE;
			   continue;
			}
			if ($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $header, $body)) {
			   $this->setlog("E-mail has been sent to <".$rcpt_to.">\n");
			} else {
			   $this->setlog("Error: Cannot send email to <".$rcpt_to.">\n");
               $this->sent = FALSE;
			}
			fclose($this->sock);
			$this->setlog("Disconnected from remote host\n");
		}
		return $this ->sent;
	}

	/* Private Functions */
	function smtp_send($helo, $from, $to, $header, $body = "")
	{
		if (!$this->smtp_putcmd("HELO", $helo)) {
			return $this->smtp_error("sending HELO command");
		}

		#auth
		if($this->auth){
			if (!$this->smtp_putcmd("AUTH LOGIN", base64_encode($this->user))) {
			   return $this->smtp_error("sending AUTH command");
			}
			if (!$this->smtp_putcmd("", base64_encode($this->pass))) {
			   return $this->smtp_error("sending AUTH command");
			}
		}
		#

		//if (!$this->smtp_putcmd("MAIL", "FROM:".$from."")) {
		if (!$this->smtp_putcmd("MAIL", "FROM:<".$this->sender.">")) {
			return $this->smtp_error("sending MAIL FROM command");
		}

		if (!$this->smtp_putcmd("RCPT", "TO:<".$to.">")) {
			return $this->smtp_error("sending RCPT TO command");
		}

		if (!$this->smtp_putcmd("DATA")) {
			return $this->smtp_error("sending DATA command");
		}

		if (!$this->smtp_message($header, $body)) {
			return $this->smtp_error("sending message");
		}

		if (!$this->smtp_eom()) {
			return $this->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
		}

		if (!$this->smtp_putcmd("QUIT")) {
			return $this->smtp_error("sending QUIT command");
		}
		return TRUE;
	}

	function smtp_sockopen($address)
	{
		if ($this->relay_host == "") {
		return $this->smtp_sockopen_mx($address);
		} else {
		return $this->smtp_sockopen_relay();
		}
	}

	function smtp_sockopen_relay()
	{
		$this->setlog("Trying to ".$this->relay_host.":".$this->smtp_port."\n");
		$this->sock = @fsockopen($this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out);
		if (!($this->sock && $this->smtp_ok())) {
			$this->setlog("Error: Cannot connenct to relay host ".$this->relay_host."\n");
			$this->setlog("Error: ".$errstr." (".$errno.")\n");
			return FALSE;
		}
		$this->setlog("Connected to relay host ".$this->relay_host."\n");
		return TRUE;
	}

	function smtp_sockopen_mx($address)
	{
		$domain = preg_replace("/^.+@([^@]+)$/", "\1", $address);
		if (!@getmxrr($domain, $MXHOSTS)) {
			$this->setlog("Error: Cannot resolve MX \"".$domain."\"\n");
			return FALSE;
		}

		foreach ($MXHOSTS as $host) {
			$this->setlog("Trying to ".$host.":".$this->smtp_port."\n");
			$this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out);
			if (!($this->sock && $this->smtp_ok())) {
			   $this->setlog("Warning: Cannot connect to mx host ".$host."\n");
			   $this->setlog("Error: ".$errstr." (".$errno.")\n");
			   continue;
			}
			$this->setlog("Connected to mx host ".$host."\n");
			return TRUE;
		}
		$this->setlog("Error: Cannot connect to any mx hosts (".implode(", ", $MXHOSTS).")\n");
		return FALSE;
	}

	function smtp_message($header, $body)
	{
		fputs($this->sock, $header."\r\n".$body);
		$this->smtp_debug("> ".str_replace("\r\n", "\n"."> ", $header."\n> ".$body."\n> "));
		return TRUE;
	}

	function smtp_eom()
	{
		fputs($this->sock, "\r\n.\r\n");
		$this->smtp_debug(". [EOM]\n");
		return $this->smtp_ok();
	}

	function smtp_ok()
	{
		$response = str_replace("\r\n", "", fgets($this->sock, 512));
		$this->smtp_debug($response."\n");
		if (!preg_match("/^[23]/", $response)) {
			fputs($this->sock, "QUIT\r\n");
			fgets($this->sock, 512);
			$this->setlog("Error: Remote host returned \"".$response."\"\n");
			return FALSE;
		}
		return TRUE;
	}

	function smtp_putcmd($cmd, $arg = "")
	{
		if ($arg != "") {
			if($cmd=="") $cmd = $arg;
			else $cmd = $cmd." ".$arg;
		}
		fputs($this->sock, $cmd."\r\n");
		$this->smtp_debug("> ".$cmd."\n");
		//echo "cmd=".$cmd."\r\n";
		return $this->smtp_ok();
	}

	function smtp_error($string)
	{
		$this->setlog("Error: Error occurred while ".$string.".\n");
		return FALSE;
	}

	function setlog($message)
	{
		
		$this->logs .= $message;
		$this->smtp_debug($message);
		/**不需要写日志2016/5/20
		if ($this->log_file == "") {
			return TRUE;
		}
		$message = date("M d H:i:s ").get_current_user()."[".getmypid()."]: ".$message;
		if (!@file_exists($this->log_file) || !($fp = @fopen($this->log_file, "a"))) {
			$this->smtp_debug("Warning: Cannot open log file \"".$this->log_file."\"\n");
			return FALSE;
		}
		flock($fp, LOCK_EX);
		fputs($fp, $message);
		fclose($fp);
		**/
		return TRUE;
	}


	function strip_comment($address)
	{
		$comment = "/\([^()]*\)/";
		while (preg_match($comment, $address)) {
			$address = preg_replace($comment, "", $address);
		}
		return $address;
	}


	function get_address($address)
	{
		$address = preg_replace("/([ \t\r\n])+/", "", $address);
		$address = preg_replace("/^.*<(.+)>.*$/", "\1", $address);
		return $address;
	}

	function smtp_debug($message)
	{
		if ($this->debug) {
			echo $message;
		}
	}
} // end class


?>