<?php
/*
 * ACOJ Web Interface
   ./admin_configuration.php
 * parameters: none.
 * permission required: administrator.
 * Version: 2014-05-12
 * Author: An-Li Alt Ting
 * Email: anlialtting@gmail.com
 */
require_once'./header.php';
function update(){
	global$_POST,$mysqli;
	$res=$mysqli->query('SELECT * FROM `configurations`;');
	while($row=$res->fetch_assoc()){
		$id=$mysqli->real_escape_string($row['id']);
		$s=$mysqli->real_escape_string($_POST[$row['id']]);
		$query="
			UPDATE `configurations`
			SET `value`='$s'
			WHERE `id`='$id';";
		$mysqli->query($query);
	}
	header('location:./admin.php');
	exit(0);
}
function show_form(){
	global$mysqli,$border_head,$border_tail;
	echo
"$border_head
<form method=\"post\">
";
	$res=$mysqli->query('SELECT * FROM `configurations`;');
	while($row=$res->fetch_assoc())
		echo
"	{$row['id']}:<br>
	<textarea name=\"{$row['id']}\" rows=\"4\" style=\"width:100%;\">".
	htmlentities($row['value'])
	."</textarea><br>
";
	echo
"	<input type=\"submit\">
</form>
$border_tail
";
}
head();
if($_SERVER['REQUEST_METHOD']==='POST')
	update();
else{
	show_head('Configuration - Admin - ACOJ');
	show_menu();
	show_form();
	show_tail();
}
tail();
?>
