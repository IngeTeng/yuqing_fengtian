<?php
include_once ("lib.mds.function.php");

function get_page_by_url($url_input, &$url_output, &$content)
{

	$rname = time().rand(0,10000);
	$filename = $rname.".html";
	$logname = $rname.".log"; 
	$cmd = "wget -O ".$filename. " -o ". $logname . " \"". $url_input . "\"";
	system($cmd . " >/dev/null");

	$logcontent = file_get_contents($logname);
//echo($logcontent);
	//preg_match_all('/Location: ([^\ ]+) /', $logcontent, $a);
	preg_match_all('/Î»ÖÃ£º([^\ ]+) /', $logcontent, $a);
	//print_r($a);
	if (count($a[1]) > 0)
	{
		$url_output = $a[1][count($a[1])-1];
	}
	else
	{
		$url_output = $url_input;
	}
	
	$content = file_get_contents($filename);
//echo($content);
	unlink($filename);
	unlink($logname);

}

function get_title_and_content_by($url, &$content)
{	
	$sock = mdsLogin("127.0.0.1", 1708);
	if ($sock > 0)
	{
		$a = getMdsContent($sock, $url, $content);
	}
	socket_close($sock);
	return $a;
}

function is_utf8($word)
{
	if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$word) == true)
	{
		return true;
	}
	else
	{
		return false;
	}
} // function is_utf8

function get_page_info($url)
{
	get_page_by_url($url, $url2, $c);
	if (is_utf8($c))
	{
		$c = iconv("utf8", "gbk", $c);
	}
	if (strlen($c) == 0)
	{
		return array();
	}
	$info = get_title_and_content_by($url2, $c);
	return $info;
}


//$url = "http://www.baidu.com/link?url=I2BfaSLHEiHBVm69FoCJ9N6AjFbyxRuI2HR3RpW5lr00O0WxXVn4I283J1T9kX22d5b4jiIxojQY4vZkbBq-MK";
//$url = "http://leaders.people.com.cn/n/2013/0722/c58278-22280430.html";
//get_page_by_url($url, $url2, $c);
//$a = (get_page_info($url));

?>