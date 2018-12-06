<?php
/**
 * 公用函数 function_common.php
 *
 * @version       v0.01
 * @create time   2011-5-16
 * @update time   
 * @author        jiangting
 * @copyright     Copyright (c) 微普科技 WiiPu Tech Inc. (http://www.wiipu.com)
 * @informaition  
 */
function sqlReplace($str){
   $strResult = $str;
   if(!get_magic_quotes_gpc())
   {
     $strResult = addslashes($strResult);
   }
   return strip_tags($strResult);
}
function sqlReplace2($str){
   $strResult = $str;
   if(!get_magic_quotes_gpc())
   {
     $strResult = addslashes($strResult);
   }
   return $strResult;
}
?>