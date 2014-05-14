<?php
/*
 * ACOJ Web Interface
 * ./competitions.php
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
head();
show_head('Competitions - '.$configurations['name_website_logogram']);
show_menu();
function show_operations(){
	echo
"<p align=\"center\">
";
	if(isgroup(1)){
		echo
"		<a href=\"./competition_insert.php\"><font color=\"gray\">Insert</font></a>
";
	}
	echo
"</p>
";
}
show_operations();
echo
"$center_head
";
echo
"<table class=\"shadow\" width=\"840\">
	<caption><b>Competitions</b></caption>
	<tr>
		<td>ID</td>
		<td>Name</td>
		<td>Start time</td>
		<td>End time</td>
	</tr>
";
$res=$mysqli->query('SELECT `id`,`name`,`time_first`,`time_last` FROM `competitions`;');
while($row=$res->fetch_assoc()){
	echo
"	<tr>
		<td>".$row['id']."</td>
		<td><a href=\"./competition.php?id=".$row['id']."\">".htmlentities($row['name'])."</a></td>
		<td>".htmlentities($row['time_first'])."</td>
		<td>".htmlentities($row['time_last'])."</td>
	</tr>
";
}
$res->free();
echo
"</table>
";
echo
"$center_tail
";
show_operations();
show_tail();
tail();
?>
