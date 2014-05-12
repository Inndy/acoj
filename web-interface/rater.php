<?php
/*
 * ACOJ Web Interface
 * ./rater.php
 * Parameters: $_GET['id']
 * Permission required: none.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
require_once'./highlighter.php';
function show_operations(){
	global$rater;
	echo
"<p align=\"center\">
";
	if(isgroup(1))
		echo
"	<a href=\"./rater_update.php?id={$rater['id']}\"><font color=\"gray\">Update</font></a>
";
	echo
"</p>
";
}
head();
isset($_GET['id'])&&mysqli_single_select("
		SELECT COUNT(*)
		FROM `raters`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';")||exit;
$rater=mysqli_single_row_select("
		SELECT
		`id`,
		`timestamp_insert`,
		`name`,
		`general`,
		`interactive`,
		`source`,
		`description`
		FROM `raters`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';");
show_head('Rater - '.$configurations['name_website_logogram']);
show_menu();
show_operations();
echo
"$center_head
	ID: ".$rater['id']."<br>
	<br>
	Insert Time: ".$rater['timestamp_insert']."<br>
	<br>
	Name: ".htmlentities($rater['name'])."<br>
	<br>
	General: ".($rater['general']?"Yes":"No")."<br>
	<br>
	Interactive: ".($rater['interactive']?"Yes":"No")."<br>
	<br>
	Description:<br>
	".nl2br(htmlentities($rater['description']))."<br>
	<br>
	Source code:<br>
	".text_border(highlighter_cpp($rater['source']))."
$center_tail
";
show_operations();
show_tail();
tail();
?>
