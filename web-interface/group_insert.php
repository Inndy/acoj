<?php
/*
   acoj web shell
   ./group_insert.php
   parameters: none.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
function show_form(){
	global$center_head,$center_tail;
	show_head('Group Insert - '.$configurations['name_website_logogram']);
	show_menu();
	echo
"$center_head
<form method=\"post\">
	Name: <input type=\"text\" name=\"name\"><br>
	Introduction:<br>
	<textarea name=\"introduction\"></textarea><br>
	<input type=\"submit\"><br>
</form>
$center_tail
";
	show_tail();
}
function insert(){
	global$mysqli;
	$parameters=array('name','introduction');
	foreach($parameters as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	$mysqli->query("
		INSERT INTO `groups`
		(`name`,`introduction`)
		VALUE(
		'$name_e',
		'$introduction_e'
		);");
	if($mysqli->error){echo $mysqli->error;exit;}
	$id=mysqli_single_select("SELECT LAST_INSERT_ID();");
	header("location:./group.php?id=$id");
	exit(0);
}
if($_SERVER['REQUEST_METHOD']==='POST')
	insert();
else
	show_form();
tail();
?>
