<?php
/*
 * ACOJ Web Interface
 * ./raters.php
 * Permission required: administrator
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
function show_operations(){
	echo
"<p align=\"center\">
";
	if(isgroup(1))
		echo
"	<a href=\"./rater_insert.php\"><font color=\"gray\">Insert</font></a>
";
	echo
"</p>
";
}
function show_body(){
	global$mysqli,$center_head,$center_tail;
	echo
"$center_head
<table class=\"shadow\">
	<caption><b>Raters</b></caption>
	<tr>
		<td><b>ID</b></td>
		<td><b>Name</b></td>
		<td><b>General</b></td>
		<td><b>Interactive</b></td>
";
	if(isgroup(1))
		echo
"		<td><b><font color=\"gray\">Operation</font></b></td>
";
	echo
"	</tr>
";
	$res=$mysqli->query('
		SELECT
		`id`,
		`name`,
		`general`,
		`interactive`
	       	FROM `raters`;');
	while($row=$res->fetch_assoc()){
		echo
"	<tr>
		<td>{$row['id']}</td>
		<td>".hlink_rater($row['id'])."</td>
		<td>".($row['general']?'Yes':'No')."</td>
		<td>".($row['interactive']?'Yes':'No')."</td>
";
		if(isgroup(1))
			echo
"		<td>
			<a href=\"./rater_update.php?id={$row['id']}\">
				Update
			</a>
		</td>
";
		echo
"	</tr>	
";
	}
	$res->free();
	echo
"</table>
$center_tail
";
}
show_head('Raters - '.$configurations['name_website_logogram']);
show_menu();
show_operations();
show_body();
show_operations();
show_tail();
tail();
?>
