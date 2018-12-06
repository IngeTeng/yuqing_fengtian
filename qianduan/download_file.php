<?php
$download_filename = $_GET['filename'];
$file = trim(strrchr($download_filename, '/'), '/');//获取无路径的文件名

header('Content-Type: application/force-download; charset=utf8');
header("Content-Disposition: attachment;filename='".$file."'");
$download_filename = iconv( 'utf-8', 'gb2312', $download_filename);
readfile($download_filename);

exit;

?>

