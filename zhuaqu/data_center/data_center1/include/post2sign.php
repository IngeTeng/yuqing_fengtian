<?php

/**
 * @filename GetSign.php 
 * @encoding UTF-8 
 * @author CzRzChao 
 * @createtime 2016-11-29  17:36:31
 * @updatetime 2016-11-29  17:36:31
 * @version 1.0
 * @Description
 * 获取sign
 */

class Post2Sign {
    
    /**
     * 传入数组和秘钥获取sign值
     * @param array $array
     * @param string $secret
     * @return string
     */
    public static function getSign($array, $secret)
    {
        $str = '';
        ksort($array, SORT_STRING);
        $str = self::_array2String($array);
        $str .= sprintf('&secret=%s', $secret);
        return md5(urlencode($str));
    }
    /**
     * 把数组转换为加密字符串
     * @param array $array
     * @return string
     */
    private static function _array2String($array)
    {
        $str = '';
        if (!is_array($array)) {
            return (string)$array;
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $str .= $key. '=[' . self::_array2String($value). ']&';
            } else {
                if (is_int($key)) {
                    $str .= sprintf('%s&', $value);
                } else {
                    $str .= sprintf('%s=%s&', $key, $value);
                }
            }
        }
        return rtrim($str, '&');
    }
}