<?php
	require_once('inc_dbconn.php');
	$list = array();
	$sql="select * from qq_cookies where expries > ".time()." order by qc_id asc";
	$result=mysql_query($sql);
	while($rows=mysql_fetch_array($result))
	{
		$list[] = $rows["qq_cookie"];
	}
	mysql_free_result($result);
	echo(json_encode($list));
?>
