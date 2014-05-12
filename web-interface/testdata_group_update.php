<?php
/*
   acoj web shell
   ./testdata_group_update.php
   parameters: none.
   permissions: none.
 */
require_once'./header.php';
function post(){
	global$mysqli,$group;
	parameters_exist(array("score","name","is_example"))||exit;
	$mysqli->query("UPDATE `groups_testdata`
			SET
			`score`='".$mysqli->real_escape_string($_POST['score'])."',
			`name`='".$mysqli->real_escape_string($_POST['name'])."',
			`is_example`='".isset($_POST['is_example'])."'
			WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';");
	header("location:./testdata.php?id_problem=".$group['id_problem']);
}
function show_body(){
	global$mysqli,$center_head,$center_tail,$group;
	echo
"$center_head
<form method=\"post\">
	Score: <input type=\"text\" name=\"score\" value=\"".$group['score']."\"><br>
	<br>
	Name: <input type=\"text\" name=\"name\" value=\"".$group['name']."\"><br>
	<br>
	Is example: <input type=\"checkbox\" name=\"is_example\"".($group['is_example']?" checked":"")."><br>
	<br>
	<input type=\"submit\" value=\"Update\"><br>
</form>
$center_tail
";
}
head();
isset($_GET['id'])&&mysqli_single_select("
		SELECT COUNT(*)
		FROM `groups_testdata`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';")||exit;
$group=mysqli_single_row_select("
		SELECT `id`,`id_problem`,`score`,`name`,`is_example`
		FROM `groups_testdata`
		WHERE `id`='".$mysqli->real_escape_string($_GET['id'])."';");
if($_SERVER['REQUEST_METHOD']==='POST'){
	post();
}else{
	show_head('Testdata Group Update - '.$configurations['name_website_logogram']);
	show_menu();
	show_body();
	show_tail();
}
tail();
?>
