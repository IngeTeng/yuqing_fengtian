<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Jiangting@WiiPu -- http://www.wiipu.com" />
	<link rel="stylesheet" href="style2.css" type="text/css" />
	<title>显示/隐藏左侧导航栏</title>
	<script language="JavaScript">
		function Submit_onclick(){
			if(window.top.document.getElementById('frams').cols == "205,7,*") {
				window.top.document.getElementById('frams').cols="0,7,*";
				document.getElementById("ImgArrow").src="images/switch_right.gif";
				document.getElementById("ImgArrow").alt="打开左侧导航栏";
			} else {
				window.top.document.getElementById('frams').cols="205,7,*";
				document.getElementById("ImgArrow").src="images/switch_left.gif";
				document.getElementById("ImgArrow").alt="隐藏左侧导航栏";
			}
		}

		function MyLoad() {
			if(window.parent.location.href.indexOf("MainUrl")>0) {
				window.top.midFrame.document.getElementById("ImgArrow").src="images/switch_right.gif";
			}
		}
	</script>
</head>
<body onload="MyLoad()">
	<div id="switchpic">
		<a href="javascript:Submit_onclick()"><img src="images/switch_left.gif" alt="隐藏左侧导航栏" id="ImgArrow" /></a>
	</div>
</body>
</html>
