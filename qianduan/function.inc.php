<?php

/**
 * function.inc.php  与业务有关的函数
 *
 * @version       v0.01
 * @create time   2014/9/1
 * @update time   
 * @author        jt
 * @copyright     Copyright (c) 芝麻开发 (http://www.zhimawork.com)
 */

/**
 * 获得执行程序的时间(秒)
 * 
 * @param $starttime 
 * @param $endtime
 *
 * @return
 */
function getRunTime($starttime = 0, $endtime = 0){
	global $PageStartTime;
	if(empty($starttime)){
		$starttime = $PageStartTime;
	}
	if(empty($endtime)){
		$PageEndTime = explode(' ',microtime());
		$PageEndTime = $PageEndTime[1] + $PageEndTime[0];
		$endtime = $PageEndTime;
	}
	
	$runtime = number_format(($endtime - $starttime), 3);
	return $runtime;
}
function getPageUrl(){
    $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
    $url .= $_SERVER['HTTP_HOST'];
    $url .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : urlencode($_SERVER['PHP_SELF']) . '?' . urlencode($_SERVER['QUERY_STRING']);
    return $url;
}

/**
 * 分页参数page传递后的处理
 * 
 * @param mixed $pagecount 页数
 * @return
 */
function getPage($pagecount){

	$page = empty($_GET['page']) ? 1 : trim($_GET['page']);
	if(!is_numeric($page)) $page = 1;
	if($page < 1) $page = 1;
    if(empty($pagecount)) 
        $page = 1;
	elseif($page > $pagecount) 
        $page = $pagecount;

	return $page;
}
/**
 * 分页显示 dspPages()--具体样式再通过CSS控制
 * 形如：
 * 1 2 3 × × × 98 99 100
 * 1 × × × 7 8 9 × × × 100
 *
 * @param $url       链接URL
 * @param $page      当前页数
 * @param $pagesize  页数
 * @param $rscount   记录总数
 * @param $pagecount 总页数
 * @return
 */
function getUrlExcludePage($url)
{
    $parsed_url = parse_url($url);
    if(!array_key_exists("query", $parsed_url)){
        return $url;
    }

    parse_str($parsed_url["query"], $query_array);
    if(array_key_exists("page", $query_array)) {
        unset($query_array["page"]);
    }
    $query_str = http_build_query($query_array);

    if(!empty($query_str))
    {
        $return_url = $parsed_url["path"]."?$query_str";

    }
    else
    {
        $return_url = $parsed_url["path"];
    }

    return $return_url;
}
function timediff($timediff){

    $days = intval($timediff/86400);
    $remain = $timediff%86400;
    $hours = intval($remain/3600);
    $remain = $remain%3600;
    $mins = intval($remain/60);
    $secs = $remain%60;
    $res = array("day" => $days,"hour" => $hours,"min" => $mins);
    return $days."天".$hours."时".$mins."分";
}
function timediff_min($timediff){

    $days = intval($timediff/86400);
    $remain = $timediff%86400;
    $hours = intval($remain/3600);
    $remain = $remain%3600;
    $mins = intval($remain/60);
    $secs = $remain%60;
    $res = array("day" => $days,"hour" => $hours,"min" => $mins);
    return $mins;
}
function timediff_day($timediff){

    $days = intval($timediff/86400);
    $remain = $timediff%86400;
    $hours = intval($remain/3600);
    $remain = $remain%3600;
    $mins = intval($remain/60);
    $secs = $remain%60;
    $res = array("day" => $days,"hour" => $hours,"min" => $mins);
    return $days;
}

function dspPage($url, $page, $pagesize, $rscount, $pagecount){

    //参数安全处理
    $url  = str_replace(array(">", "<"), array("&gt;", "&lt;"), $url);
    if(!is_numeric($page))       $page = 0;
    if(!is_numeric($pagesize))   $pagesize = 0;
    if(!is_numeric($rscount))    $rscount = 0;
    if(!is_numeric($pagecount))  $pagecount = 0;

    $url = getUrlExcludePage($url);
    //构建显示
    $temppage="";
    $temppage.=" <span class='sign'>|<</span>";

    if($page>1){
        $temppage.="&nbsp;<a href='".$url."?page=1'>首页</a>&nbsp;&nbsp;";
        $temppage.="<span class='sign'><</span>&nbsp;<a href='".$url."?page=".($page-1)."'>上一页</a>";

    }else{
        $temppage.="&nbsp;首页&nbsp;&nbsp;";
        $temppage.="<span class='sign'><</span>&nbsp;上一页";

    }


    if($page<=$pagecount-1){
        $temppage.="&nbsp;&nbsp;&nbsp;<a href='".$url."?page=".($page+1)."'>下一页</a>&nbsp;<span class='sign'> > </span>";
        $temppage.="<a href='".$url."?page=".$pagecount."' >尾页</a><span class='sign'> >|</span>";

    }else{
        $temppage.="&nbsp;&nbsp;&nbsp;下一页&nbsp;<span class='sign'> > </span>";
        $temppage.="尾页<span aria-hidden='true'> >|</span>";

    }



    if(!strpos($url, "?") === false)
        $temppage=str_replace("?page=", "&page=", $temppage);

    return $temppage;
}


function dspPages($url, $page, $pagesize, $rscount, $pagecount){

    //参数安全处理
    $url  = str_replace(array(">", "<"), array("&gt;", "&lt;"), $url);
    if(!is_numeric($page))       $page = 0;
    if(!is_numeric($pagesize))   $pagesize = 0;
    if(!is_numeric($rscount))    $rscount = 0;
    if(!is_numeric($pagecount))  $pagecount = 0;

    $url = getUrlExcludePage($url);


    $temppage="";
    $temppage.="<nav aria-label=\"Page navigation\" class=\"PageBox\"><ul class=\"pagination\">";

    if($page>1){
        $temppage.="<li><a href=\"".$url."?page=1\" aria-label=\"Previous\"><span aria-hidden=\"true\">首页</span></a></li>";
    }else{
        $temppage.="<li><a href=\"#\"><span aria-hidden=\"true\">首页</span></a></li>";
    }

    If($pagecount<9){

        for($p=1;$p<=$pagecount;$p++){
            if($p!=$page)
                $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
            else
                $temppage.=" <li class=\"active\"><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
        }
    }else{

        if($page<=3){
            for($p=1;$p<=5;$p++){
                if($p!=$page)
                    $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
                else
                    $temppage.="<li class=\"active\"><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
            }
            $temppage.=" <li><a href='#'>...</a></li>";
            for($p=$pagecount-3;$p<=$pagecount;$p++){
                if($p!=$page)
                    $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
                else
                    $temppage.="<li class=\"active\"><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
            }
        }else if($pagecount-$page<=3){

            for($p=1;$p<=3;$p++){
                $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
            }
            $temppage.="<li><a href='#'>...</a></li>";
            for($p=$pagecount-4;$p<=$pagecount;$p++){
                if($p!=$page){
                    $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
                }else{
                    $temppage.=" <li class=\"active\"><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
                }
            }
        }
        else{
            $temppage.=" <li><a href=\"".$url."?page=1\">1</a></li>";
            $temppage.=" <li><a href='#'>...</a></li>";
            for($p=$page-2;$p<=$page+2;$p++){
                if($p!=$page){
                    $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
                }else{
                    $temppage.=" <li class=\"active\"><a href=\"#\">".$p."</a></li>";

                }
            }
            $temppage.="<li><a href='#'>...</a></li>";
            $temppage.=" <li><a href=\"".$url."?page=".$pagecount."\">".$pagecount."</a></li>";
        }
    }

    if($page<=$pagecount-1){
        $temppage.="<li><a href=\"".$url."?page=".$pagecount."\" aria-label=\"Next\">末页</a></li>";
    }else{
        $temppage.="<li><a href=\"#\" aria-label=\"Next\">末页</a></li>";
    }

    $temppage .="</ul></nav>";


    if(!strpos($url, "?") === false)
        $temppage=str_replace("?page=", "&page=", $temppage);

    return $temppage;
}


/**
 * 民心网前端分页界面
 * @param $url
 * @param $page
 * @param $pagesize
 * @param $rscount
 * @param $pagecount
 * @return string
 */
function dspPagesForMin($url, $page, $pagesize, $rscount, $pagecount){

    //参数安全处理
    $url  = str_replace(array(">", "<"), array("&gt;", "&lt;"), $url);
    if(!is_numeric($page))       $page = 0;
    if(!is_numeric($pagesize))   $pagesize = 0;
    if(!is_numeric($rscount))    $rscount = 0;
    if(!is_numeric($pagecount))  $pagecount = 0;

    $url = getUrlExcludePage($url);


    $temppage="";
    $temppage.="<div class=\"pagination\">";

    if($page>1){
        $temppage.="<a href=\"".$url."?page=1\" >首页</span></a>";
    }else{
        $temppage.="<a href=\"#\" >首页</span></a>";
    }

    If($pagecount<9){

        for($p=1;$p<=$pagecount;$p++){
            if($p!=$page)
                $temppage.=" <a href=\"".$url."?page=".$p."\">".$p."</a>";
            else
                $temppage.=" <a href=\"#\"  class=\"hover\">".$p."</a>";
        }
    }else{

        if($page<=3){
            for($p=1;$p<=5;$p++){
                if($p!=$page)
                    $temppage.=" <a href=\"".$url."?page=".$p."\">".$p."</a>";
                else
                    $temppage.="<a href=\"#\" class=\"hover\">".$p."</a>";
            }
            $temppage.=" <span class=\"more\"></span>";
            for($p=$pagecount-3;$p<=$pagecount;$p++){
                if($p!=$page)
                    $temppage.=" <a href=\"".$url."?page=".$p."\">".$p."</a>";
                else
                    $temppage.="<a href=\"#\" class=\"hover\">".$p."</a>";
            }
        }else if($pagecount-$page<=3){

            for($p=1;$p<=3;$p++){
                $temppage.=" <a href=\"".$url."?page=".$p."\">".$p."</a>";
            }
            $temppage.="<span class=\"more\"></span>";
            for($p=$pagecount-4;$p<=$pagecount;$p++){
                if($p!=$page){
                    $temppage.=" <a href=\"".$url."?page=".$p."\">".$p."</a>";
                }else{
                    $temppage.=" <a href=\"#\" class=\"hover\">".$p."</a>";
                }
            }
        }
        else{

            $temppage.=" <a href=\"".$url."?page=1\">1</a>";
            $temppage.=" <span class=\"more\"></span>";
            for($p=$page-2;$p<=$page+2;$p++){
                if($p!=$page){
                    $temppage.=" <a href=\"".$url."?page=".$p."\">".$p."</a>";
                }else{
                    $temppage.=" <a href=\"#\" class=\"hover\">".$p."</a>";

                }
            }
            $temppage.="<span class=\"more\"></span>";
            $temppage.=" <a href=\"".$url."?page=".$pagecount."\">".$pagecount."</a>";
        }
    }

    if($page<=$pagecount-1){
        $temppage.="<a href=\"".$url."?page=".$pagecount."\" >末页</a>";
    }else{
        $temppage.="<a href=\"#\" >末页</a>";
    }

    $temppage .="</div>";


    if(!strpos($url, "?") === false)
        $temppage=str_replace("?page=", "&page=", $temppage);

    return $temppage;
}


/*
 * 研判分页
 * */

function dspPagesForyan($url, $page, $pagesize, $rscount, $pagecount){

    //参数安全处理
    $url  = str_replace(array(">", "<"), array("&gt;", "&lt;"), $url);
    if(!is_numeric($page))       $page = 0;
    if(!is_numeric($pagesize))   $pagesize = 0;
    if(!is_numeric($rscount))    $rscount = 0;
    if(!is_numeric($pagecount))  $pagecount = 0;

    $url = getUrlExcludePage($url);


    $temppage="";
    $temppage.="<nav aria-label=\"Page navigation\" class=\"pagination-box\"><ul class=\"num-box\">";

    if($page>1){
        $temppage.="<li><a href=\"".$url."?page=1\" aria-label=\"Previous\"><span aria-hidden=\"true\">首页</span></a></li>";
    }else{
        $temppage.="<li><a href=\"#\"><span aria-hidden=\"true\">首页</span></a></li>";
    }

    If($pagecount<9){

        for($p=1;$p<=$pagecount;$p++){
            if($p!=$page)
                $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
            else
                $temppage.=" <li class=\"active\"><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
        }
    }else{

        if($page<=3){
            for($p=1;$p<=5;$p++){
                if($p!=$page)
                    $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
                else
                    $temppage.="<li class=\"active\"><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
            }
            $temppage.=" <li><a href='#'>...</a></li>";
            for($p=$pagecount-3;$p<=$pagecount;$p++){
                if($p!=$page)
                    $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
                else
                    $temppage.="<li class=\"active\"><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
            }
        }else if($pagecount-$page<=3){

            for($p=1;$p<=3;$p++){
                $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
            }
            $temppage.="<li><a href='#'>...</a></li>";
            for($p=$pagecount-4;$p<=$pagecount;$p++){
                if($p!=$page){
                    $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
                }else{
                    $temppage.=" <li class=\"active\"><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
                }
            }
        }
        else{
            $temppage.=" <li><a href=\"".$url."?page=1\">1</a></li>";
            $temppage.=" <li><a href='#'>...</a></li>";
            for($p=$page-2;$p<=$page+2;$p++){
                if($p!=$page){
                    $temppage.=" <li><a href=\"".$url."?page=".$p."\">".$p."</a></li>";
                }else{
                    $temppage.=" <li class=\"active\"><a href=\"#\">".$p."</a></li>";

                }
            }
            $temppage.="<li><a href='#'>...</a></li>";
            $temppage.=" <li><a href=\"".$url."?page=".$pagecount."\">".$pagecount."</a></li>";
        }
    }

    if($page<=$pagecount-1){
        $temppage.="<li><a href=\"".$url."?page=".$pagecount."\" aria-label=\"Next\">末页</a></li>";
    }else{
        $temppage.="<li><a href=\"#\" aria-label=\"Next\">末页</a></li>";
    }

    $temppage .="</ul></nav>";


    if(!strpos($url, "?") === false)
        $temppage=str_replace("?page=", "&page=", $temppage);

    return $temppage;
}








?>





