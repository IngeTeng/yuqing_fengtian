<?php
include_once ("lib.net.function.php");
function mdsLogin($host, $port)
{
	$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
	if (false === socket_connect($socket, $host, $port)) {
		echo 'Can not connect to Server [' . $host . ':' . $port . '].';
		return 0;
	}
	$loginXml = "<?xml version=\"1.0\" encoding=\"GBK\" ?>\r\n".
				"<LOGIN>\r\n".
				"<FIELD NAME=\"NAME\">Zhongnan_tieba</FIELD>\r\n".
				"<FIELD NAME=\"PASSWD\"></FIELD>\r\n".
				"<FIELD NAME=\"MODE\">ASYNC</FIELD>\r\n".
				"<FIELD NAME=\"ASYNC_HOST\">127.0.0.1</FIELD>\r\n".
				"<FIELD NAME=\"ASYNC_PORT\">3898</FIELD>\r\n".
				"</LOGIN>";
	$loginXml = "<?xml version=\"1.0\" encoding=\"GBK\" ?>\r\n".
				"<LOGIN>\r\n".
				"<FIELD NAME=\"NAME\">Zhongnan_tieba</FIELD>\r\n".
				"<FIELD NAME=\"PASSWD\"></FIELD>\r\n".
				"<FIELD NAME=\"MODE\">SYNC</FIELD>\r\n".
				"</LOGIN>";
	if (false === eget_send($socket, $loginXml, strlen($loginXml))) {
		echo 'Can not write login info to Server [' . $host . ':' . $port . '].';
		return 0;
	}
	
	
	
	$contentLength = 0;
	$head = '';
	if (false === eget_read_head($socket, $head, $contentLength)) {
		echo 'Can not read login info HEAD of MDS.';
		return 0;
	}

	$data = '';
	if (false === eget_read($socket, $data, $contentLength)) {
		echo 'Can not read login info BODY of MDS.';
		return 0;
	}
	
	
	//print_r($data);
	return $socket;
}

function getMdsContent($socket, $url, $page_html, $site_id)
{
	$bodyXml = 
		"<FILE>\r\n".
		"<FIELD NAME=\"Url\">".$url."</FIELD>\r\n".
		"<FIELD NAME=\"Con-Type\">HTM</FIELD>\r\n".
		"<FIELD NAME=\"Sender\">Zhongnan_tieba</FIELD>\r\n".
		"<FIELD NAME=\"Site-ID\">".$site_id."</FIELD>\r\n".
		"<FIELD NAME=\"URI\">". $url ."</FIELD>\r\n".
		"<FIELD NAME=\"TYPE\">HTM</FIELD>\r\n".
		"<FIELD NAME=\"LENGTH\">".strlen($page_html)."</FIELD>\r\n".
		"<FIELD NAME=\"TAG\">0</FIELD>\r\n".
		"</FILE>";
//	$bodyXml .= $page_html
//	echo($bodyXml);

	if (false === eget_send($socket, $bodyXml, strlen($bodyXml))) {
		echo 'Can not write file info to Server';
		return null;
	}
	if (false === @socket_write($socket, $page_html, strlen($page_html))) {
		echo 'Can not write file html to Server';
		return null;
	}
	$contentLength = 0;
	$head = '';
	
	if (false === eget_read_head($socket, $head, $contentLength)) {
		echo 'Can not read  info HEAD of MDS.';
		return null;
	}
//print_r($contentLength);
	//$contentLength = 10;
	$data = '';
	if (false === eget_read($socket, $data, $contentLength)) {
		echo 'Can not read  info BODY of MDS.';
		//return null;
	}
//	echo("<br>result:". $data);
	$xmlDoc = new DOMDocument();
	$xmlDoc->loadXML($data);
	$a = array();
	$f = $xmlDoc->getElementsByTagName("FIELD");
	for ($j = 0; $j < $f->length; $j++)
    {
            $attr = $f->item($j)->attributes->item(0)->nodeValue;
            $a[$attr] = iconv("utf8","gbk", $f->item($j)->textContent);
    }
 //   print_r($a);
	$data = '';
	if (false === eget_read($socket, $data, $a["TEXTLEN"])) {
		echo 'Can not read  info BODY TEXT of MDS.';
		//return null;
	}
//	echo("<br>content:". $data);
	$a["content"] = $data;

	return $a;
}

//atcLogin("127.0.0.1", 1708);

?>
