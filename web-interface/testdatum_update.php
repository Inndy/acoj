<?php
/*
 * ACOJ Web Interface
 * ./testdata_update.php
 * Permission required: administrator.
 * Version: 2014-05-11
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function testdatum_update(){
	global$mysqli,$testdatum;
	$mysqli->query("UPDATE `testdata` SET
			`name`='".$mysqli->real_escape_string($_POST['name'])."',
			`limit_time_ms`='".$mysqli->real_escape_string($_POST['limit_time_ms'])."',
			`limit_memory_byte`='".$mysqli->real_escape_string($_POST['limit_memory_byte'])."',
			`limit_stack_byte`='".$mysqli->real_escape_string($_POST['limit_stack_byte'])."',
			`description`='".$mysqli->real_escape_string($_POST['description'])."'
			WHERE `id`='{$testdatum['id']}';");
	header("location:./testdatum.php?id={$testdatum['id']}");
	exit;
}
function show_form(){
	global$center_head,$center_tail,$testdatum;
	echo
"$center_head
<form method=\"post\">
	ID: {$testdatum['id']}<br>
	<br>
	Name: <input name=\"name\" value=\"{$testdatum['name']}\"><br>
	<br>
	Time limit: <input name=\"limit_time_ms\" value=\"{$testdatum['limit_time_ms']}\"> ms<br>
	<br>
	Memory limit: <input name=\"limit_memory_byte\" value=\"{$testdatum['limit_memory_byte']}\"> Bytes<br>
	<br>
	Stack limit: <input name=\"limit_stack_byte\" value=\"{$testdatum['limit_stack_byte']}\"> Bytes<br>
	<br>
	Description:<br>
	<textarea name=\"description\" rows=\"24\" cols=\"80\">{$testdatum['description']}</textarea><br>
	<br>
	<input type=\"submit\" value=\"Update\"><br>
	<br>
</form>
$center_tail
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
		`name`,
		`limit_time_ms`,
		`limit_memory_byte`,
		`limit_stack_byte`,
		`description`
		FROM `testdata`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';");
if($_SERVER['REQUEST_METHOD']==='POST')
	testdatum_update();
else{
	show_head('Testdatum Update - '.$configurations['name_website_logogram']);
	show_menu();
	show_form();
	show_tail();
}
tail();
?>
