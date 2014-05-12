<?php
/*
 * ACOJ Web Interface
 * ./homepage.php
 * Parameters: none.
 * Permission required: none.
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function show_body(){
	global$configurations,$center_head,$center_tail;
	echo
"$center_head
<br>
誠徵「系統測試員」與「題目管理員」，不須基礎，做事認真者佳，報名或詢問請 Email 至 anlialtting@gmail.com。<br>
<br>
<form action=\"./text_post.php\" method=\"post\">
	<textarea
		id=\"content\"
		name=\"content\"
		style=\"height:320px;width:800px;font-size:100%;\"
		wrap=\"off\"
		>Welcome to {$configurations['name_website']} !</textarea><br>
	<div id=\"interface\" style=\"text-align:center;\">
		Highlighter:
		<select name=\"brush\">
			<option value=\"0\">Plain</option>
			<option value=\"1\">C</option>
			<option value=\"2\">C++</option>
			<option value=\"4\">HTML</option>
			<option value=\"3\">JavaScript</option>
			<option value=\"5\">PHP</option>
		</select>
		<input type=\"submit\" value=\"Post\">
	</div>
</form>
<script>
var t=document.getElementById('content');
t.onkeydown=function(e){
	if(e.keyCode===9){
		var f=t.selectionStart,l=t.selectionEnd;
		t.value=t.value.substring(0,f)+'\t'+t.value.substring(l,t.value.length);
		t.selectionStart=t.selectionEnd=f+1;
		return false;
	}
};
t.onfocus=function(e){
	if($('#content').val()=='Welcome to Accepted Online Judge !'){
		$('#content').val('');
		$('#content').css({'height':'320px','padding-top':'2px','text-align':'left','font-size':'100%'});
		$('#interface').css({'visibility':'visible'});
	}
};
if($('#content').val()=='Welcome to ".$configurations['name_website']." !'){
	$('#content').css({'height':'290px','padding-top':'32px','text-align':'center','font-size':'200%'});
	$('#interface').css({'visibility':'hidden'});
}
</script>
$center_tail
";
}
head();
show_head($configurations['name_website']);
show_menu();
show_body();
show_tail();
tail();
?>
