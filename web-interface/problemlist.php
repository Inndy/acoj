<?php
/*
 * ACOJ Web Interface
 * ./problemlist.php
 * Parameters: $_GET['id']
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function show_operations(){
}
function show_body(){
	global$mysqli,$center_head,$center_tail,$id;
	$problemlist=mysqli_single_row_select("
			SELECT *
			FROM `problemlists`
			WHERE `id`='".$mysqli->real_escape_string($id)."';");
	echo
"<br>
$center_head
<table class=\"shadow\">
	<caption><b>".htmlentities($problemlist['name'])."</b></caption>
	<tr>
		<td>ID. Problem</td>
	</tr>
";
	echo
"</table>
$center_tail
";
}
if(isset($_GET['id']))
	$id=$_GET['id'];
else
	exit(0);
head();
show_head('Problemlist - '.$configurations['name_website_logogram']);
show_menu();
show_body();
show_tail();
tail();
?>
