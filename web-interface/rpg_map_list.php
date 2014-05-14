<?php
/*
 * ACOJ Web Interface
 * ./rpg_map_list.php
 * Version: 2014-05-14
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once('./header.php');
function show_operations(){
	global $page;
	echo
"<p align=\"center\">
	<a href=\"./rpg_map_list.php?p=".(0<=$page-1?$page-1:0)."\">Previous Page</a>
	| <a href=\"./rpg_map_list.php?p=".($page+1)."\">Next Page</a>
	| <a href=\"./rpg_map_insert.php\">Insert</a>
";
	echo
"</p>
";
}
$const_page_size=20;
$page=isset($_GET['p'])?$_GET['p']:0;
$id_first=$const_page_size*$page;
head();
show_head("Map List - ACOJ RPG");
show_menu();
show_operations();
echo
"$centerl_head
<table class=\"shadow\" width=\"840\">
	<caption><b>Map List</b></caption>
	<tr>
		<td><b>ID</b></td>
		<td><b>Name</b></td>
	</tr>
";
$res_maps=$mysqli->query("SELECT
		`id`,
		`name`
	       	FROM `rpg_maps`
		LIMIT $id_first,$const_page_size;");
while($map=$res_maps->fetch_assoc()){
	echo
"	<tr>
		<td>".htmlentities($map['id'])."</td>
		<td>".htmlentities($map['name'])."</td>
	</tr>
";
}
echo
"	</table>
$centerl_tail
";
show_operations();
show_tail();
tail();
?>
