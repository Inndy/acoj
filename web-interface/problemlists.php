<?php
/*
   acoj web shell
   ./problemlists.php
   Permission required: user.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
if(!$loggedin)exit(0);
function show_operations(){
	echo
"<p style=\"text-align:center;\">
	<a href=\"./problemlist_insert.php\">Insert</a>
</p>
";
}
function show_body(){
	global$mysqli,$data_user_current,$center_head,$center_tail;
	echo
"$center_head
<table class=\"shadow\">
	<caption><b>Problemlists</b></caption>
	<tr>
		<td><b>ID</b></td>
		<td><b>Insert Timestamp</b></td>
		<td><b>Name</b></td>
	</tr>
";
	$res=$mysqli->query("
			SELECT *
			FROM `problemlists`
			WHERE `id_user_owner`='{$data_user_current['id']}';");
	while($row=$res->fetch_assoc()){
		echo
"	<tr>
		<td><a href=\"./problemlist.php?id=".htmlentities($row['id'])."\">".htmlentities($row['id'])."</a></td>
		<td>".htmlentities($row['timestamp_insert'])."</td>
		<td>".htmlentities($row['name'])."</td>
	</tr>
";
	}
	$res->free();
	echo
"</table>
$center_tail
";
}
show_head('Problemlists - ACOJ');
show_menu();
show_operations();
show_body();
show_operations();
show_tail();
tail();
?>
