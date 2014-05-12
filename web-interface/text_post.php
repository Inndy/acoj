<?php
/*
   acoj web shell
   ./text_post.php
 * Version: 2014-05-11
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
function show_form(){
	global $border_head,$border_tail;
	show_head('Text Post - '.$configurations['name_website_logogram']);
	show_menu();
	echo
"$border_head
<br>
<form method=\"post\">
	Highlight:
	<select name=\"brush\">
		<option value=\"0\">Plain</option>
		<option value=\"1\">C</option>
		<option value=\"2\">C++</option>
		<option value=\"3\">JavaScript</option>
	</select>
	<br>
	<br>
	<textarea id=\"content\" name=\"content\" rows=\"16\" style=\"width:100%;\" wrap=\"off\"></textarea><br>
	<br>
	<input type=\"submit\" value=\"Post\"><br>
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
</script>
$border_tail";
	show_tail();
}
function insert(){
	global $_SERVER,$mysqli,$data_user_current;
	if(!isset($_POST['brush'])||!isset($_POST['content']))
		exit(0);
	$brush_e=$mysqli->real_escape_string($_POST['brush']);
	$content_e=$mysqli->real_escape_string($_POST['content']);
	$mysqli->query("
			INSERT INTO `textposts`
			(`ipaddress`,`id_user`,`brush`,`content`)
			VALUE (
				'{$_SERVER['REMOTE_ADDR']}',
				'{$data_user_current['id']}',
				'$brush_e',
				'$content_e');");
	$id=mysqli_single_select("SELECT LAST_INSERT_ID();");
	header("location:./text.php?id=$id");
	exit(0);
}
if($_SERVER['REQUEST_METHOD']==='POST')
	insert();
else
	show_form();
tail();
?>
