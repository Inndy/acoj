<?php
/*
 * ACOJ Web Interface
 * ./rpg_character_list.php
 * Parameters: none.
 * Permission required: loggedin.
 * Version: 2014-05-14
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function show_operations(){
	echo
"<p style=\"text-align:center;\">
	<a href=\"./rpg_character_insert.php\">Insert</a>
</p>
";
}
function show_body(){
	global$mysqli,$data_user_current,$center_head,$center_tail;
	echo
"$center_head
<table class=\"shadow\">
	<caption><b>Character List</b></caption>
	<tr>
		<td><b>ID</b></td>
		<td><b>Timestamp</b></td>
		<td><b>Name</b></td>
		<td><b>Gender</b></td>
	</tr>
";
	$res_characters=$mysqli->query("
			SELECT *
			FROM `rpg_characters`
			WHERE `id_user`='{$data_user_current['id']}';");
	while($character=$res_characters->fetch_assoc()){
		echo
"	<tr>
		<td>{$character['id']}</td>
		<td>{$character['timestamp_insert']}</td>
		<td><a href=\"./rpg_map.php?cid={$character['id']}&id=1\">{$character['name']}</a></td>
		<td>{$character['gender']}</td>
	</tr>
";
	}
	echo
"</table>
$center_tail
";
}
head();
if(!$loggedin)
	exit;
show_head('Character List - ACOJ RPG');
show_menu();
show_operations();
show_body();
show_operations();
show_tail();
tail();
?>
