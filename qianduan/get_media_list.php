<?php
/**
 * Created by PhpStorm.
 * User: 张鑫
 * Date: 2018/10/18
 * Time: 23:52
 */
function getMedia($uri, $media_list)
{
    $media_name = '';
    for($i=0; $i<count($media_list); $i++){
        if(strstr($uri, $media_list[$i]['domain']) != ''){
            $media_name=$media_list[$i]['media_name'];
            break;
        }
    }
    return $media_name;
}