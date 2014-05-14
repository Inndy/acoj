<?php
/*
 * ACOJ Web Interface
 * ./testdatum.php
 * Parameters: $_GET['id']
 * Permission required: administrator
 * Version: 2014-05-11
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
require_once'./highlighter.php';
function show_operations(){
	global$testdatum;
	echo
"<p align=\"center\">
";
	if(isgroup(1))
		echo
"	<a href=\"./testdatum_update.php?id=".$testdatum['id']."\"><font color=\"gray\">Update</font></a>
";
	echo
"</p>
";
}
function show_body(){
	global$border_head,$border_tail,$testdatum;
	echo
"$border_head
<p>
	ID: {$testdatum['id']}<br>
	<br>
	Insert Time: {$testdatum['timestamp_insert']}<br>
	<br>
	Insert User: ".hlink_user($testdatum['id_user_insert'])."<br>
	<br>
	Problem: ".hlink_problem($testdatum['problem'])."<br>
	<br>
	Name: ".($testdatum['name']===''?'No Name':$testdatum['name'])."<br>
	<br>
	Time limit: {$testdatum['limit_time_ms']} ms<br>
	<br>
	Memory limit: {$testdatum['limit_memory_byte']} Bytes = ".($testdatum['limit_memory_byte']/1024)." KiB<br>
	<br>
	Stack limit: ".$testdatum['limit_stack_byte']."Bytes = ".($testdatum['limit_stack_byte']/1024)." KiB<br>
	<br>
</p>
<h4>Description</h4>
<p>".nl2br(htmlentities($testdatum['description']))."</p>
<h4>Input (Prefix 1 KiB)</h4>
".text_border(htmlentities($testdatum['SUBSTRING(`input`,1,1024)']))."
<h4>Output (Prefix 1 KiB)</h4>
".text_border(htmlentities($testdatum['SUBSTRING(`output`,1,1024)']))."
$border_tail
";
}
head();
isset($_GET['id'])&&mysqli_single_select("
		SELECT COUNT(*)
		FROM `testdata`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';")||exit;
$testdatum=mysqli_single_row_select("
		SELECT
		`id`,
		`timestamp_insert`,
		`id_user_insert`,
		`problem`,
		`name`,
		`limit_time_ms`,
		`limit_memory_byte`,
		`limit_stack_byte`,
		`description`,
		SUBSTRING(`input`,1,1024),
		SUBSTRING(`output`,1,1024)
		FROM `testdata`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';");
show_head('Testdatum - '.$configurations['name_website_logogram']);
show_menu();
show_operations();
show_body();
show_operations();
show_tail();
tail();
?>
