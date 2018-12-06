<?php

print_r($_POST);
?>
<html>
<body>
<form method="post">
<input name="11111" value="22222" type="text">
<div id="show_list">
</div>

<input type="submit">
</form>
</body>
</html>
<script>
var tr_div = document.createElement("tr");
//alert(tr_div);
var input = document.createElement("input");
  input.name="123123213";  
  input.value="232222";
  input.type="text";//设置类型为file  
// alert(input);
tr_div.appendChild(input);
//alert(tr_div);
var list = document.getElementById("show_list");
var str = '<input type="text" name="2222222" value="1111">';
//list.html(str);
list.appendChild(tr_div);
alert("OK");
</script>