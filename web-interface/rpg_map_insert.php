<?php
/*
 * ACOJ Web Interface
 * ./rpg_map_insert.php
 * Version: 2014-05-14
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
function show_form(){
	global$center_head,$center_tail;
	show_head('Map Insert - ACOJ RPG');
	show_menu();
	echo
"$center_head
<form method=\"post\">
	Name: <input type=\"text\" name=\"name\"><br>
	Description: <br>
	<textarea name=\"description\"></textarea><br>
	Max X: <input type=\"text\" name=\"max_x\" value=\"16\"><br>
	Max Y: <input type=\"text\" name=\"max_y\" value=\"24\"><br>
	<input type=\"submit\" value=\"Insert\"><br>
</form>
$center_tail
";
	show_tail();
	tail();
}
function insert(){
	global$mysqli;
	$parameters=array('name','description','max_x','max_y');
	foreach($parameters as $x){
		if(!isset($_POST[$x]))
			exit(0);
		$$x=$_POST[$x];
		${$x.'_e'}=$mysqli->real_escape_string($$x);
	}
	$mysqli->query("
			INSERT INTO `rpg_maps`
			(
			 `name`,
			 `description`,
			 `max_x`,
			 `max_y`
			 ) VALUE (
				 '$name_e',
				 '$description_e',
				 '$max_x_e',
				 '$max_y_e'
				 );");
	header("location:./rpg_map_list.php");
	exit(0);
}
if($_SERVER['REQUEST_METHOD']==='POST')
	insert();
else
	show_form();
?>
