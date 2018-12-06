<?php
function html_get($url, $cookie="", $referer="")
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.3 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    if ($cookie != "")
    {
    	$coo = "Cookie: " . $cookie;
    	$headers[] = $coo;
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($referer != "")
    {
        curl_setopt($ch,CURLOPT_REFERER,$referer);
    }
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
function getXmlValue($content, $start, $end)
{
	$pstart = strpos($content, $start);
        if ($pstart > 0 || $pstart === 0)
        {
                $pstart += strlen($start);
                $sub_content = substr($content, $pstart);
                $pend = strpos($sub_content, $end);
                if ($pend > 0 || $pend === 0)
                {
                        $a = substr($sub_content, 0, $pend);
                        return $a;
                }
        }
        return "";
	
}
function get_media($uri,&$media_list)
{
	$media_name="";
	for($i=0;$i<count($media_list);$i++){
		if ($media_list[$i]['domain'] == "")
		{
			continue;
		}
	    if(strstr($uri,$media_list[$i]['domain']) != ""){
		     $media_name=$media_list[$i]['media_name'];
		     return $media_name;
		} 
	}
    return $media_name;
}
?>