<?php
/*
 * ACOJ Web Interface
 * ./rpg_character_insert.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
function show_form(){
	global$center_head,$center_tail;
	show_head('Character Insert - ACOJ RPG');
	show_menu();
	echo
"$center_head
<form method=\"post\">
	Name: <input type=\"text\" name=\"name\"><br>
	Gender: <br>
	<input type=\"submit\" value=\"Insert\"><br>
</form>
$center_tail
";
	show_tail();
}
function insert(){
	global$mysqli,$data_user_current;
	$parameters=array('name');
	foreach($parameters as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	$mysqli->query("INSERT INTO `rpg_characters` (`id_user`,`name`)
			VALUE ('{$data_user_current['id']}','$name_e');");
	header("location:./rpg_character_list.php");
	exit(0);
}
if($_SERVER['REQUEST_METHOD']==='POST')
	insert();
else
	show_form();
tail();
?>
