<?php
/**
 * ��̨���ú�������ҵ���޹صĺ���
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) ΢�տƼ� WiiPu Tech Inc. (http://www.wiipu.com)
 * @informaition  
 */
//error_reporting(0);//���󱨸�
function getUrl(){
	$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
	return ($url);
}
function sqlReplace($str){
   $strResult = $str;
   if(!get_magic_quotes_gpc())
   {
     $strResult = addslashes($strResult);
   }
   return $strResult;
}
function HTMLEncode($str){
	if (!empty($str)){
		$str=str_replace("&","&amp;",$str);
		$str=str_replace(">","&gt;",$str);
		$str=str_replace("<","&lt;",$str);
		$str=str_replace(CHR(32),"&nbsp;",$str);
		$str=str_replace(CHR(9),"&nbsp;&nbsp;&nbsp;&nbsp;",$str);
		$str=str_replace(CHR(9),"&#160;&#160;&#160;&#160;",$str);
		$str=str_replace(CHR(34),"&quot;",$str);
		$str=str_replace(CHR(39),"&#39;",$str);
		$str=str_replace(CHR(13),"",$str);
		$str=str_replace(CHR(10),"<br/>",$str);
	}
	return $str;
}
Function HTMLDecode($str){
	if (!empty($str)){
		$str=str_replace("&amp;","&",$str);
		$str=str_replace("&gt;",">",$str);
		$str=str_replace("&lt;","<",$str);
		$str=str_replace("&nbsp;",CHR(32),$str);
		$str=str_replace("&nbsp;&nbsp;&nbsp;&nbsp;",CHR(9),$str);
		$str=str_replace("&#160;&#160;&#160;&#160;",CHR(9),$str);
		$str=str_replace("&quot;",CHR(34),$str);
		$str=str_replace("&#39;",CHR(39),$str);
		$str=str_replace("",CHR(13),$str);
		$str=str_replace("<br/>",CHR(10),$str);
		$str=str_replace("<br>",CHR(10),$str);
	}
	return $str;
}
function DateDiff($part, $begin, $end){
	$diff = strtotime($end) - strtotime($begin);
	switch($part){
		case "y": $retval = bcdiv($diff, (60 * 60 * 24 * 365)); break;
		case "m": $retval = bcdiv($diff, (60 * 60 * 24 * 30)); break;
		case "w": $retval = bcdiv($diff, (60 * 60 * 24 * 7)); break;
		case "d": $retval = bcdiv($diff, (60 * 60 * 24)); break;
		case "h": $retval = bcdiv($diff, (60 * 60)); break;
		case "n": $retval = bcdiv($diff, 60); break;
		case "s": $retval = $diff; break;
	}
	return $retval;
}
function alertInfo($info,$url,$type){
	switch($type){
		case 0:
			echo "<script language='javascript'>alert('".$info."');location.href='".$url."'</script>";
			exit();
			break;
		case 1:
			echo "<script language='javascript'>alert('".$info."');history.back(-1);</script>";
			exit();
			break;
	}
}
function checkData($data,$name,$type){
	switch($type){
		case 0:
			if(!preg_match('/^\d*$/',$data)){
				alertInfo("�Ƿ�����".$name,'',1);
			}
			break;
		case 1:
			if(empty($data)){
				alertInfo($name."����Ϊ��","",1);
			}
			break;
	}
	return $data;
}

function checkEmail($email,$name)
{
	if(empty($email))
	{
		alertInfo($name.'����Ϊ��','',1);
	}else if(!eregi("^[a-zA-Z0-9]([a-zA-Z0-9]*[-_.]?[a-zA-Z0-9]+)+@([a-zA-Z0-9]+\.)+[a-zA-Z]{2,}$", $email)) 
	{
		alertInfo($name.'�����ʽ����ȷ','',1);
	}

}
function cutstr($string, $length) {
	$charset="utf-8";
	if(strlen($string) <= $length) {
		return $string;
	}
	//$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
	$strcut = '';
	if(strtolower($charset) == 'utf-8') {
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t < 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);

	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}
	if(strlen($string) > strlen($strcut)) $strcut = $strcut.'...';
	return $strcut.'...';
}
function showPage($url,$page,$pagecount){
	$tempStr="";
	$spacer="?";
	if(strpos($url,"?")>-1) $spacer='&';
	$url.=$spacer;
	$tempStr="<a href='".$url."page=1'><img src='images/page_first.gif' alt='��ҳ' /></a>";
	if($page>1)
		$tempStr.=" <a href='".$url."page=".($page-1)."'><img src='images/page_back.gif' alt='��һҳ' /></a>";
	else
		$tempStr.=" <img src='images/page_back.gif' alt='��һҳ' />";
	if($page<$pagecount)
		$tempStr.=" <a href='".$url."page=".($page+1)."'><img src='images/page_next.gif' alt='��һҳ' /></a>";
	else
		$tempStr.=" <img src='images/page_next.gif' alt='��һҳ' />";
	$tempStr.=" <a href='".$url."page=".$pagecount."'><img src='images/page_last.gif' alt='ĩҳ' /></a>";
	$tempStr.=" ת����<input type='text' id='pageTo' size='3' style='width:26px;height:14px;' value='".$page."'/>ҳ<a href='javascript:location.href=\"".$url."page=\"+document.getElementById(\"pageTo\").value;'><img src='images/page_go.gif' alt='ת��' /></a>";
	return $tempStr;
}

/****************�Լ��ĺ���*******************/

	function addTag($key){
		$comm='';
		$id='';
		$key=str_replace("��",",",$key);
		$keyList=explode(",",$key);
		foreach ($keyList as $value){
			$sql="select tag_id from ".WIIDBPRE."tag where tag_name='".$value."'";
			$rs=mysql_query($sql);
			$rows=mysql_fetch_assoc($rs);
			if (!$rows){
				$sql_rr="insert into ".WIIDBPRE."tag (tag_name) values ('".$value."')";
				if (mysql_query($sql_rr)){
					$id.=$comm.mysql_insert_id();
				}
			}else{
				$id.=$comm.$rows['tag_id'];
			}
			$comm='|';
			
		}
		return $id;
	}

	function getTagByID($id){
		$sql_rr="select tag_name from ".WIIDBPRE."tag where tag_id=".$id;
		$rs_rr=mysql_query($sql_rr);
		$rows_rr=mysql_fetch_assoc($rs_rr);
		if ($rows_rr){
			return $rows_rr['tag_name'];
		}
	}
?>